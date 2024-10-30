<?php
namespace KOW_Redirect;

require_once(KOW_REDIRECT_ROOT.'/includes/helper.php');

class Updates extends Helper
{
    public $version = '';
    public $versions = ['1.0.0', '2.0.0'];

    public function __construct()
    {
		parent::__construct();
		
        //* get current version
        $this->get_current_version();

        //* check if version is set
        if ($this->version == '') {
			//add_filter($this->prefix.'app_localisation', [$this, 'flag_new_install_in_localisation'], 10, 1);
			$this->version = $this->versions[0];
        }
        add_filter($this->prefix.'app_localisation', [$this, 'localise_version'], 10, 1);

        //* check if upgrade available
        if (\method_exists(...$this->getUpgradeFunk())) {
            //* set flag to require upgrade
            add_filter($this->prefix.'app_localisation', [$this, 'flag_upgrade_in_localisation'], 10, 1);
        }
    }

    private function getUpgradeFunk()
    {
        $f_version =  \str_replace('.', '_', $this->version);
        return [$this, 'upgrade_from_'.$f_version];
    }

    public function localise_version($localisation)
    {
        $localisation['version'] = $this->version;
        return $localisation;
    }

    public function flag_upgrade_in_localisation($localisation)
    {
        $localisation['force_page'] = 'Upgrade';
        return $localisation;
    }
    
    public function flag_new_install_in_localisation($localisation)
    {
        $localisation['force_page'] = 'Install';
        $localisation['versions'] = $this->versions;
        return $localisation;
    }

    private function get_current_version()
    {
        $this->version = $this->cache_get('db_ver', -1);
    }

    private function write_current_version()
    {
        $this->cache_set('db_ver', $this->version);
    }

    public function update()
    {
		$_version = $this->version;
		$first_upgrade = true;
		$previouse_upgrade;
        $response;
        try {
			$upgrade_count = 0;
            while (\method_exists(...($func = $this->getUpgradeFunk()))) {
				if ($first_upgrade == true) {
					$this->install();
					$first_upgrade = false;
				} elseif ($func == $previouse_upgrade) {
					//* to protect me from myself
					throw new \Exception("Update loop detected!");
				}
				if ($upgrade_count > 100) {
					//* also to protect me from myself
					throw new \Exception("Too many 'upgrades'!");
				}
				$func();
				$previouse_upgrade = $func;
				$upgrade_count++;
            }
        } catch (\Throwable $th) {
            \error_log($th->getMessage());
			//$response = $th->getMessage();
			return new \WP_Error('redirect_upgrade_failier', $th->getMessage());
        }
        
        if ($_version != $this->version) {
            $this->write_current_version();
            return 'full-success';
        } else {
			$this->write_current_version();
            return 'not-needed';
        }
    }

    private function upgrade_from_1_0_0()
    {
		//~ changing 'to' fields to match what we want
		$redirect_points = $this->gettable("SELECT id, to_url FROM `#prefix_points`", []);
		$site_url = site_url();
		error_log($site_url);
		$mass_query = "INSERT INTO `#prefix_points` (id, to_url) VALUES ";
		$query_vars = [];
		$first = true;
		foreach ($redirect_points as $row_id => $cols) {
			$to_url = $cols->to_url;
			$to_url = str_replace($site_url, '', $to_url);
			$to_url = '/' . \trim($to_url, '/');
			$id = $cols->id;

			if (!$first) {
				$mass_query .= ',';
			} else {
				$first = false;
			}

			$mass_query .= "('%s', '%s') ";
			$query_vars[] = $id;
			$query_vars[] = $to_url;
		}
		$mass_query .= "ON DUPLICATE KEY UPDATE to_url = VALUES(to_url);";
		$result = $this->getnull($mass_query, $query_vars, true);
		if ($result === false) {
			//! something went wrong!
			throw new \Exception('Not implimented!');
		}
		$this->version = '2.0.0';
    }
}
