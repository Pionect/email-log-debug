<?php
/**
Plugin Name: Email Log - Debug Addon
Description: An add-on Plugin to Email Log Plugin, that allows you to see the SMTP response
Version: 0.1
Author: Pionect
Author URI: http://www.pionect.nl
Text Domain: email-log
 */

namespace EmailLogDebug;

use EmailLogDebug\Includes\UpdateTable;

if ( ! defined( 'EMAIL_LOG_DEBUG_PLUGIN_FILE' ) ) {
    define( 'EMAIL_LOG_DEBUG_PLUGIN_FILE', __FILE__ );
}

class Plugin {
    
    const VERSION       = '0.1';
    const JS_HANDLE     = 'email-log-debug';
    const TABLE_NAME    = \EmailLog::TABLE_NAME;

    public static function admin() {
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        if ( is_plugin_active( 'email-log/email-log.php' ) && is_admin() ) {
            include 'Includes/AdminScreen.php';
            new Includes\AdminScreen();
        }
    }

    public static function init_capturing(){
        include 'Includes/CaptureResponse.php';
        new Includes\CaptureResponse();
    }

    public static function install()
    {
        // handle update email_log table update
        include 'Includes/UpdateTable.php';
        UpdateTable::run();
    }
    
}

add_action( 'admin_init', array( \EmailLogDebug\Plugin::class, 'admin' ), 100);
add_action( 'plugins_loaded', array( \EmailLogDebug\Plugin::class, 'init_capturing' ), 1);
register_activation_hook( __FILE__, array( \EmailLogDebug\Plugin::class, 'install' ) );