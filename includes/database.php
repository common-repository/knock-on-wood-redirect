<?php

namespace KOW_Redirect;

class database
{
    public static $table_prefix = 'redirect_';

    public function uninstall()
    {
        $dummy = new Helper();
        if ($dummy->settings['drop_tables_on_uninstall'] == true) {
            //TODO: drop all associated tables here
        }
    }

    public function install()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE `#prefix_points` (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        from_url text NOT NULL,
        to_url text NOT NULL,
        redir_type INT(3) NOT NULL,
        created DATETIME DEFAULT NOW(),
        is_disabled BOOLEAN DEFAULT FALSE,
        overridden BOOLEAN DEFAULT FALSE,
        PRIMARY KEY  (id)
        ) $charset_collate;";
        $this->getnull($sql, array(), false);

        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE `#prefix_clicks` (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        p_id mediumint(9) NOT NULL,
        created DATETIME DEFAULT NOW(),
		INDEX (`p_id`),
        PRIMARY KEY  (id)
        ) $charset_collate;";

        $this->getnull($sql, array(), false);
        
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE `#prefix_settings` (
			`id` VARCHAR(25) NOT NULL , 
			`value` TEXT NOT NULL , 
			`updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , 
			PRIMARY KEY (`id`)
		) $charset_collate;";

        $this->getnull($sql, array(), false);
    }

    private function cleanTable($table)
    {
        global $wpdb;
        $replaceWith = $wpdb->prefix . database::$table_prefix;
        return str_replace("#prefix_", $replaceWith, $table);
    }

    private function prepair($query, $data = array(), $debug = false)
    {
        global $wpdb;
        $query = Database::cleanTable($query);
        $return = $query;
        if (count($data) <= 0) {
            $return = $query;
        } else {
            $return = $wpdb->prepare($query, $data);
        }
        if ($debug) {
			error_log($return);
        }
        return $return;
    }
    
    public function getvar($query, $data = array(), $debug = false)
    {
        global $wpdb;
        $prepaired_query = Database::prepair($query, $data, $debug);
        return $wpdb->get_var($prepaired_query);
    }

    public function getnull($query, $data = array(), $debug = false)
    {
        global $wpdb;
        $prepaired_query = Database::prepair($query, $data, $debug);
        return $wpdb->query($prepaired_query);
    }

    public function insert($query, $data = array(), $debug = false)
    {
        global $wpdb;
        $this->getnull($query, $data, $debug);
        return $wpdb->insert_id;
    }

    public function gettable($query, $data = array(), $debug = false)
    {
        global $wpdb;
        $prepaired_query = Database::prepair($query, $data, $debug);
        return $wpdb->get_results($prepaired_query);
    }
}
