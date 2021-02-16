<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Wallet {
   public $single_day = (60*60*24);
   public $month = (60*60*24*30); 

   public function __construct() {
      $CI = & get_instance();
      $CI->load->helper('url_helper');
      $CI->load->library('user_agent');
      $CI->load->model('common_database');
      $CI->load->library('email');    
   } 
   static function getSubs($sub,$time){
      $CI = & get_instance();
      return $CI->config->item('subs')->$sub->$time;
   }   
   static function status($account = false,$type = false){
      $CI = & get_instance();
      $single_day = (60*60*24);
      $month      = ($single_day*31);
      $year       = ($single_day*365);
      $semi       = $month * 6;
      $due        = false;
      $trial      = $CI->config->item("trial");

      $CI = & get_instance();
      if (isset($_SESSION['usermail']) && $account && $type) {
        if ($type == 'business') {
          $acc = $CI->common_database->get_data("sp_business",1,false,'group_identifier',$account);
          if ($acc) {
            $account_no = $acc[0]['account_no'];
            $date_added = $acc[0]['date_created'];
          }
        }else{
          $acc = $CI->common_database->get_data("sp_users",1,false,'email',$account);
          if ($acc) {
            $account_no = $acc[0]['account_no'];
            $date_added = $acc[0]['date_added'];
          }
        }                
        if (isset($account_no)) {
          $subs = $CI->common_database->get_data("sp_subscriptions",1,false,'account',$account_no,'status',1);          
          if (!$subs) {
            if (($date_added + $trial) >= time() ) {
               $r['status'] = true;
               $r['m']      = 'trial';
               $r['trial_days'] = round((($date_added + $trial) - time())/$single_day);
            }else{
              $r['status'] = false;
              $r['m']      = 'No subscription';
            }
          }else{
            $sub_type = $subs[0]['type'];
            $sub_date = $subs[0]['date'];
            $today    = time();
            $diff     = ($today - $sub_date);

            if ($sub_type == 'monthly') {
              $r['due_time'] = ($sub_date + $month) - $today;
              if (($sub_date + $month) < $today) {                
                $due = true;                 
              }                        
            }elseif($sub_type == 'semi'){
              $r['due_time'] = ($sub_date + $semi) - $today;
              if (($sub_date + $semi)  < $today) {
                $due = true;                
              }
            }elseif($sub_type == 'annually'){
              $r['due_time'] = ($sub_date + $year) - $today ;
              if (($sub_date + $year)  < $today) {
                $due = true;                 
              }
            }else{
              $r['due_time'] = 0;
              $due = true;
            }
            if ($due) {              
              if (($date_added + $trial) < time()) {
                $sb['status'] = 0;
                $CI->common_database->update("sp_subscriptions",$sb,'account',$account_no);
                $r['status'] = false;
                $r['m'] = 'Payment due';                
              }else{
                $r['status'] = true;
                $r['m'] = 'No payment needed';
              }
            }else{
              $r['status'] = true;
              $r['m'] = 'No payment needed';
            }
          }
        }else{
          $r['status'] = false;
          $r['m'] = "Invalid access";
        }
      }else{
        $r['status'] = false;
        $r['m'] = "Invalid access";
      }
      return json_decode(json_encode($r));
   }
   function payer($slug = false,$data = false){
      $CI = & get_instance();
   }  
   static function wallet($account = false){
    $CI = & get_instance();
    $user = $CI->common_database->get_cond("sp_users","email='$account' or account_no='$account'");
    if (!$user) {
    $user = $CI->common_database->get_cond("sp_business","group_identifier='$account' or account_no='$account'");
    }
    if ($user) {
      $account = $user[0]['account_no'];
    }else{
      $account = false;
    }
    if ($account) {
      $ac = $CI->common_database->get_data('sp_wallet',1,false,'account',$account);
      if (!$ac) {      
      }
      if ($ac) {      
        $r['status']  = true;
        $r['balance'] = $ac[0]['balance'];
      }else{
        $data['account'] = $account;
        $data['balance'] = 0;
        $data['last_updated'] = time();
        $CI->common_database->add('sp_wallet',$data);
        $r['status'] = true; 
        $r['balance'] = 0;     
      }
    }else{
      $r['status']  = false;
      $r['balance'] = false; 
    }
    return json_decode(json_encode($r));
   }
   static function subscribe($account,$ac_type,$type){
     $CI = & get_instance();
     if ($ac_type == 'business') {
       $acc = $CI->common_database->get_data("sp_business",1,false,'group_identifier',$account);
     }else{
        $acc = $CI->common_database->get_data("sp_users",1,false,'email',$account);
     }     
     if ($acc && !Wallet::status($account,$ac_type)->status) {
       $acc = $acc[0]['account_no'];
       $due = Wallet::getSubs($ac_type,$type);
       $wallet_balance = Wallet::wallet($acc)->balance;
       if (Wallet::wallet($acc)->status) {
         if($wallet_balance >= $due) {
           $wal_bal = $wallet_balance - $due;
           $w['balance'] = $wal_bal;
           $CI->common_database->update("sp_wallet",$w,'account',$acc);

           $data['status'] = 0;
           $CI->common_database->update("sp_subscriptions",$data,'account',$acc);
           $time = time();
           $data['type'] = $type;
           $data['amount'] = $due;
           $data['status'] = 1;
           $data['date'] = $time;
           $data['account'] = $acc;
           $CI->common_database->add('sp_subscriptions',$data);

           $data = [
            "time" => $time,
            "type" => $type,
            "account" => $acc,
            "amount" => $due,
           ];                     
           wallet::commissions($data);
           $r['status'] = true;
           $r['m'] = 'Subscribed ';
         }else{
          $r['status'] = false;
          $r['m'] = 'Not enough funds in account';
         }
       }else{
        $r['status'] = false;
        $r['m'] = 'No account';
       }
     }else{
        $r['status'] = false;
        $r['m'] = 'There is an active subscription for this account';
     }    
      
     return json_decode(json_encode($r));
   }
   static function userSub($user){
    $CI = & get_instance();
    if (isset($_SESSION['account']) && isset($_SESSION['usermail'])) {
      if ($_SESSION['usermail'] == $_SESSION['account']) {
        $ac = $CI->common_database->get_data('sp_users',1,false,'email',$_SESSION['account']);
      }else{
        $ac = $CI->common_database->get_data('sp_business',1,false,'group_identifier',$_SESSION['account']);
      }
      if ($ac) {
        $ac_no = $ac[0]['account_no'];
        $sub = $CI->common_database->get_data("sp_subscriptions",1,false,'account',$ac_no,'status',1);
        if ($sub) {
          $r['sub'] = $sub[0]['type'];
          $r['date'] = $sub[0]['date'];
          return json_decode(json_encode($r));
        }else{
          return false;
        }
      }else{
        return false;
      }
    }else{
      return false;
    }
   }
   static function commissions($data = false){
    /*$data = [
      "time" => $time,
      "type" => $type,
      "account" => $acc,
      "amount" => $due
     ];*/     
    if ($data) {
      $CI = &get_instance();
      $account   = isset($data['account']) ? $data['account'] : false;
      $type      = isset($data['type']) ? $data['type'] : false;
      $time      = isset($data['time']) ? $data['time'] : false;
      $amount    = isset($data['amount']) ? $data['amount'] : false;      
      if ($account && $amount && $time && $type) {        
        $sub = $CI->common_database->get_data("sp_subscriptions",1,false,"account",$account,"date",$time,false,"type",$type);               
        if ($sub) {     
          $ac_own = wallet::acOwner($account); 
          if ($ac_own) {
            $u_own = $CI->common_database->get_data("sp_users",1,false,"email",$ac_own);
            $ors = "id ";
            if ($u_own) {
              if ($u_own[0]['account_no'] != $account) {
                $a_ =$u_own[0]['account_no']; 
                $ors = "account='$a_' ";
                $bus_acs = $CI->common_database->get_data("sp_business",false,false,"created_by",$ac_own);
                if ($bus_acs) {
                  foreach ($bus_acs as $b_ac) {
                    $i = $b_ac['account_no'];
                    $ors .= " or account='$i' ";
                  }
                }
              }
            }
           }     
          $subs = $CI->common_database->get_cond("sp_subscriptions",$ors);       
          if ($subs) {
            $commissions = wallet::getComm($type,(sizeof( $subs))) * $amount;
          }else{
            $commissions = .3 * $amount;
          }                                      
          if ($commissions) {                                   
            if ($ac_own) {                           
              $refered = $CI->common_database->get_data("sp_referals",1,false,"refered",$ac_own);
              if ($refered) {
                $referer = $refered[0]['referer'];               
                $ch_rep = $CI->common_database->get_data("sp_sales_reps",1,false,"email",$referer);                 
                $agent = false;
                $dealer = false;
                $rep  = false;                
                if ($ch_rep) {
                  $ch_agent = $CI->common_database->get_data("sp_partners",1,false,"email",$referer,"approved",1,false,"blocked",0);            
                  if ($ch_agent) {
                    if ($ch_agent[0]["email"] == $referer) {
                      $agent = [
                        'sales_id' => $sub[0]['id'],
                        'partner' => $ch_agent[0]["email"],
                        'amount' => $commissions,
                        "date" => $time
                      ];
                      wallet::payAgent($agent);
                    }else{
                      $comm  =  $ch_rep[0]['commission'];
                      if ($comm > 1) {
                        $comm = $comm/100;
                      }                     
                      if ((DOUBLE)$comm > 0) {                          
                          $reduced = $commissions - ($commissions * $comm);      
                          $agent = [
                            'sales_id' => $refered[0]['id'],
                            'partner' => $ch_agent[0]["email"],
                            'amount' => $reduced,
                            "date" => $time
                          ];
                          wallet::payAgent($agent);
                          $not_data = [
                            "account" => $ch_agent[0]["email"],
                            "title" => "Commission earned",
                            "content" => "New commission of Ksh. ".$reduced." sale by ".$referer." on ".(date("d-m-Y",$time)),
                            "notified" => "partner",
                            "date_added" => $time,          
                          ];  
                          $CI->common_database->add("sp_notifications",$not_data);/*pay sales rep*/  
                          $reduced = ($commissions * $comm);                     
                          $rep = [
                            'sales_id' => $refered[0]['id'],
                            'partner' => $referer,
                            'amount' => $reduced,
                            "date" => $time
                          ];
                          $CI->common_database->add("sp_earnings",$rep);
                          $not_data = [
                            "account" => $referer,
                            "title" => "Commission earned",
                            "content" => "New commission of Ksh. ".$reduced." sale by ".$referer." on ".(date("d-m-Y",$time)),
                            "notified" => "partner",
                            "date_added" => $time,          
                          ];                          
                          $CI->common_database->add("sp_notifications",$not_data);
                          $ac =  $CI->common_database->get_data("sp_users",1,false,"email",$referer);                          
                          if ($ac) {
                            $ac = $ac[0]['account_no'];
                            $bal = wallet::wallet($ac);
                            if (isset($bal->balance) && $bal->balance >= 0) {
                              $w_data = [
                                "balance" => $bal->balance + $reduced
                              ];
                              $CI->common_database->update("sp_wallet",$w_data,"account",$ac);
                            } 
                          }                                                    
                      }else{
                        $agent = [
                          'sales_id' => $refered[0]['id'],
                          'partner' => $ch_agent[0]["email"],
                          'amount' => $commissions,
                          "date" => $time
                        ];
                        wallet::payAgent($agent);
                      }                      
                    }
                  }
                }
                return false;
              }
              return false;
            }else{
              return false;
            }
          }else{
            return false;
          }
        }
        return false;
      }else{
        return false;
      }
    }else{
      return false;
    }
   }
   static function payAgent($data = false,$n_data = false){
    if ($data) {     
      $CI = &get_instance();
      $agent = $data["partner"];
      $ch_rep = $CI->common_database->get_data("sp_sales_reps",1,false,"email",$agent);
      if($ch_rep && $ch_rep[0]['partner'] != $agent) {
        $d = $data;
        $d['partner'] = $ch_rep[0]['partner'];
        $d['amount'] = .3 * $data['amount'];
        $not_data = [
          "account" => $ch_rep[0]['partner'],
          "title" => "Commission earned",
          "content" => "New commission of Ksh. ".$d['amount']." sale by ".$agent." on ".(date("d-m-Y",$d['date'])),
          "notified" => "partner",
          "date_added" => $data['date'],          
        ];
        $ac =  $CI->common_database->get_data("sp_users",1,false,"email",$ch_rep[0]['partner']);
        if ($ac) {
          $ac = $ac[0]['account_no'];
          $bal = wallet::wallet($ac);
          if (isset($bal->balance) && $bal->balance >= 0) {
            $w_data = [
              "balance" => $bal->balance + $d['amount']
            ];
            $CI->common_database->update("sp_wallet",$w_data,"account",$ac);
          }          
        }
        $CI->common_database->add("sp_earnings",$d);
        $CI->common_database->add("sp_notifications",$not_data);       
      }
      $CI->common_database->add("sp_earnings",$data);
      $ac =  $CI->common_database->get_data("sp_users",1,false,"email",$agent);
      if ($ac) {
        $ac = $ac[0]['account_no'];
        $bal = wallet::wallet($ac);
        if (isset($bal->balance) && $bal->balance >= 0) {
          $w_data = [
            "balance" => $bal->balance + $data['amount']
          ];
          $CI->common_database->update("sp_wallet",$w_data,"account",$ac);
        }          
      }
      if ($n_data) {
        $CI->common_database->add("sp_notifications",$n_data);
      }else{
        $n_data = [
          "account" => $agent,
          "title" => "Commission earned",
          "content" => "New commission of Ksh. ".$data['amount']." sale by ".$agent." on ".(date("d-m-Y",$data['date'])),
          "notified" => "partner",
          "date_added" => $data['date'],          
        ];
        $CI->common_database->add("sp_notifications",$n_data);
      }
    }
    return false;
   }
   static function acOwner($ref = false){
    if ($ref) {      
      $CI = &get_instance();
      $ch_bus = $CI->common_database->get_data("sp_business",1,false,"account_no",$ref);
      if ($ch_bus) {        
        return $ch_bus[0]['created_by'];
      }else{
        $ch_user =$CI->common_database->get_data("sp_users",1,false,"account_no",$ref);
        if ($ch_user) {
          return $ref;
        }else{
          return false;
        }
      }
    }
    return false;
   }
   static function getComm($type = false,$times = false){
    if ($type && $times) {
      if ($type == "monthly") {
        if ($times >= 5&& $times <= 12) {
          return .05;
        }else{
          if ($times == 1) {
            return .3;
          }elseif($times == 2){
            return .2;
          }
          elseif($times == 3){
            return .15;
          }elseif($times == 4){
            return .1;
          }elseif($times == 5){
            return .05;
          }
        }
      }elseif ($type == "semi") {
        if ($times == 1) {
          return .3;
        }elseif($times == 2){
          return .2;
        }else{
          return false;
        }
      }elseif ($type == "annually"){
         if ($times == 1) {
          return .3;
        }else{
          return false;
        }
      }
    }else{
      return false;
    }
   }
} 