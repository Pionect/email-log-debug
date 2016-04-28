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

    //set the token early so it is saved by Email Log
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

        $this->db_set('phpmailer',print_r($phpmailer,true));
    }

    function save_debug_output($string,$debug_level) {
        if($this->token == "") return;

        $this->db_append('smtp_response',utf8_encode($string));
    }
    
    function save_result($bool){
        if($this->token == "") return;

        $this->db_set('result',$bool);
    }

    private function db_set($column,$value){
        global $wpdb;
        $query = $wpdb->prepare(
            "UPDATE `{$this->table_name}` SET `$column` = %s
             WHERE `id` = {$this->email_id};",$value);
        $wpdb->query($query);
    }

    private function db_prepend($column,$value){
        global $wpdb;
        $query = $wpdb->prepare(
            "UPDATE `{$this->table_name}` SET `$column` = CONCAT(%s,IFNULL(`$column`,''))
             WHERE `id` = {$this->email_id};",$value);
        $wpdb->query($query);
    }

    private function db_append($column,$value){
        global $wpdb;
        $query = $wpdb->prepare(
            "UPDATE `{$this->table_name}` SET `$column` = CONCAT(IFNULL(`$column`,''),%s)
             WHERE `id` = {$this->email_id};",$value);
        $wpdb->query($query);
    }
}

