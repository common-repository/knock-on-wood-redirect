<?php
/**
 * TODO:
 * * Add settings endpoints
 * * Add export endpoints
 */
namespace KOW_Redirect;

require_once(KOW_REDIRECT_ROOT.'/includes/helper.php');

class Rest extends Helper
{
    const NAMESPACE = 'kow_redirect/v1';
    
    /**
     * redirect types allowed.
     * Note: 301 & 302 are best practices
     */
    const REDIRECT_TYPES = [
        301=>'301 - Moved Permanently',
        302=>'302 - Moved Temporarily',
        303=>'303 - Moved Temporarily, Drop Request Data',
        307=>'307 - Moved Temporarily, STRICT',
        308=>'308 - Moved Permanently, STRICT'
    ];

    public function __construct()
    {
		parent::__construct();

        register_rest_route(
            self::NAMESPACE,
            '/heartbeat',
            [
                'methods' => 'GET',
                'callback' => [$this, 'heartbeat'],
            ]
        );
        register_rest_route(
            self::NAMESPACE,
            '/get_redirects',
            [
                'methods' => 'POST',
                'callback' => [$this, 'get_redirects'],
            ]
        );
        register_rest_route(
            self::NAMESPACE,
            '/get_redirect',
            [
                'methods' => 'POST',
                'callback' => [$this, 'get_redirect'],
            ]
        );
        register_rest_route(
            self::NAMESPACE,
            '/set_redirect',
            [
                'methods' => 'POST',
                'callback' => [$this, 'set_redirect'],
            ]
        );
        register_rest_route(
            self::NAMESPACE,
            '/remove_redirect',
            [
                'methods' => 'POST',
                'callback' => [$this, 'remove_redirect'],
            ]
        );
        register_rest_route(
            self::NAMESPACE,
            '/set_disabled',
            [
                'methods' => 'POST',
                'callback' => [$this, 'set_disabled'],
            ]
        );
        register_rest_route(
            self::NAMESPACE,
            '/do_database_upgrade',
            [
                'methods' => 'POST',
                'callback' => [$this, 'do_database_upgrade'],
            ]
        );
        register_rest_route(
            self::NAMESPACE,
            '/skip_database_upgrade',
            [
                'methods' => 'POST',
                'callback' => [$this, 'skip_database_upgrade'],
            ]
        );
        register_rest_route(
            self::NAMESPACE,
            '/settings',
            [
				'methods' => 'GET',
				'callback' => [$this, 'get_settings']
			]
		);
		register_rest_route(
            self::NAMESPACE,
            '/settings',
            [
				'methods' => 'POST',
				'callback' => [$this, 'post_settings']
			]
        );
    }
    
    /**
     * @param WP_REST_Request $request
     */
    public function get_settings($request)
    {
        if (!current_user_can('manage_options')) {
            return new \WP_Error('forbidden', '');
		}
		$settings = [];
		foreach ($this->settings as $key => $value) {
			$settings[$key] = [
				'type' => \gettype($value),
				'value' => $value
			];
		}
        return $settings;
	}

	/**
     * @param WP_REST_Request $request
     */
    public function post_settings($request)
    {
        if (!current_user_can('manage_options')) {
            return new \WP_Error('forbidden', '');
		}
		$settings = $request->get_param('settings') ?? [];
		foreach ($settings as $key => $value) {
			$this->settings[$key] = $value;
		}
		if ($this->saveUserSettings() != true) {
			return new \WP_Error('redirect_settings_save_failier', '');
		} else {
			$settings = [];
			foreach ($this->settings as $key => $value) {
				$settings[$key] = [
					'type' => \gettype($value),
					'value' => $value
				];
			}
        	return $settings;
		}
    }

    /**
     * @param WP_REST_Request $request
     */
    public function heartbeat()
    {
        if (\get_current_user_id() != 0) {
            return ['nonce'=>\wp_create_nonce('wp_rest')];
        } else {
            return new \WP_Error('rest_not_authenticated', "You are not logged in!");
        }
    }

