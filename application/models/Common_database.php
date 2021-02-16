<?php
class Common_database extends CI_Model{
	function __construct() {
		parent::__construct();				
	}	
	function add($table,$data){
		$this->db->insert($table,$data);
	}
	function update($table,$data,$field,$key,$field2 = false,$key2 = false,$field3 = false,$key3 = false)
	{		
		if ($field2 == false || $key2 == false) {			
			$condition = "$field='$key'";
		}
		elseif($field3 == false || $key3 == false){			
			$condition = "$field='$key' AND $field2='$key2'";
		}
		else{
			$condition = "$field='$key' AND $field2='$key2' AND $field3='$key3'";
		}
		$this->db->where($condition);
		$this->db->update($table, $data);
	}
	function get_cond($table,$cond = false){		
		if ($cond) {					
			$condition = $cond;
		}	
		$this->db->select('*');
		$this->db->from($table);		
		if (isset($condition)) {
			$this->db->where($condition);
		}		
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}
	public function exec($statement = false){
		if ($statement){
			$this->db->select($statement);
			$query = $this->db->get();
			if ($query->num_rows() > 0) {
				return $query->result_array();
			} else {
				return false;
			}
		}
		return false;
	}   
	function delete($table,$field,$key,$field2 = false,$key2 = false,$field3 = false,$key3 = false)
	{		
		if ($field2 == false || $key2 == false) {			
			$condition = "$field='$key'";
		}
		elseif($field3 == false || $key3 == false){			
			$condition = "$field='$key' AND $field2='$key2'";
		}
		else{
			$condition = "$field='$key' AND $field2='$key2' AND $field3='$key3'";
		}
		$this->db->where($condition);
		$this->db->delete($table);
		return true;
	}

	function get_data($table,$limit = false,$start_id = false,$parent_field = false,$parent_value ="",$parent_field_2 = false,$parent_field_2_value = "",$order_by = false,$parent_field_3 = false,$parent_field_3_value = ""){		
		if ($parent_field != false) {
			if ($parent_field_3 != false) {
				$condition = "$parent_field = '$parent_value' AND $parent_field_2 = '$parent_field_2_value' AND $parent_field_3 = '$parent_field_3_value'";
			}
			elseif ($parent_field_2 != false) {
				$condition = "$parent_field = '$parent_value' AND $parent_field_2 = '$parent_field_2_value' ";
			}else{				
				$condition = " $parent_field = '$parent_value'";
			}
		}				
		$this->db->select('*');
		$this->db->from($table);
		if ($order_by) {
			$order_by = $order_by;
		}else{
			$order_by = "ORDER by id DESC";
		}
		if (isset($condition)) {
			$this->db->where($condition." ".$order_by);
		}else{
			$this->db->where("1 ".$order_by);
		}
		if ($limit != false) {
			$this->db->limit($limit);
		}
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}
	function sum($table,$f,$field = false,$key = false,$field2 = false,$key2 = false,$field3 = false,$key3 = false){
		if ($field != false) {
			if ($field2 != false) {
				$condition = "$field='$key' AND $field2='$key2'";
			}elseif($field3 != false){
				$condition = "$field='$key' AND $field2='$key2' AND $field3='$key3'";
			}else{
				$condition = "$field='$key'";
			}
		}

		$this->db->select_sum($f);
		$this->db->from($table);
		if (isset($condition)) {
			$this->db->where($condition);
		}
		$query=$this->db->get();
		return $query->result();
	}
	public function count($table,$f,$field = false,$key = false,$field2 = false,$key2 = false,$field3 = false,$key3 = false){
		if ($field != false) {
			if ($field2 != false) {
				$condition = "$field='$key' AND $field2='$key2'";
			}elseif($field3 != false){
				$condition = "$field='$key' AND $field2='$key2' AND $field3='$key3'";
			}else{
				$condition = "$field='$key'";
			}
		}		
		$this->db->select('*');
		$this->db->from($table);
		if (isset($condition)) {
			$this->db->where($condition);
		}				
		$query = $this->db->get();
		return $query->num_rows();
	}
	public function searchEmail($search){
		$query = $this->db->query("SELECT * FROM sp_users where email like '%$search%'");
		return $query->result_array();
	}
	public function searchUser($search){
		$query = $this->db->query("SELECT * FROM sp_users where email like '%$search%' or account_no like '%$search%'");
		return $query->result_array();
	}
}