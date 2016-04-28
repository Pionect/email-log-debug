<?php

Namespace EmailLogDebug\Includes;

/**
 * Db email_log table update
 *
 * @package     Email Log - Debug
 * @author      Pionect
 * @since       0.1
 */
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * Helper class to update the email_log table
 *
 * @author Pionect
 */
class UpdateTable {

    /**
     * Update email_log table
     *
     * @global object $wpdb
     */
    public static function run() {

        global $wpdb;
        $table_name = $wpdb->prefix . EmailLog::TABLE_NAME;

        $wpdb->get_results("SELECT * 
                            FROM information_schema.COLUMNS 
                            WHERE TABLE_NAME = '$table_name' 
                            AND COLUMN_NAME = 'smtp_response'");

        if ($wpdb->num_rows == 0) {

            $sql = "ALTER TABLE `$table_name` 
                ADD COLUMN `smtp_response` TEXT,
                ADD COLUMN `phpmailer` TEXT,
                ADD COLUMN `result` SMALLINT(1)";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            $wpdb->query($sql);
        }
    }

}