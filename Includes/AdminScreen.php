<?php

Namespace EmailLogDebug\Includes;

class AdminScreen {

    function __construct() {
        // column hooks
        if (is_admin()) {
            add_filter(\EmailLog::HOOK_LOG_COLUMNS, array(&$this, 'add_new_column'));
            add_action(\EmailLog::HOOK_LOG_DISPLAY_COLUMNS, array(&$this, 'display_new_column'), 10, 2);
            add_action('wp_ajax_show_smtp_response', array(&$this, 'ajax_smtp_response_callback'));
            add_action('wp_ajax_show_phpmailer', array(&$this, 'ajax_phpmailer_callback'));
            add_filter('el_row_actions',array(&$this,'add_action'),10,2);
        }
    }

    /**
     * Add new SMTP Response column
     */
    function add_new_column($column) {
        $column['result'] = __('Result', 'email-log');
        return $column;
    }

    function display_new_column($column_name, $item) {
        if ($column_name == 'result') {
            if(is_null($item->result)){
                echo '<span style="color:#777">unknown</span>';
            } elseif($item->result) {
                echo '<span style="color:#008000">OK</span>';
            } else {
                echo '<span style="color:#FF0000">Failed</span>';
            }
        }
    }

        /**
     * Display content for SMTP column
     */
    function add_action( $actions, $item) {
        $smtp_response_url = add_query_arg(
            array(
                'action'    => 'show_smtp_response',
                'email_id'  => $item->id,
                'TB_iframe' => 'true',
                'width'     => '600',
                'height'    => '550',
            ),
            'admin-ajax.php'
        );


        $actions['view-smtp-response'] = sprintf( '<a href="%1$s" class="thickbox" title="%2$s">%2$s</a>',
            esc_url( $smtp_response_url ),
            __( 'View SMTP response', 'email-log' )
        );

        $phpmailer_url = add_query_arg(
            array(
                'action'    => 'show_phpmailer',
                'email_id'  => $item->id,
                'TB_iframe' => 'true',
                'width'     => '600',
                'height'    => '550',
            ),
            'admin-ajax.php'
        );

        $actions['view-phpmailer'] = sprintf( '<a href="%1$s" class="thickbox" title="%2$s">%2$s</a>',
            esc_url( $phpmailer_url ),
            __( 'View PHPMailer', 'email-log' )
        );

        return $actions;
    }

    /**
     * AJAX callback for displaying email SMTP response
     *
     * @since 0.2
     */
    function ajax_smtp_response_callback()
    {
        $this->get_db_value('smtp_response');
    }

    function ajax_phpmailer_callback()
    {
        $this->get_db_value('phpmailer');
    }

    private function get_db_value($name){
        global $wpdb;

        $email_id = filter_input(INPUT_GET,'email_id');

        $table_name = $wpdb->prefix . \EmailLog::TABLE_NAME;

        // Select the matching item from the database
        $query   = $wpdb->prepare("SELECT `$name` FROM `$table_name` WHERE id = %d", $email_id);
        $content = $wpdb->get_var($query);

        // Write the full response to the window
        echo '<pre>' . ($content =="" ? 'N\A' : $content). '</pre>';

        die(); // this is required to return a proper result
    }

}
