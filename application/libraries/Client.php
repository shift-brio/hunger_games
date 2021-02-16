<?php
	Class Client{
		public function __construct() {
	      $CI = & get_instance();      
	      $CI->load->library('session');
	      $CI->load->helper('form');
	      $CI->load->model('common_database');      
	      $CI->load->library('user_agent');
	      $CI->load->helper('url_helper');
	      $CI->load->helper('date');            
	   }
	} 
 ?>