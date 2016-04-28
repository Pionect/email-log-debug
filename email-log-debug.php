<?php

namespace EmailLogDebug;

use EmailLogDebug\Includes\UpdateTable;

/**
  Plugin Name: Email Log - Debug Addon
  Description: An add-on Plugin to Email Log Plugin, that allows you to see the SMTP response
  Version: 0.1
  Author: Pionect
  Author URI: http://www.pionect.nl
  Text Domain: email-log
 */

if ( ! defined( 'EMAIL_LOG_DEBUG_PLUGIN_FILE' ) ) {
    define( 'EMAIL_LOG_DEBUG_PLUGIN_FILE', __FILE__ );
}

class Plugin {
    
    const VERSION                  = '0.1';
    const JS_HANDLE                = 'email-log-debug';

    function __construct() {
        include 'Includes/AdminScreen.php';
        include 'Includes/CaptureResponse.php';
        new Includes\AdminScreen();
        new Includes\CaptureResponse();
    }

    function install()
    {
        // handle update email_log table update
        include 'Includes/UpdateTable.php';
        UpdateTable::run();
    }
    
}

function Init_Email_Log_Debug() {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    if ( is_plugin_active( 'email-log/email-log.php' ) ) {
        global $EmailLogDebug;
        
        $EmailLogDebug = new Plugin();
    }
}
add_action( 'init', 'Init_Email_Log_Debug', 100);

register_activation_hook( __FILE__, array( 'Email_Log_Debug', 'install' ) );