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

    function admin() {
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        if ( is_plugin_active( 'email-log/email-log.php' ) && is_admin() ) {
            include 'Includes/AdminScreen.php';
            new Includes\AdminScreen();
        }
    }

    function init_capturing(){
        include 'Includes/CaptureResponse.php';
        new Includes\CaptureResponse();
    }

    function install()
    {
        // handle update email_log table update
        include 'Includes/UpdateTable.php';
        UpdateTable::run();
    }
    
}

add_action( 'admin_init', array( 'EmailLogDebug\Plugin', 'admin' ), 100);
add_action( 'plugins_loaded', array( 'EmailLogDebug\Plugin', 'init_capturing' ), 1);
register_activation_hook( __FILE__, array( 'EmailLogDebug\Plugin', 'install' ) );