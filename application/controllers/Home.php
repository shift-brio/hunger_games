<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
	function __construct(){
	 parent::__construct();
	 $this->load->helper("url_helper");
	 $this->load->model("sign_database");
	 $this->load->model("common_database");	 
    $this->load->library('common');

    $this->status = false;
    $this->message = "";
	}   
   public function test(){        
         $d = [
            "ip" => common::get_ip(),
            "time" => time()         
         ];
         $this->common_database->add("sp_logs", $d);
         common::welcome("briochieng97@gmail.com");  
   }	
   public function admin(){
       $data['time'] = common::updated();
      if (isset($_SESSION["usermail"])) {
          $this->load->view("headers/main",$data);        
          $this->load->view("admin",$data);
      }else{
         redirect(base_url());
      }
   }
   public function index(){              
       $data['time'] = common::updated();
       if (isset($_SESSION["usermail"])) {
          $this->load->view("headers/main",$data);        
          $this->load->view("components/home",$data);
       }
       else{
         $this->load->view("headers/main",$data);        
         $this->load->view("index",$data);
       }
   }
   public function sign_in(){
      if (!isset($_SESSION["usermail"])) {
         $data['time'] = common::updated();
         $this->load->view("headers/landing",$data);
         $this->load->view("sign_in");
      }else{
         redirect(base_url());
      }
   }   
	public function login(){
		if (isset($_POST['email']) && isset($_POST['pass'])) {
         $email = $_POST["email"];

			if (common::auth($_POST["email"], $_POST["pass"])) {	
            if (!is_numeric($email)) {
               $user = $this->common_database->get_data('users',1,false,"email",$email);
            }else{
               $user = $this->common_database->get_data('users',1,false,"phone",$email);
            }
            $_SESSION['usermail'] = $user[0]['email']; 
            $_SESSION["name"]     = $user[0]["name"];
				$r['status'] = true;
				$r['m'] = "logged in";
			}
			else{
				$r['status'] = false;
				$r['m'] = "Wrong email or password";
			}
		}
		else{
			$r['status'] = false;
			$r['m'] = "Invalid access";
		}
		common::emitData($r);
	}			   	
	protected function emitData($val = []){
		header("Content-type:application/json");
		echo json_encode($val);
		exit();
	}		  
	public function get_user($email = false){
		if ($email) {			
			if (is_numeric($email)) {
				$field = "phone";
			}
			else{
				$field = "email";
			}
			$user = $this->common_database->get_data("users",1,false,$field,$email);
			if ($user) {
				$r['status'] = true;
				$r['m'] = $user;
			}
			else{
				$r['status'] = false;
				$r['m'] = "User does not exist";
			}
		}
		else{
			$r['status'] = false;
			$r['m'] = "No email or phone supplied";
		}
		return $r;		
	}				
	protected function checkAdmin($email = false){
		if (!$email) {
			return false;
		}
		else{
			$b = $this->common_database->get_data('sp_members',false,false,'email',$email,'account',$_SESSION['account']);
			if ($b) {
				if ($b[0]['post'] == 'admin') {
					return true;
				}else{
					return false;
				}
			}
			else{
				return false;
			}
		}
	}	
	protected function setHeaders(){
	    header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
	    header("Last-Modified: ".gmdate("D, d M Y HH:i:s")." GMT");
	    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	    header("Cache-Control: post-check=0, pre-check=0",false);
	    header("pragma: no-cache");
	}	
   public function register($slug = false){
      if (!isset($_SESSION["usermail"])) {
         if (isset($_POST["email"]) && isset($_POST["name"]) && isset($_POST["phone"]) && isset($_POST["pass"])) {
            $name = $_POST["name"];
            $email = $_POST["email"];
            $phone = $_POST["phone"];
            $pass = common::gen_pass($_POST["pass"]);

            $ch_email = $this->common_database->get_data("users",1,false,'email',$_POST['email']);
            $ch_phone = $this->common_database->get_data("users",1,false,'email',$_POST['phone']);
            if (!$ch_email) {
               if (!$ch_phone) {
                  $data = [
                     "email" => $email,
                     "password" => $pass,
                     "phone" => $phone,
                     "name" => $name,
                     "active" => time(),
                     "date_added" => time()
                  ];
                  $this->common_database->add("users", $data);
                  $_SESSION["usermail"] = $email;
                  $_SESSION["phone"] = $phone;
                  $r["status"] = true;
               }else{
                   $r["status"] = false;
                   $r["m"] = "The phone number you supplied has already been  registered.";
               }
            }else{
               $r["status"] = false;
               $r["m"] = "The email address you supplied has already been registered.";
            }
         }else{
            $r["status"] = false;
            $r["m"] = "Kindly check your credetials and try again";
         }
      }else{
         $r["status"] = false;
         $r["m"] = "An account is already logged in on this browser. Kindly reload the site to proceed.";
      }
      common::emitData($r);
   }
   public function menu_item(){
      if (isset($_POST["item"]) && is_numeric($_POST["item"]) && isset($_SESSION["usermail"])) {
        $ch_item = $this->common_database->get_data("menu_items", 1, false, "id", $_POST["item"]);
        if ($ch_item) {
           $r["status"] = true;
           $r["m"] = [
            "id" =>$ch_item[0]["id"],
            "name" => $ch_item[0]["name"],
            "desc" => $ch_item[0]["description"],
            "image" => $ch_item[0]["image"],
            "cost" =>$ch_item[0]["cost"]
           ];
        }else{
         $r["status"] = false;
         $r["m"] = "The item requested could not be found at this time";
        }
      }else{
         $r["status"] = "false";
         $r["m"] = "Invalid access";
      }
      common::emitData($r);
   }
   public function search(){
      if (isset($_SESSION["usermail"]) && isset($_POST["key"])) {
         $key = preg_replace("/'/", "", $_POST["key"]);
         $key = preg_replace('/"/',"", $key);
         $res = $this->common_database->exec("* from menu_items where name like '%$key%' and available=1 order by name asc");
         if ($res) {
           $r["status"] = true;
           $r["m"] = [];
           foreach ($res as $i) {
              $v = common::format_item($i);
              array_push($r["m"], $v);
           }
         }else{
            $r["status"] = true;
            $r["m"] = $this->load->view("components/no_food",[], true);
         }
      }else{
         $r["status"] = false;
         $r["m"] = "Invalid access";
      }
      common::emitData($r);
   }
   public function update_cart(){
      if (isset($_SESSION["usermail"]) && isset($_POST["items"]) && (is_array($_POST["items"]) || $_POST["items"] == 0) && isset($_POST["mode"])) { 

         if ($_POST["items"] == 0) {
            $_SESSION["cart"] = [];
            $r["status"] = true;
            $r["m"] = [];
            common::emitData($r);

            return;
         }

         
         $mode = $_POST["mode"];

         $items = $_POST["items"];
         if (!isset($_SESSION["cart"])) {
           $_SESSION["cart"] = [];
         }
         $all_in = true;
         foreach ($items as $item) {
            if (isset($item["id"]) && isset($item["qty"])) {
               $ch = $this->common_database->get_data("menu_items",1, false,"id",$item["id"],"available", 1);
            }else{
               $all_in = false;
            }
         }
         if ($all_in) {
            $c_len = sizeof($_SESSION["cart"]);
            if ($c_len > 0) {               
               if ($mode == "update") {
                  foreach ($items as $item) {
                     $in_cart = false;
                     for ($i=0; $i < $c_len ; $i++) { 
                        if ($item["id"] == $_SESSION["cart"][$i]["id"]) {
                           $in_cart = true;
                           $_SESSION["cart"][$i]["qty"] = $item["qty"];
                        }
                     }
                     if (!$in_cart) {
                        array_push($_SESSION["cart"], ["id" => $item["id"], "qty" => $item["qty"]]);
                     }
                  }
               }elseif($mode == "replace"){
                  $_SESSION["cart"] = [];
                 foreach ($items as $item) {
                   array_push($_SESSION["cart"], ["id" => $item["id"], "qty" => $item["qty"]]);
                 } 
               }
            }else{
               foreach ($items as $item) {
                  $v = [
                     "id" => $item["id"],
                     "qty" => $item["qty"]
                  ];
                  array_push($_SESSION["cart"], $v);
               }
            }
            $r["status"] = true;
            $r["m"] = $_SESSION["cart"];
         }else{
            $r["status"] = false;
            $r["m"] = "Items unavailable in database.";
         }
      }else{
         $r["status"] = false;
         $r["m"] = "Invalid access";
      }
      common::emitData($r);
   }
   public function get_cart(){
      if (isset($_SESSION["usermail"])) {
         $r["status"] = true;
         if (isset($_SESSION["cart"])) {
            $c_data = [];
            foreach ($_SESSION["cart"] as $item) {
              $d = $this->common_database->get_data("menu_items", 1, false, "id", $item["id"]);
              if ($d) {
                 $i_d = common::format_item($d[0]);
                 $i_d["qty"] = $item["qty"];
                 array_push($c_data, $i_d);
              }
            }
            $r["m"] = $c_data;
         }else{
            $r["m"] = [];
         }
      }else{
         $r["status"] = false;
         $r["m"] = "Invalid access";
      }
      common::emitData($r);
   }
   public function place_order(){
      if (isset($_SESSION["usermail"]) && isset($_POST["cart"]) && is_array($_POST["cart"])) {
         $items = $_POST["cart"];
         $all_in = true;

         foreach ($items as $item) {
            $ch = $this->common_database->get_data("menu_items", 1, false, "id",$item["id"],"available", 1);
            if (!$ch) {
               $all_in = false;
            }
         }
         if ($all_in) {
            $ord_id = sha1(md5($_SESSION["usermail"].time().$items[sizeof($items) - 1]["id"]));
            $ord_id = mb_substr($ord_id, 0, 5);

            $ord_data = [
               "user" => $_SESSION["usermail"],
               "identifier" => $ord_id,
               "date_added" => time()
            ];
            $this->common_database->add("orders", $ord_data);
            foreach ($items as $item) {
               $i_data = [
                  "order_id" => $ord_id,
                  "item" => $item["id"],
                  "quantity" => $item["qty"] > 0 ? $item["qty"] : 1,
                  "date_added" => time()
               ];
               $this->common_database->add("order_items", $i_data);
            }
            $r["status"] = true;
            $r["m"] = "Success";
            $_SESSION["cart"] = [];
         }else{
            $r["status"] = false;
            $r["m"] = "Some items in your shopping cart are not available at the moment.";
         }
      }else{
         $r["status"] = false;
         $r["m"] = "Invalid access";
      }
      common::emitData($r);
   }
   public function cancel_order(){
      if (isset($_SESSION["usermail"]) && isset($_POST["item"])) {
         $item = $_POST["item"];
         $order = $this->common_database->get_data("orders",1,false,"identifier",$_POST["item"], "user", $_SESSION["usermail"]);
         if ($order) {
            $this->common_database->delete("orders","identifier",$item);
            $this->common_database->delete("order_items","order_id",$item);
            $r["status"] = true;
         }else{
            $r["status"] = false;
            $r["m"] = "Order not found";
         }
      }else{
         $r["status"] = false;
         $r["m"] = "Invalid access";
      }
      common::emitData($r);
   }
	public function logout(){
		unset($_SESSION['usermail']);		
		unset($_SESSION);
      session_destroy();

		redirect(base_url(""));
	}
}