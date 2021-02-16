<?php
Class Common{
	public function __construct() {

		self::instance()->load->library('session');
		self::instance()->load->helper('form');
		self::instance()->load->model('common_database');      
		self::instance()->load->library('user_agent');
		self::instance()->load->helper('url_helper');
		self::instance()->load->helper('date');
	}

   static function format_item($i = false){
      if ($i) {
         return [
                  "id" => $i["id"],
                  "name" => $i["name"],
                  "desc" => $i["description"],
                  "cost" => $i["cost"],
                  "image" => base_url("media/menu/".$i["image"])
              ];
      }
      return false;
   }
   static function config($item = "app_name"){     

      return self::instance()->config->item($item);
   } 
   
   static  function get_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
      $ipaddress = getenv('HTTP_CLIENT_IP');
   else if(getenv('HTTP_X_FORWARDED_FOR'))
      $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
   else if(getenv('HTTP_X_FORWARDED'))
      $ipaddress = getenv('HTTP_X_FORWARDED');
   else if(getenv('HTTP_FORWARDED_FOR'))
      $ipaddress = getenv('HTTP_FORWARDED_FOR');
   else if(getenv('HTTP_FORWARDED'))
      $ipaddress = getenv('HTTP_FORWARDED');
   else if(getenv('REMOTE_ADDR'))
      $ipaddress = getenv('REMOTE_ADDR');
   else
      $ipaddress = 'UNKNOWN'; 
   return $ipaddress;
}

static function render_order($order = false){
   if ($order) {
      $items = self::instance()->common_database->get_data("order_items", false, false, "order_id", $order["identifier"]);
      if ($items) {
         $total_cost = 0;
         foreach ($items as $item) {
            $i = self::instance()->common_database->get_data("menu_items",1,false,"id",$item["item"]);            
            if ($i) {
               $total_cost += $i[0]["cost"] * $item["quantity"];
            }                     
         }
         if ($order["status"] == 1) {
            $button = '';
         }else{
            $button = ' <div class="ord-tools">
                           <button item="'.$order["identifier"].'" class="ord-cancel click">
                        Cancel order
                           </button>
                        </div>';
         }
         $content = '
            <div class="ord-item">
               <div class="ord-date">
                  '.(date("d-m-Y", $order["date_added"])).'
               </div>
               <div class="ord-code">
                  '.(strtoupper($order["identifier"])).'
               </div>
               <div class="ord-amnt">
                 '.(number_format($total_cost, 0)).'
               </div>
               '.($button).'
            </div>
         ';
         echo $content;
      }
   }
}
static function get_currency($country = "ke"){     

   $c_data = self::instance()->common_database->exec("currency_code, currency_symbol from sp_countries where name='$country' or code='$country' limit 1");

   if ($c_data) {
      return $c_data[0]["currency_symbol"] != "" ?  $c_data[0]["currency_symbol"]." " :  $c_data[0]["currency_code"]." ";
   }

   return "KSh ";
}

static function getCountries(){
   $c = self::instance()->common_database->get_cond("sp_countries", "1 order by name asc");

   return $c;
}   

static function month_len($month = 1, $year = 2020){
   return cal_days_in_month(CAL_GREGORIAN, $month, $year);
}


static function updated(){  
   $time = time();          
   $reloads = isset($_SESSION["reloads"]) ? $_SESSION["reloads"]: 0;
   $_SESSION["reloads"] = $reloads + 1;
   $reloads = $_SESSION["reloads"];


   $new_update =   self::config("last_update") < (60 * 60 * 24);
   if ($reloads > 5) {
      $new_update = false;
      unset($_SESSION["reloads"]);
   }

   return $new_update  ? '?'.$time : '';  
}


static function gen_pass($string = ""){
   return md5(sha1($string));
}

static function logout(){
   unset($_SESSION['usermail']);
   unset($_SESSION['account']);
   unset($_SESSION["active_tab"]);
}

static function active_tab(){
   return isset($_SESSION["active_tab"]) ? $_SESSION["active_tab"] : "";
}
static function abs_date($date_string = "", $filter = "m"){
   if(strlen($date_string) == 0){return 0;}
   $time = strtotime($date_string);
   $date_string = date($filter, $time);

   $pattern = '/0/i';
   $replacement = '';

   return intval($date_string) < 10 ? preg_replace($pattern, $replacement, $date_string): $date_string;

}   