    /**
     ** The user has decided to start the DB upgrade process
     * @param WP_REST_Request $request
     */
    public function do_database_upgrade($request)
    {
        if (!current_user_can('activate_plugins')) {
            return new \WP_Error('forbidden', '');
        }
        require_once(KOW_REDIRECT_ROOT.'/includes/update.php');
        $updater = new Updates();
        return $updater->update();
    }

    /**
     ** The user has decided to skip the DB upgrade process
     * @param WP_REST_Request $request
     */
    public function skip_database_upgrade($request)
    {
        if (!current_user_can('activate_plugins')) {
            return new \WP_Error('forbidden', '');
        }
        require_once(KOW_REDIRECT_ROOT.'/includes/update.php');
        $updater = new Updates();
        $updater->version = \end($updater->versions);
        return $updater->update();
    }

    /**
     * @param WP_REST_Request $request
     */
    public function get_redirects($request)
    {
        if (!current_user_can('read_private_posts')) {
            return new \WP_Error('forbidden', '');
        }
        $page = $request->get_param('page') ?? 0;
        $page_size = $request->get_param('page_size') ?? 10;
        $q = '%'.($request->get_param('query') ?? "|").'%';
        $offset = $page_size * $page;
        
        $sortby = $request->get_param('sortby') ?? 'id';
        if (!in_array($sortby, ['id','from_url','to_url','redir_type', 'hits','is_disabled', 'created', 'last_hit'])) {
            return new \WP_Error('invalid_sortby_given', '');
        }
        $sortdir = $request->get_param('sortdir') ?? 'asc';
        if (!in_array($sortdir, ['asc', 'desc'])) {
            return new \WP_Error('invalid_sortdir_given', '');
        }

        $query = "SELECT * FROM 
			(
				SELECT a.*, COUNT(b.p_id) as hits, MAX(b.created) as last_hit, CONCAT(from_url, '|', to_url, '|', redir_type) q 
				FROM `#prefix_points` a
				LEFT JOIN `#prefix_clicks` b ON a.id = b.p_id
				GROUP BY a.id
			) c
			WHERE q LIKE '%s' 
			ORDER BY $sortby $sortdir
			LIMIT %d, %d";
        $query_count = "SELECT COUNT(*) FROM
			(
				SELECT CONCAT(from_url, '|', to_url, '|', redir_type) q 
				FROM `#prefix_points` a
			) c
			WHERE q LIKE '%s'";

        $results = $this->gettable($query, [$q, $offset, $page_size]);
        $count = $this->getvar($query_count, [$q]);
        
        return [
            'results' => $results,
            'count' => $count
        ];
    }
    
    /**
     * @param WP_REST_Request $request
     */
    public function get_redirect($request)
    {
        if (!current_user_can('read_private_posts')) {
            return new \WP_Error('forbidden', '');
        }
        $id = \intval($request->get_param('id'));
        
        return $this->_get_refirect($id);
    }

    public function _get_refirect($id)
    {
        if ($id == 0) {
            return new \WP_Error('id_invalid', '');
        }

        $results = $this->gettable("SELECT * FROM `#prefix_points` WHERE id = '%s'", [$id]);
        if (\count($results) > 0) {
            return $results[0];
        } else {
            return new \WP_Error('not_found', '');
        }
    }

