<?php

Namespace EmailLogDebug\Includes;

class CaptureResponse {

    private $phpmailer;

    function __construct(){ 
        // Register hooks
        global $phpmailer;

        if ( !is_object( $phpmailer ) || $phpmailer instanceof PHPMailer == false ) {
            require_once ABSPATH . WPINC . '/class-phpmailer.php';
            require_once ABSPATH . WPINC . '/class-smtp.php';
            $phpmailer = new PHPMailer( true );
        }

        $this->phpmailer = $phpmailer;

        $phpmailer->AddCustomHeader('DEBUG-TOKEN',"DEBUG-TOKEN: " . uniqid() );
        $phpmailer->SMTPDebug = 2;
        $phpmailer->Debugoutput = array(&$this,'save_smtp_output');
        $phpmailer->action_function = array(&$this,'save_result');
    }
    
    function save_debug_output($string,$debug_level) {
        global $wpdb;

        $headers = $this->phpmailer->getCustomHeaders();
        dd($headers);

        if (array_key_exists('PNCT-TOKEN', $headers) == FALSE) {
            return;
        }
        
        $table_name = $wpdb->prefix . EmailLog::TABLE_NAME;
        $sql = "UPDATE `" . $table_name . "` SET `smtp_response` = '". mysqli_real_escape_string($string) ."'"
            . "WHERE `headers` LIKE '%" . $headers['PNCT-TOKEN'] . "%' ;";

        $wpdb->query($sql);
    }
    
    function save_result($bool){
        $this->phpmailer->getCustomHeaders();
    }
}

