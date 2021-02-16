<?php 
  Class Payroll {
    public function __construct() {
      $CI = & get_instance();      
      $CI->load->library('session');
      $CI->load->helper('form');
      $CI->load->model('common_database');      
      $CI->load->library('user_agent');
      $CI->load->helper('url_helper');
      $CI->load->helper('date');            
   }

   static function nhifValue($basic_salary = false){
      $amount = $basic_salary;
      if ($amount) {
         $CI = & get_instance();
         $rates = $CI->config->item('nhif_rates');
         $r_con = $CI->config->item('nhif_rates');
         
         $amnt = $amount;
         for ($i=0; $i < sizeof($rates['level']); $i++) { 
            $rates['level'][$i] = $rates['level'][$i] - 1;
         }
         if ($amnt >= $r_con['level'][(sizeof($rates['level']) - 1)]) {
           $nhif = $rates['amount'][sizeof($rates['amount']) - 1];
         }else{       
           for ($i = 0; $i < sizeof($rates['level']); $i++) { 
             if ($amnt <= $rates['level'][$i]) {
               $nhif = $rates['amount'][$i];
               break;
             }
           }
         }
         return $nhif;
      }else{
         return false;
      }
   }

   static function nssfValue($basic_salary){
    $amount = $basic_salary;
    $CI = & get_instance();
    $minimum_wage = $CI->config->item('minimum_wage');
    $nssf_default = $CI->config->item('nssf_default');
    if ($amount) {
      if ($amount <= $minimum_wage) {
        $nssf = (0.06*6000) + 0.06*($amount - 6000);
        return $nssf;
      }else{
        return $nssf_default;
      }
    }else{
      return false;
    }
   }

   static function grossPay($basic = 0,$bonus = 0){
      return $basic + $bonus;
   }

   static function taxablePay($gross = 0,$nssf = 0){
      return ($gross - $nssf);
   }

   static function getPaye($taxable_pay = false){
    if ($taxable_pay) {
     $CI = & get_instance();
     $amnt = $taxable_pay;
     $rates = $CI->config->item('paye_rates');

     $r_level = $rates['level'];
     $r_increment = $rates['increment'];
     $r_rate = $rates['rate'];

     for ($i=0; $i < sizeof($r_level); $i++) { 
       if ($amnt > $r_level[$i]) {
         $paye = round($r_increment[$i] + ($amnt - $r_level[$i]) * $r_rate[$i],0);
         break;
       }
     }
     return $paye;
    }else{
      return false;
    }
   }

   static function paye($total_paye = 0){
      $CI = & get_instance();
      $paye_val = $CI->config->item("paye_val");

      $r = $total_paye >= $paye_val ? round($total_paye - $paye_val,0) : 0;
      return $r;
   }

   static function netPay($gross = 0,$paye = 0,$nssf = 0,$nhif = 0){
    return $gross - ($paye + $nssf + $nhif);
   }

   static function payrollData($group = false,$month = '',$year = ''){
    if ($group) {      
      $month = $month = '' ? date('m'): $month;      
      $year  = $year == '' ? date('Y') : $year;      
      $CI = & get_instance();
      //add 23 hours before start day of next month

      $_d = 60 * 60 * 23.5; 
      $days = common::month_len($month, $year); 
      $end_month = (strtotime($days."-".$month."-".$year) + $_d);
      $prev_year = $month == 1 ? $year - 1: $year;
      $prev_month = $month == 1 ? 12 : $month - 1;
      $prev_month_days = common::month_len($prev_month, $prev_year);
      $prev = strtotime($prev_month_days."-".$prev_month."-".$prev_year);

      //and start_date>='$prev'

      $emps = $CI->common_database->exec("* from sp_employee_data where group_identifier='$group' and status='active' and start_date<='$end_month' order by id asc");     

      $grp = $CI->common_database->get_data('sp_business',1,false,'group_identifier',$group);
      $employees = [];      
      $basic_total        =  0;
      $bonus_total        =  0;
      $gross_total        =  0;
      $taxable_pay_total  =  0;
      $total_paye_        =  0;
      $paye_total         =  0;
      $nssf_all           =  0;
      $nhif_total         =  0;
      $net_pay_total      =  0;
      $company_nssf_total =  0;
      $total_nssf         =  0;    

      if ($emps && $grp) {
        foreach ($emps as $emp) {                 
            $b = $CI->common_database->get_data('sp_bonuses',false,false,'emp_id',$emp['id'],'month',$month,false,"year",$year);
            $bonus = 0;
            if ($b) {
              foreach ($b as $bs) {
                $bonus += $bs['amount'];
              }
            }
            $basic        =  $emp['basic_salary'];
            $gross        =  self::grossPay($basic,$bonus);
            $nssf         =  self::nssfValue($basic);
            $taxable      =  self::taxablePay($gross,$nssf); 
            $total_paye   =  self::getPaye($taxable);
            $paye         =  self::paye($total_paye);
            $nhif         =  self::nhifValue($basic);
            $net_pay      =  self::netPay($gross,$paye,$nssf,$nhif);
            $company_nssf =  $nssf;
            $nssf_total   =  $nssf + $company_nssf;
            $pin          =  $emp['pin_number'];
            $name         =  $emp['name'];
            $nssf_number  =  $emp['nssf_number'];
            $nhif_number  =  $emp['nhif_number'];
            $id_number    =  $emp['id_number'];

            $basic_total        +=  $basic;
            $bonus_total        +=  $bonus;
            $gross_total        +=  $gross;
            $taxable_pay_total  +=  $taxable;
            $total_paye_        +=  $total_paye;
            $paye_total         +=  $paye;
            $nssf_all           +=  $nssf;
            $nhif_total         +=  $nhif;
            $net_pay_total      +=  $net_pay;
            $company_nssf_total +=  $company_nssf;
            $total_nssf         +=  $nssf_total;

            array_push($employees, array(
                'name'         => $name,
                'pin_number'   => $pin,
                'id_number'    => $id_number,
                'nssf_number'  => $nssf_number,
                'nhif_number'  => $nhif_number,
                'basic_salary' => $basic,
                'bonus'        => $bonus,
                "start_date"   => $emp["start_date"],                
                'gross_pay'    => $gross,
                'taxable_pay'  => $taxable,
                'total_paye'   => $total_paye,
                'paye'         => $paye,
                'nssf'         => $nssf,
                'nhif'         => $nhif,
                'net_pay'      => $net_pay,
                'company_nssf' => $company_nssf,
                'nssf_total'   => $nssf_total              
            ));       
        }
        $r['group_name'] = $grp[0]['group_name'];
        $r['month'] = $month;
        $r['year'] = $year;
        $r['data'] = $employees;
        $r['totals'] =
               [
                  'basic' => $basic_total,
                  'bonus' => $bonus_total,
                  'gross' => $gross_total,
                  'taxable' => $taxable_pay_total,
                  'total_paye' => $total_paye_,
                  'paye' => $paye_total,
                  'nssf' => $nssf_all,
                  'nhif' => $nhif_total,
                  'net_pay' => $net_pay_total,
                  'company_nssf' => $company_nssf_total,
                  'total_nssf' => $total_nssf
                ];         
        return json_decode(json_encode($r));
      }else{
        $r['group_name'] = $grp[0]['group_name'];
        $r['month'] = $month;
        $r['year'] = $year;
        $r['data'] = false;
        $r['totals'] =
               [
                  'basic' => $basic_total,
                  'bonus' => $bonus_total,
                  'gross' => $gross_total,
                  'taxable' => $taxable_pay_total,
                  'total_paye' => $total_paye_,
                  'paye' => $paye_total,
                  'nssf' => $nssf_all,
                  'nhif' => $nhif_total,
                  'net_pay' => $net_pay_total,
                  'company_nssf' => $company_nssf_total,
                  'total_nssf' => $total_nssf
                ]; 
        return json_decode(json_encode($r));  
      }
    }else{
        $r['group_name'] = $grp[0]['group_name'];
        $r['month'] = $month;
        $r['year'] = $year;
        $r['data'] = $employees;
        $r['totals'] =
               [
                  'basic' => $basic_total,
                  'bonus' => $bonus_total,
                  'gross' => $gross_total,
                  'taxable' => $taxable_pay_total,
                  'total_paye' => $total_paye_,
                  'paye' => $paye_total,
                  'nssf' => $nssf_all,
                  'nhif' => $nhif_total,
                  'net_pay' => $net_pay_total,
                  'company_nssf' => $company_nssf_total,
                  'total_nssf' => $total_nssf
                ]; 
      return json_decode(json_encode($r));  
    }
   }
  }
 ?>