    /**
     * @param WP_REST_Request $request
     */
    public function set_redirect($request)
    {
        if (!current_user_can('edit_private_posts')) {
            return new \WP_Error('forbidden', '');
        }
        //* get params
        $id = \intval($request->get_param('id'));
        $to_path = \trim($request->get_param('to_url') ?? '');
        $from_path = \trim($request->get_param('from_url') ?? '');
        $redir_type = \intval($request->get_param('redir_type'));
        $overridden = \intval($request->get_param('overridden')) == 1;
        
        if (!isset(self::REDIRECT_TYPES[$redir_type])) {
            return new \WP_Error('redir_type_invalid', '', $redir_type);
        }

        //* clean url paths
        $from_path_parts = \explode('?', $from_path, 2);
        if (\count($from_path_parts) > 1 && $from_path_parts[1] != '') {
            $from_path_parts[0] = \rtrim($from_path_parts[0], '/');
            if ($from_path_parts[0] == '') {
                $from_path_parts[0] = '/';
            }
            $from_path = $from_path_parts[0] . '?' . $from_path_parts[1];
        } else {
            $from_path = \rtrim($from_path, '/?');
        }

        if ($this->validate_url_path($to_path) != true) {
            return new \WP_Error('to_path_invalid', '');
        }
        if ($this->validate_url_path($from_path) != true) {
            return new \WP_Error('from_path_invalid', '');
        }
        
        if ($to_path == $from_path) {
            return new \WP_Error('redirect_loop_detected', '');
        }

        //* detect chains
        $chains = $this->gettable("SELECT * FROM `#prefix_points` WHERE `id` != '%s' AND ( `to_url` LIKE '%s' OR `from_url` LIKE '%s' )", [$id, $from_path, $to_path]);
        if (\count($chains) > 0) {
            return new \WP_Error('redirect_chains_detected', '', $chains);
        }

        //* detect duplicates
        $duplicates = $this->gettable("SELECT * FROM `#prefix_points` WHERE `id` != '%s' AND `from_url` LIKE '%s'", [$id, $from_path]);
        if (\count($duplicates) > 0) {
            return new \WP_Error('redirect_duplicates_detected', '', $duplicates);
        }

        $to_path = \esc_url_raw($to_path);
        $from_path = \esc_url_raw($from_path);

        //* push to DB
        if ($id == 0) {
            //* add
            return $this->add_redirect($to_path, $from_path, $redir_type, $overridden?1:0);
        } else {
            //* edit
            return $this->edit_redirect($id, [
                'to_url' => $to_path,
                'from_url' => $from_path,
                'redir_type' => $redir_type
            ]);
        }
    }

    /**
     * @param WP_REST_Request $request
     */
    public function set_disabled($request)
    {
        if (!current_user_can('edit_private_posts')) {
            return new \WP_Error('forbidden', '');
        }
        $id = \intval($request->get_param('id'));
        $is_disabled = \intval($request->get_param('is_disabled'));

        if ($is_disabled > 1) {
            return new \WP_Error('is_disabled_invalid', '');
        }

        if ($id == 0) {
            return new \WP_Error('id_invalid', '');
        }

        $this->edit_redirect($id, ['is_disabled'=>$is_disabled]);
        return $this->_get_refirect($id);
    }

    /**
     * @param WP_REST_Request $request
     */
    public function remove_redirect($request)
    {
        if (!current_user_can('delete_private_posts')) {
            return new \WP_Error('forbidden', '');
        }
        $id = \intval($request->get_param('id'));
        
        if ($id == 0) {
            return new \WP_Error('id_invalid', '');
        }

        $this->getnull("DELETE FROM `#prefix_points` WHERE `id` = '%s'", [$id]);
    }

    public function validate_url_path($path)
    {
        if (substr($path, 0, 1) != '/') {
            return false;
        } else {
            return filter_var(\get_site_url(null, $path), FILTER_VALIDATE_URL);
        }
    }

    public function add_redirect($to, $from, $redir_type, $overridden)
    {
        $id = $this->insert("INSERT INTO `#prefix_points` (to_url, from_url, redir_type, overridden) VALUES ('%s','%s','%s','%s')", [$to, $from, $redir_type, $overridden]);
        return $this->_get_refirect($id);
    }

    public function edit_redirect($id, $changes)
    {
        $query = "UPDATE `#prefix_points` SET";
        $args = [];
        foreach ($changes as $key => $value) {
            if (\count($args) > 0) {
                $query .= ',';
            }
            $query .= ' '.$key."='%s'";
            $args[] = $value;
        }

        $query .= " WHERE id='%s'";
        $args[] = $id;

        $this->getnull($query, $args);

        return $this->_get_refirect($id);
    }
}