static function instance(){

   return $ci = get_instance();
}
static function get_user($email = false){
   if ($email) {         
      if (is_numeric($email)) {
         $field = "phone_number";
      }
      else{
         $field = "email";
      }
      $user = self::instance()->common_database->get_data("sp_users",1,false,$field,$email);
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
static function auth($email = false, $pass = false){
   if ($email && $pass) {    
     $l = self::instance()->sign_database->login($email, self::gen_pass($pass));
     if ($l) {           
      $data['active']  = time();      
      self::instance()->common_database->update('users',$data,'email',$email);

      return true;
   }
}
return false;
}

static function getTime(){ 
  if ((time() - self::instance()->config->item("last_update")) < (60 * 60 * 24)) {
   $data['time'] = '?'.time();	
}else{
   $data['time'] = '';	
}
return $data['time'];
}

static function emitData($val){
  header("Content-type:application/json");
  echo json_encode($val);
  exit();
}
static function getPhoneType($phone){
  $exp = "/[+]+[0-9]+[0-9]+[0-9]+[7]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]/";
  $exp1 = "/[0]+[7]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]+[0-9]/";
  if (preg_match($exp, $phone)) {
   return "type_1";
}else if(preg_match($exp1, $phone)){
   return "type_2";
}else{
   return false;
}

}
static function cmp($a,$b){
  if ($a['profit'] == $b['profit']) {
   return 0;
}
return ($a['profit'] < $b['profit']) ? -1 : 1;
}
static function acmp($a,$b){
  if ($a['profit'] == $b['profit']) {
   return 0;
}
return ($a['profit'] > $b['profit']) ? -1 : 1;
}
static function sorter($arr){  
  $CI = & get_instance();       
  usort($arr, ['common','cmp']);
  return $arr;
} 
static function asorter($arr){  
  $CI = & get_instance();       
  usort($arr, ['common','acmp']);
  return $arr;
}
/*fiter sorter*/
static function tcmp($a,$b){
  if ($a['total'] == $b['total']) {
   return 0;
}				
return ($a['total'] < $b['total']) ? 1 : -1;
}
static function tacmp($a,$b){
  if ($a['total'] == $b['total']) {
   return 0;
}		
return ($a['total'] > $b['total']) ? 1 : -1;
}
static function dcmp($a,$b){
  if ($a['discount'] == $b['discount']) {
   return 0;
}
return ($a['discount'] < $b['discount']) ? 1 : -1;
}
static function dacmp($a,$b){
  if ($a['discount'] == $b['discount']) {
   return 0;
}
return ($a['discount'] > $b['discount']) ? 1 : -1;
}
static function o_sorter($arr,$field = false){  
  $CI = & get_instance();       
  if ($field == "discount") {
   usort($arr, ['common','dcmp']);
}else{
   usort($arr, ['common','tcmp']);
}
return $arr;
}	
static function o_asorter($arr,$field = false){  
  $CI = & get_instance();       
  if ($field == "discount") {
   usort($arr, ['common','dacmp']);
}else{
   usort($arr, ['common','tacmp']);
}
return $arr;
}

static function recycler($account = false, $type = false, $data = false){
  if ($account && $type && $data) {
   $CI = &get_instance();
   $d = [
    "type" => $type,
    "account" => $account,
    "date" => time(),
    "deleted_by" => $_SESSION["usermail"],
    "data" => $data,
    "date_added" => time()
 ];
 self::instance()->common_database->add("sp_recycler",$d);
}
}
static function get_account_no($text = ""){
   return strtoupper(mb_substr(md5(sha1($text.time())), 0,6));
}

static function remember($user = false){		
  if (isset($_SESSION["remember"])) {
   $remembered = $_SESSION["remember"];
}else{
   $remembered = [];
}		
if ($user) {			
   $inList = false;
			/*if (is_array($remembered) && sizeof($remembered) > 0) {		
				foreach ($remembered as $u) {								
					$inList = $u["email"] == $user || true;
				}								
				if (!$inList) {
					array_push($remembered, ["email" => $user, "date" => time()]);
				}				
			}else{
				array_push($remembered, ["email" => $user, "date" => time()]);				
			}*/
			array_push($remembered, ["email" => $user, "date" => time()]);
			$_SESSION["remember"]	= $remembered;			
		}	
		if (sizeof($remembered) > 0) {
			return json_decode(json_encode($remembered[sizeof($remembered) - 1]));
		}	
		return json_decode(json_encode(["email" => "", "date" => time()]));
	}

  static function sendMail($head = false,$to = false, $message = false, $from = false){
   $CI = &get_instance();   
   $config['protocol']  = 'smtp';
   $config['smtp_host'] = 'mail.spendtrack.app';
   $config['smtp_port'] = 465;
   $config['smtp_user'] = self::config("support_email");
   $config['smtp_pass'] = 'sp@_support';
   self::instance()->load->library('email', $config);
   /*=====================================================*/
   self::instance()->email->set_newline("\r\n");  
   self::instance()->email->set_mailtype("html"); 
   /*===================================================*/                      
   if ($from && is_array($from)) {
      self::instance()->email->from($from["email"], $from["name"]);
   }else{
      self::instance()->email->from(self::config("support_email"), self::config("app_name"));
   }
   self::instance()->email->to($to);
   self::instance()->email->subject($head);      
   self::instance()->email->message($message); 
   self::instance()->email->send();

   return true;
}

}
?>