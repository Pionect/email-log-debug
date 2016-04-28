<?php

Namespace EmailLogDebug\Includes;

class CaptureResponse {

    private $token;
    private $email_id;
    private $table_name;
    
    function __construct(){ 
        // Register hooks
        add_filter( 'wp_mail', array( $this, 'set_token' ),1);
        add_action('phpmailer_init',array(&$this,'phpmailer_init'));
    }

    function set_token($mail_info){
        $this->token = uniqid();
        $mail_info['headers'][] = 'DEBUG-TOKEN:'.$this->token;
        return $mail_info;
    }

    function phpmailer_init($phpmailer){
        $phpmailer->SMTPDebug = 2;
        $phpmailer->Debugoutput = array(&$this,'save_debug_output');
        $phpmailer->action_function = array(&$this,'save_result');

        global $wpdb;
        $this->table_name = $wpdb->prefix . \EmailLog::TABLE_NAME;

        $this->email_id = $wpdb->get_var("SELECT id FROM `{$this->table_name}` WHERE `headers` LIKE '%{$this->token}%'");


        $query = $wpdb->prepare(
            "UPDATE `{$this->table_name}` SET `phpmailer` = %s
             WHERE `id` = {$this->email_id};",
            print_r($phpmailer,true));
        $wpdb->query($query);
    }

    function save_debug_output($string,$debug_level) {
        if($this->token == "") return;
        
        global $wpdb;
        $query = $wpdb->prepare(
            "UPDATE `{$this->table_name}` SET `smtp_response` = CONCAT(IFNULL(`smtp_response`,''),%s)
             WHERE `id` = {$this->email_id};",
            $string);
        $wpdb->query($query);
    }
    
    function save_result($bool){
        if($this->token == "") return;

        global $wpdb;
        $query = "UPDATE `{$this->table_name}` SET `result` = '$bool'
                  WHERE `id` = {$this->email_id};";
        $wpdb->query($query);
    }
}

