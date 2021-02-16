<?php
class Sign_database extends CI_Model{
	function __construct() {
		parent::__construct();				
	}	
	function chek_email($email)
	{
		$condition = "email = '$email'";
		$this->db->select('email');
		$this->db->from('sp_users');
		$this->db->where($condition);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return true;
		} else {
			return false;
		}
		
	}
	function chek_phone($email)
	{
		$condition = "phone_number = '$email'";
		$this->db->select('phone_number');
		$this->db->from('sp_users');
		$this->db->where($condition);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return true;
		} else {
			return false;
		}
		
	}
	function login($email,$pass)
	{
		if (!is_numeric($email)) {
			$condition = "email = '$email' AND password='$pass' ";
		}else{
			$condition = "phone_number = '$email' AND password='$pass' ";
		}
		$this->db->select('email');
		$this->db->from('users');
		$this->db->where($condition);
		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return true;
		} else {
			return false;
		}
		
	}	
}