<?php
	Class Mailer{
		public function __construct() {
	      $CI = & get_instance();      
	      $CI->load->library('session');
	      $CI->load->helper('form');
	      $CI->load->model('common_database');      
	      $CI->load->library('user_agent');
         $CI->load->library('common');
	      $CI->load->helper('url_helper');
	      $CI->load->helper('date');            
	   }
	   public static function send($data){
			$CI = & get_instance(); 		
			$to 		= $data['to'];
			$from       = $data['from'];
			$subject    = $data['subject'];
			$body       = $data['message'];
			$attachment = $data['attachment'];			

			
		}		
	} 
 ?>