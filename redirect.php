<?php
/*
Plugin Name: Knock on Wood Redirect
Plugin URI:
Description: The Redirect plugin supports in the creation and management of redirects, to tie loose ends on your website. You can add your redirects via the GUI, a pastebin or a CSV import and track the hits for each redirect. The validator for creating / editing redirects filters out redirect loops or chains and gets confirmation if you are trying to redirect from a URL that is live.
Author: Alan Daniels
Version: 2.0.0
Author URI: https://alanssoftware.wordpress.com/
*/
/*
 *  Copyright (C) 2020  Alan Daniels

 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  any later version.

 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.

 *  For the full license, see the license file in the same directory.
 */
//

namespace KOW_Redirect;

\define('KOW_REDIRECT_ROOT', \dirname(__FILE__));
\define('KOW_REDIRECT_URL', plugins_url('', __FILE__));
require_once(KOW_REDIRECT_ROOT.'/includes/helper.php');
require_once(KOW_REDIRECT_ROOT.'/includes/update.php');

class Main extends Helper
{
    public function __construct()
    {
        parent::__construct();
        \register_activation_hook(__FILE__, array($this, 'install'));
        \register_deactivation_hook(__FILE__, array($this, 'uninstall'));
		
		$this->Updates = new Updates();

        \add_action('init', [$this, 'redirect_page']);
        \add_action('admin_menu', [$this, 'admin_menu']);
		\add_action('rest_api_init', [$this, 'rest_api_init']);
        
        if ($this->settings['disable_yoast_redirects']) {
            /*
            * Yoast SEO Disable Automatic Redirects for
            * Posts And Pages
            * Credit: Yoast Development Team
            * Last Tested: May 09 2017 using Yoast SEO Premium 4.7.1 on WordPress 4.7.4
            */
            add_filter('wpseo_premium_post_redirect_slug_change', '__return_true');
            /*
            * Yoast SEO Disable Automatic Redirects for
            * Taxonomies (Category, Tags, Etc)
            * Credit: Yoast Development Team
            * Last Tested: May 09 2017 using Yoast SEO Premium 4.7.1 on WordPress 4.7.4
            */
            add_filter('wpseo_premium_term_redirect_slug_change', '__return_true');
        }
    }

    public function redirect_page($preempt = false, $wp_query = null)
    {
        if (!$preempt) {
            $protocol = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https' : 'http';
            $request = \substr($protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], \strlen(\get_site_url()));
            $request_parts = \explode('?', $request, 2);
            $request_parts[1] = $request_parts[1]??'';
            foreach ($request_parts as $key => $value) {
                $request_parts[$key] = \rtrim($value, '/');
            }
            if ($request_parts[0] == '') {
                $request_parts[0] = '/';
            }
            
            $test_uris = [\esc_url_raw($request_parts[0]), \esc_url_raw($request_parts[0].'?'.$request_parts[1])];
            $redirects = $this->gettable("SELECT * FROM `#prefix_points` WHERE `is_disabled` = 0 AND ( `from_url` LIKE '%s' OR `from_url` LIKE '%s' )", $test_uris);
            //\var_dump($test_uris, $redirects);
            if (\count($redirects) > 0) {
                $redirect = $redirects[0];
                $id = $redirect->id;
                $to = $redirect->to_url;
                $to = strpos($to, ':') != false ? $to : get_site_url('', $to);
                $type = $redirect->redir_type;
                if ($to != '') {
                    if (!$this->is_site_admin()) {
                        $this->getnull("INSERT INTO `#prefix_clicks` (`p_id`) values(%d)", [$id]);
                    }
                    wp_redirect(trim($to, "/\\"), $type);
                    $preempt = true;
                    die;
                }
            }
        }
    }

    /**
     * ! no ref's
     */
    public function admin_menu()
    {
        if (current_user_can('read_private_posts')) {
            add_menu_page('Configure Redirects', 'Redirects', 'edit_others_posts', Helper::$prefix.'redirects', [$this, 'admin_page']);
        }
        //add_submenu_page(Helper::$prefix.'redirects',  'Mass Input', 'Mass Input', 'edit_others_posts', Helper::$prefix.'mass_input', [$this, 'admin_page']);
        //add_submenu_page(Helper::$prefix.'redirects',  'Settings', 'Settings', 'edit_others_posts', Helper::$prefix.'settings', [$this, 'admin_page']);
    }
    
    /**
     * ! no ref's
     */
    public function admin_page()
    {
        ?>
		<div id='root'>
		</div>
		<?php
        wp_enqueue_script($this->prefix . 'app', plugin_dir_url(__FILE__) . 'assets/app.js', array( 'jquery' ), mt_rand(10, 1000), true);
		wp_enqueue_style($this->prefix . 'app-style', plugin_dir_url(__FILE__) . 'assets/main.css', [], mt_rand(10, 1000));
		
		$localisation = [
			'api_nonce'   => wp_create_nonce('wp_rest'),
			'api_url'	  => rest_url('kow_redirect/v1'),
			'site_url'    => site_url(),
			'plugin_url'  => KOW_REDIRECT_URL
		];

		$localisation = apply_filters($this->prefix.'app_localisation', $localisation);

		\error_log(\json_encode($localisation));

        wp_localize_script(
            $this->prefix . 'app',
            'wpr_object',
            $localisation
        );
    }
    
    public function rest_api_init()
    {
        require_once(KOW_REDIRECT_ROOT.'/includes/rest.php');
        new Rest();
    }
}

new Main();
