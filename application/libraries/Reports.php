<?php defined('BASEPATH') OR exit('No direct script access allowed');

require('vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet as Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Reports{
    public function __construct() {   
     
     $instance = self::instance();

     $instance->load->library('session');
     $instance->load->helper('form');
     $instance->load->model('common_database');      
     $instance->load->library('user_agent');
     $instance->load->helper('url_helper');
     $instance->load->helper('date'); 
     $instance->load->library("common");
   }
   static function instance(){

      return $ci = get_instance();
   }
   static function payroll($data = false){
     $CI = & get_instance();     
     if ($data) {
       return $CI->load->view("full_payroll",$data,true);
     }            
   }
   static function paye($data = false){
     $CI = & get_instance();
     if ($data) {
      return $CI->load->view("paye_report",$data,true);
    } 
   }
   static function nhif($data = false){
     $CI = & get_instance();
     if ($data) {
       return $CI->load->view("nhif_report",$data,true); 
     }
   }
   static function nssf($data = false){
     $CI = & get_instance();
     if ($data) {
       return $CI->load->view("nssf_report",$data,true); 
     }
   }

   static function download_report($type = false, $kind = false, $data = false){

      if ($type && $kind && $data) {
         $excel_data = self::excel($type,$kind,$data);

         if ($excel_data) {
            if (common::config("external_download")) {
               
               self::external_download(
                  json_encode([
                     "type" => $type,
                     "kind" => $kind,
                     "data" => $data                                           
                  ]),                        
                  json_encode($excel_data)
               );
            }else{                   
              self::download_excel($excel_data["filename"], $excel_data["sheet_data"], $excel_data["cols"]);
            }
         }else{
 
          show_404();
         }
      }else{
         show_404();
      }
   }
   public static function excel($report = 'income',$type = 'personal',$data_ = false){             
       $alpha = [['name'=>'A'],['name'=>'B'],['name'=>'C'],['name'=>'D'],['name'=>'E'],
      ['name'=>'F'],['name'=>'G'],['name'=>'H'],['name'=>'I'],['name'=>'J'],
      ['name'=>'K'],['name'=>'L'],['name'=>'M'],['name'=>'N'],['name'=>'O'],
      ['name'=>'P'],['name'=>'Q'],['name'=>'R'],['name'=>'S'],['name'=>'T'],
      ['name'=>'U'],['name'=>'V'],['name'=>'W'],['name'=>'X'],['name'=>'Y'],
      ['name'=>'Z']];
      $headers = self::getHeaders($report);
      $excel_data = [];      
      if (sizeof($headers) > 0) {
        $cols = sizeof($headers);          
         for ($i=0; $i < $cols; $i++) {            
          $excel_data[$i] = [];
          $excel_data[$i]['name'] = $alpha[$i]['name'];
          $excel_data[$i]['values'] = [];                    
         }  
            //set report headers
         $c = [];
         $c['index'] = 1;
         $c['value'] = '';
         $c['styles'] = 'bold';
         array_push($excel_data[2]['values'], $c);
              //set report name
         $c = [];
         $c['index'] = 2;
         $c['value'] = $data_['name'];
         $c['styles'] = 'bold';
         array_push($excel_data[0]['values'], $c);
              //set space betwoon header and first value
         $c = [];
         $c['index'] = 3;
         $c['value'] = '';
         $c['styles'] = 'bold';
         array_push($excel_data[2]['values'], $c);
              //set report headers
         for ($i=0; $i < $cols; $i++) { 
          $c = [];
          $c['index'] = 4;
          $c['value'] = $headers[$i];
          $c['styles'] = 'bold';
          array_push($excel_data[$i]['values'], $c);
         }      
         $data = self::getData($report,$type,$data_);      
         if (sizeof($data) > 0) {
          for ($i = 5; $i < (sizeof($data) + 5); $i++) { 
           for ($x=0; $x < $cols; $x++) { 
            $v = [];
            $v['index']  = $i;
            $v['value']  =  $data[$i - 5]['data'][$x];
            $v['styles'] =  $data[$i - 5]['styles'];           
            array_push($excel_data[$x]['values'], $v);
           }
          }
         }  //exit(1234);   

         return ["filename" => $data_['name'] , "sheet_data" => $excel_data, "cols" => $cols];
      }      
      return false;   
   }
   static function download_excel($name = "", $sheet_data = [], $cols = 0){
      if (is_array($sheet_data) && sizeof($sheet_data) > 0) {
         $excel_data = $sheet_data;
         $filename = $name;      

         $spreadsheet = new Spreadsheet;   
         $sheet = $spreadsheet->getActiveSheet(); 
         for ($i=0; $i < $cols; $i++) { 
           for ($x=0; $x < sizeof($excel_data[$i]['values']); $x++) { 
                $cell = $excel_data[$i]['name'].$excel_data[$i]['values'][$x]['index'];
                $sheet->setCellValue($cell, $excel_data[$i]['values'][$x]['value']);
                if ($excel_data[$i]['values'][$x]['styles'] != 'none') {
                 $style = [
                  'font' => [
                      'bold' => true,
                   ]
                 ];
                $sheet->getStyle($cell)->applyFromArray($style);
               }
               /*$sheet->getStyle($cell)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);*/
            }
         } 
         for ($i=1; $i < $cols; $i++) { 
          $name = $excel_data[$i]['name'];
          $sheet->getColumnDimension($name)->setAutoSize(true);
         }
         $writer = new Xlsx($spreadsheet);
         

         //$writer->save("media/receipts/".$filename);
         header('Content-Type: application/vnd.ms-excel');
         header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
         header('Cache-Control: max-age=0');

         $writer->save('php://output');
      }
      else{
         show_404();
      }
   }
   static function getTitle($title = "networth"){
      $networth = " Networth Statement";
      $sfp  = " Statement of Financial Position";
      $income = "Statement of Comprehensive Income";
      $paye = " Paye Schedule";
      $nhif = " NHIF Schedule";
      $nssf = " NSSF Schedule";
      $payroll = " Payroll Report";
      $g_ledger = " General Ledger";
      $trial = " Trial Balance ";

      return isset($$title) ? $$title : "";
   }
   static function getHeaders($report = false, $data = ["limit" => 30]){
     if ($report) {
       $report = strtolower($report);

       $income  = ['','Budget','Actual','Varriance'];
       $sfp     = ['','','Amount/Value'];
       $payroll = ['','Name','Basic Pay','Bonus','Gross Pay', 'Taxable Pay','Total PAYE','PAYE','NSSF','NHIF','Net Pay', 'NSSF Company Pay','NSSF Total'];
       $paye    = ['','Name','Pin No.','Gross Pay','NSSF','Taxable Pay','Net Paye'];
       $nhif    = ['','Name','ID No.','NHIF No.','Amount'];
       $nssf    = ['','Name','Pin No.','ID No.','NSSF No.','AMOUNT'];
       $trial   = ['', "Debit", "Credit"];
       $g_ledger = ['Category', "Item/Description", "Debit", "Credit"];
       if ($report == "aging_list") {
          $limit = $data["limit"];
          $days = 0;      
          $r = ["Client Name", "Total"];
          $cols = common::config("aging_list_cols") ? common::config("aging_list_cols") : 3;
          while ($days < ($limit * $cols)) {
             $days += $limit;
             $t = $days == $data["limit"] ? "0 - ".$limit." days" : ($days - $data["limit"] + 1)." - ". $days." days";
             array_push($r, $t);
          }
          array_push($r, (($limit * $cols) + 1)."+ days");
          $aging_list = $r;
       }


       return isset($$report) ? $$report : [];  

     }else{
       return [];
     }
   }
   public function download_request(){
      var_dump("expression");
      exit;
      if (isset($_POST) && isset($_POST["token"]) && isset($_POST["account"]) && isset($_POST["data"]) && isset($_POST["ip"]) && $_POST["ip"] == self::instance()->input->ip_address()) {
            $data = [
               "token" => md5(sha1($_POST["token"].$_POST["account"])),
               "data" => $_POST["data"],
               "ip" => self::instance()->input->ip_address(),
               "time_added" => time()
            ];
            self::instance()->common_database->add("sp_download_requests", $data);

            header("Content-type:application/json");
            echo json_encode(["response" => "success", "status" => true]);
            exit();
      }else{
         show_404();
      }
   }
   public function download(){
      if (isset($_GET["token"])) {
         $ip = self::instance()->input->ip_address();

          //get report data from db
         $data = self::instance()->common_database->get_data("sp_download_requests", 1, false, "token", $_GET["token"], "ip", $ip);

         //check if report teken is still within 10 minutes of allowed download/redownload time
         if ($data && ((time() - $data[0]["time_added"]) <= (60 * 10)) && (time() - $data[0]["time_added"]) >= 0) {
            //decode report data back to array
            $r_data = json_decode($data[0]["data"], true);
            $sheet_data = json_decode($data[0]["sheet_data"], true);

            /*var_dump($sheet_data);
            exit();*/

            //download report
            Reports::download_excel($r_data["type"], $r_data["kind"], $r_data["data"]);
            /*should probably delete the records after download is done*/
         }else{
            echo "Sorry, your access token for downloading the requested report has expired, kindly try downloading the report again. <br/> Redirecting you back to SpendTrack...";
            sleep(10);
         }
      }else{
         show_404();
      }
   }
   static function external_download($data = false, $sheet_data = ""){
      if ($data) {
         $token = time();
         //processed report data
         $d = [
            "token" => $token,
            "account" => $_SESSION["account"],
            "data" => $data,
            "filename" => "",
            "sheet_data" => urlencode($sheet_data),        
            "ip" => self::instance()->input->ip_address()
         ];      
         //var_dump($d["sheet_data"]);
         

         $sent_token = md5(sha1($token.$_SESSION["account"])); 

         //link to make the actual downlead
         $link = common::config("report_download")."reports/download?token=".$sent_token;

         //link to post data to external downloader
         $post_link = common::config("report_download")."reports/download_request";
         $request = self::send_data($post_link, 200, $d);

         //if posted download request is successful, redirect user to external download site with a download access token     
         //var_dump($request);
         //exit();
         if ($request) {  
             
            redirect($link);
         }else{
            show_404();      
         }
      }
      else{
         show_404();
      }
   }
   static function send_data($url, $expected_code = 200, $post = []){   
      $post_data = "";

      if ((bool)sizeof($post)) {
         foreach ($post as $key => $value) {
            if ($key != $value) {
               $post_data .= $key."=".$value."&";
            }
         }
      }
      
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_HEADER, TRUE);
      curl_setopt($ch, CURLOPT_NOBODY, FALSE); // remove body
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

      if (strlen($post_data) > 0) {
         curl_setopt($ch, CURLOPT_POSTFIELDS,$post_data);
      }


      $head = curl_exec($ch);
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);

      //var_dump("Data starts here -> <br/><br/><br/><br/><br/><br/>".$head."<br/><br/> Code: ".$httpCode);
      //exit();

      return $httpCode == $expected_code ? true : false;
   }
   static function getData($report = false,$type = false,$data = false){
     $CI = & get_instance();
     if ($report && $type) {  
         if (!isset($data['month'])) {
           $data['month'] = date('m');
         }
         if (!isset($data['year'])) {
           $data['year'] = date('y');
         }
         if (!isset($data['start_month'])) {
           $data['start_month'] = date('m');
         }
         if (!isset($data['end_month'])) {
           $data['end_month'] = date('m');
         }     
         $acc = $_SESSION['account'];
         $end_days = cal_days_in_month(CAL_GREGORIAN,(int)$data['end_month'],(int)$data['year']);
         $end_month   = strtotime($end_days."-".$data['end_month']."-".$data['year']);
         $start_month = strtotime("01-".$data['start_month']."-".$data['year']); 
         $bus = $CI->common_database->get_data('sp_business',1,false,'group_identifier',$_SESSION['account']);
           /*echo date("d-m-y",$end_month);
           exit();  */   
         if ($type == 'business' || $report == 'sfp') {                 
             $d = payroll::payrollData($_SESSION['account'],$data['month'],$data['year']);          
             if ($d) {
               $pay_members = $d->data;
               $totals = $d->totals;
             }else{
               $pay_members = [];
               $totals = [];
             }
             if($d){
               $cols = sizeof(self::getHeaders($report));
               $header    = self::getHeaders($report);
               $data = [];                    
               if ($report == 'income') {
                 $vat_val = $CI->config->item("vat");
                 $income_tax_val = $CI->config->item("income_tax");
                 $vat_in    = 0;
                 $income = $CI->common_database->get_data('sp_income',false,false,'account',$_SESSION['account']);
                 $incomes = [];
                 $inc_total = 0;
                 array_push($incomes, ['data' => ['','','','',''],'styles'=> 'bold']);
                 array_push($incomes, ['data' => ['Revenue','','','',''],'styles'=> 'bold']); 
                 $o_ins = [];
                 $o_ins_tot = 0; 
                 $others = []; 
                 if ($income) {
                  foreach ($income as $in) {
                   $cat = $CI->common_database->get_data('sp_categories',1,false,'id',$in['category']);
                   if ($cat) {
                     $cat = $cat[0]['name'];
                   }else{
                     $cat = '';
                   }
                   if (strtotime($in['date']) >= $start_month && strtotime($in['date']) <= $end_month) {
                    if ($in['taxed'] == 'true') {
                     $vat_in += $vat_val * $in['amount'];
                     $amnt = $in['amount'] - ($vat_val * $in['amount']);           
                   }else{
                     $amnt = $in['amount'];
                   }
                   if ($in['type'] == 'business') {                                      
                    $inc_total += $amnt;
                    $v = [];
                    $v['data'] = [$cat,'','',number_format($amnt,2),number_format($amnt,2)];
                    $v['styles'] = 'none';
                    array_push($incomes, $v);
                  }else{
                    $o_ins_tot += $amnt;
                    $v = [];
                    $v['data'] = [$cat,'','',number_format($amnt,2),number_format($amnt,2)];
                    $v['styles'] = 'none';
                    array_push($o_ins, $v);
                  }
                }
              }
            } 
            array_push($incomes, ['data' => ['','','','',''],'styles'=> 'bold']);
            array_push($incomes, ['data' => ['Expenses','','','',''],'styles'=> 'bold']);
            $expenses  = [];
            $exp_tot   = 0;
            $vat_out   = 0;
            $vat_total = 0;
            $expense =  $CI->common_database->get_data('sp_categories',false,false,'account',$_SESSION['account'],'type','expenses');   
            if ($expense) {
             foreach ($expense as $expense) {
               $v = [];            
               $v['styles'] = 'none';            
               $exp = $CI->common_database->get_data('sp_expenses',false,false,'category',$expense['id'],'account',$_SESSION['account']);
               $exp_t = 0;
               if ($exp) {
                foreach ($exp as $expense) {
                 //$v = [];              
                 if (strtotime($expense['date']) >= $start_month && strtotime($expense['date']) <= $end_month) {
                   if ($expense['taxed'] == 'true') {
                     $vat_out += $vat_val * $expense['amount']; 
                     $amnt = $expense['amount'] - ($vat_val * $expense['amount']);     
                   }else{
                     $amnt = $expense['amount'];
                   }                
                   $exp_tot += $amnt;
                   $exp_t   += $amnt;
                   //$v['data'] = ['',$expense['name'],number_format(0,2),number_format($amnt,2),number_format($amnt,2)];
                   //$v['styles'] = 'none';
                   //array_push($expenses, $v);
                 }                
               }
               $v['data'] = [$expense['name'],'','',number_format($exp_tot,1),number_format($exp_tot,1)];
               array_push($expenses, $v);
             }
           }
         }         
         array_push($expenses, ['data' => ['','','','',''],'styles'=> 'bold']);
         $g_profit = $inc_total - $exp_tot; 
         array_push($expenses, ['data' => ['Gross Profit','','','',number_format($g_profit,2)],'styles'=> 'bold']);
         array_push($others, ['data' => ['','','','',''],'styles'=> 'bold']);
         array_push($others, ['data' => ['Other Incomes','','','',''],'styles'=> 'bold']);
         foreach ($o_ins as $other) {
          array_push($others, $other);
        }
        array_push($others, ['data' => ['','','','',number_format($o_ins_tot,2)],'styles'=> 'bold']);
        array_push($others, ['data' => ['','','','',''],'styles'=> 'bold']);
        foreach ($incomes as $income) {
          array_push($data, $income);
        }
        foreach ($expenses as $expense) {
          array_push($data, $expense);
        }
        foreach ($others as $other) {
          array_push($data, $other);
        }
        $vat_payable = $vat_in - $vat_out;         
        $profit_before = ($g_profit + $o_ins_tot);
        $income_tax = $income_tax_val * $profit_before;
        $net = $profit_before - $income_tax;       
        array_push($data,['data' => ['','','','',''],'styles'=> 'bold']);
        array_push($data,['data' => ['Profit Before Tax','','','',number_format($profit_before,2)],'styles'=> 'bold']);
        array_push($data,['data' => ['Income Tax','','','',number_format($income_tax,2)],'styles'=> 'bold']);
        array_push($data,['data' => ['Net Profit','','','',number_format($net,2)],'styles'=> 'bold']); 
        array_push($data,['data' => ['','','','',''],'styles'=> 'bold']);
        array_push($data,['data' => ['VAT in','','','',number_format($vat_in,2)],'styles'=> 'bold']);
        array_push($data,['data' => ['VAT out','','','',number_format($vat_out,2)],'styles'=> 'bold']);
        array_push($data,['data' => ['VAT Payable','','','',number_format($vat_payable,2)],'styles'=> 'bold']);     
        return $data;
      }
      elseif($report == 'sfp'){
       if ($type == 'business') {
         $o_data = self::getProps($_SESSION['account']);
       }      
       $dd = $CI->common_database->get_cond('sp_accounts',"account='$acc'");
       $dda = $CI->common_database->get_cond('sp_props',"account='$acc' group by name");          
       $assets = [];
       $asset_tot = 0;
       $lia_tot = 0;
       $liabilities = [];
       array_push($assets, ['data' => ['Assets','',''],'styles'=> 'bold']);            
       $as = $dd;
       if (is_array($as)) {
         for ($i=0; $i < sizeof($as) ; $i++) { 
           $as_am = $as[$i]['balance'];
           $asset_tot +=  $as_am; 
           $x['data'] = ['',$as[$i]['name'],number_format($as_am,2)];
           $x['styles'] = 'none';
           array_push($assets, $x);      
         }
       }   
       $as = $dda;
       if (is_array($as)) {
         for ($i=0; $i < sizeof($as) ; $i++) { 
           if ($as[$i]['type'] == "asset") {
             $g_asset = $CI->common_database->get_data("sp_props",false,false,"account",$acc,"name",$as[$i]['name'],false,"type","asset");
             $as_am = 0;       
             if ($g_asset) {
               foreach ($g_asset as $asset) {
                 $as_am += $asset['amount'];
               }
             }          
             $asset_tot +=  $as_am; 
             $x['data'] = ['',$as[$i]['name'],number_format($as_am,2)];
             $x['styles'] = 'none';
             array_push($assets, $x);
           }      
         }
       }  
       $stock = common::get_inv_value($acc);    

       $x = [];
       $x['styles'] = 'none';
       $x['data'] = ['',"Stock",number_format($stock,2)];
       array_push($assets, $x); 
       $asset_tot +=  $stock;
       $x = [];

       if (isset($o_data) && $o_data['vat'] < 0) {
         $x = [];
         $asset_tot  += ($o_data['vat'] * (-1));  

         $x['data'] = ['',"VAT receivable",number_format(($o_data['vat'] * (-1)),2)];
         $x['styles'] = 'none';       
         array_push($assets, $x);
       }
       array_push($assets, ['data'=>['Total Assets','',number_format($asset_tot,2)],'styles'=>'bold']);
       array_push($assets, ['data'=>['','',''],'styles'=>'none']);
       array_push($liabilities, ['data'=>['Liabilities','',''],'styles'=>'bold']);
       $as = $dda;
       if (is_array($as)) {
         for ($i=0; $i < sizeof($as) ; $i++) { 
           if ($as[$i]['type'] == "liability") {
             $g_lia = $CI->common_database->get_data("sp_props",false,false,"account",$acc,"name",$as[$i]['name'],false,"type","liability");
             $as_am = 0;       
             if ($g_lia) {
               foreach ($g_lia as $lia) {
                 $as_am += $lia['amount'];
               }
             }          
             $lia_tot +=  $as_am; 
             $x['data'] = ['',$as[$i]['name'],number_format($as_am,2)];
             $x['styles'] = 'none';
             array_push($liabilities, $x);
           }      
         }
       }
       if (isset($o_data) && $o_data['in_tax'] > 0) {
         $x = [];
         $lia_tot  += $o_data['in_tax'];  

         $x['data'] = ['',"Income tax",number_format(($o_data['in_tax']),2)];
         $x['styles'] = 'none';       
         array_push($liabilities, $x);
       }
       if (isset($o_data) && $o_data['vat'] > 0) {
         $x = [];
         $lia_tot  += $o_data['vat'];  

         $x['data'] = ['',"VAT Payable",number_format(($o_data['vat']),2)];
         $x['styles'] = 'none';       
         array_push($liabilities, $x);
       }
       array_push($liabilities, ['data'=>['Total Liabilities','',number_format($lia_tot,2)],'styles'=>'bold']);
       foreach ($assets as $asset) {
         array_push($data, $asset);
       }
       foreach ($liabilities as $liability) {
         array_push($data, $liability);
       }
       $difference = $asset_tot - $lia_tot;
       array_push($data, ['data'=>['','',''],'styles'=>'none']);

       if (isset($o_data)) {
         array_push($data, ['data'=>["Owners' Equity",'',''],'styles'=>'bold']);
         array_push($data, ['data'=>['','Retained Earnings',number_format($o_data['equity'],2)],'styles'=>'none']);
         $paid_in = (float)$bus[0]['paid_in_capital'];
         if ($difference - $o_data['equity'] != 0) {
           $director = $difference - $o_data['equity'] - $paid_in;
         }else{
           $director = 0;
         }
         array_push($data, ['data'=>['',"Paid In Capital",number_format($paid_in,2)],'styles'=>'none']);        
         array_push($data, ['data'=>['',"Director's Account",number_format($director,2)],'styles'=>'none']);                
         array_push($data, ['data'=>["Total Owner's Equity",'',number_format(($paid_in + $o_data['equity']),2)],'styles'=>'bold']);
         array_push($data, ['data'=>['','',''],'styles'=>'none']);
         array_push($data, ['data'=>["Total Liabilities & Owners' Equity",'',number_format(($paid_in + $o_data['equity'] + $lia_tot + $director),2)],'styles'=>'bold']);
       }      
       else{
         array_push($data, ['data'=>['Net','',number_format($difference,2)],'styles'=>'bold']);
       }

       return $data;            
     }elseif($report == 'payroll'){        
       $data = [];
       $i = 1;
       foreach ($pay_members as $member) {          
         $v = [];
         $v['data'] = [$i, $member->name,number_format($member->basic_salary,2),number_format($member->bonus,2),number_format($member->gross_pay,2),number_format($member->taxable_pay,2),number_format($member->total_paye,2),number_format($member->paye,2),number_format($member->nssf,2),number_format($member->nhif,2),number_format($member->net_pay,2),number_format($member->company_nssf,2),number_format($member->nssf_total,2)];
         $v['styles'] = 'none';
         array_push($data, $v);
         $i += 1;
       }
       array_push($data,['styles'=>'bold','data'=>['','','','','','','','','','','','','']]);
       array_push($data, ['styles'=>'bold','data'=>['Totals','',number_format($totals->basic,2),number_format($totals->bonus,2),number_format($totals->gross,2),number_format($totals->taxable,2),number_format($totals->total_paye,2),number_format($totals->paye,2),number_format($totals->nssf,2),number_format($totals->nhif,2),number_format($totals->net_pay,2),number_format($totals->company_nssf,2),number_format($totals->total_nssf,2)]]);
       return $data;
     }elseif($report == 'paye'){
       $data = [];
       $i = 1;
       foreach ($pay_members as $member) {          
         $v = [];
         $v['data'] = [$i,$member->name,$member->pin_number,number_format($member->gross_pay,2),number_format($member->nssf,2),number_format($member->taxable_pay,2),number_format($member->paye,2)];
         $v['styles'] = 'none';
         array_push($data, $v);
         $i += 1;
       }
       array_push($data, ['styles'=>'none','data'=>['','','','','','','']]);
       array_push($data, ['styles'=>'bold','data'=>['Totals','','',number_format($totals->gross,2),number_format($totals->nssf,2),number_format($totals->taxable,2),number_format($totals->paye,2)]]);
       return $data;
     }elseif($report == 'nhif'){
       $data = [];
       $i = 1;
       foreach ($pay_members as $member) {          
         $v = [];
         $v['data'] = [$i,$member->name,$member->id_number,$member->nhif_number,number_format($member->nhif,2)];
         $v['styles'] = 'none';
         array_push($data, $v);
         $i += 1;
       }
       array_push($data, ['styles'=>'none','data'=>['','','','','']]);
       array_push($data, ['styles'=>'bold','data'=>['Totals','','','',number_format($totals->nhif,2)]]);

       return $data;
     }elseif($report == 'nssf'){
       $data = [];
       $i = 1;
       foreach ($pay_members as $member) {          
         $v = [];
         $v['data'] = [$i,$member->name,$member->pin_number,$member->id_number,$member->nhif_number,number_format($member->nssf,2)];
         $v['styles'] = 'none';
         array_push($data, $v);
         $i += 1;
       }
       array_push($data, ['styles'=>'none','data'=>['','','','','','']]);
       array_push($data, ['styles'=>'bold','data'=>['Totals','','','','',number_format($totals->nssf,2)]]);

       return $data;
     }elseif($report == "g_ledger"){
       $ac_no = $_SESSION["account"];
       $g_data = self::ledger_data($ac_no,$start_month, $end_month);
       $items = $g_data["items"];
       $debited = $g_data["debited"];
       $credited = $g_data["credited"];
       $data = [];
       $i = 1;
       array_push($data,['styles'=>'bold','data'=>["","","",""]]);

       
       if (is_array($items)) {
          foreach ($items as $item) {          
            $v = [];
            $v['data'] = [$item["name"], "", "", ""];
            $v['styles'] = 'bold';
            array_push($data, $v);
            if (is_array($item["items"])) {
               $x = 1;
               foreach ($item["items"] as $sub) {
                  $v = [];
                  $v["styles"] = "none";
                  $v["data"] = ["", $sub[0], $item["type"] == "dr" ? $sub[1]: "",  $item["type"] == "cr" ? $sub[1]: ""];
                  array_push($data, $v);
               }
            }
            $i += 1;
          }
       }
       array_push($data,['styles'=>'bold','data'=>["","","",""]]);
       array_push($data, ['styles'=>'bold','data'=>["", 'Totals', number_format($debited, 2), number_format($credited, 2)]]);
       return $data;
     }elseif($report == "trial"){
      $r_data = Reports::trialData($_SESSION["account"], $start_month, $end_month);
      $data = [];
      $items = $r_data["items"];      
      $debited = $r_data["debited"];
      $credited = $r_data["credited"]; 
      array_push($data,['styles'=>'bold','data'=>["","",""]]);


      if (sizeof($items) > 0) {
         foreach ($items as $item) {
            $v = [];
            $v['data'] = [$item[0], $item[2] == "dr" ? "Ksh. ".$item[1]: '' , $item[2] == "cr" ? "Ksh. ".$item[1]: '' ];
            $v['styles'] = 'none';
            array_push($data, $v);
         }
         array_push($data,['styles'=>'bold','data'=>["","",""]]);
         array_push($data, ['styles'=>'bold','data'=>['Totals', number_format($debited, 2), number_format($credited, 2)]]);
      }

      return $data;
     }else{
      /*hereee*/
       return [];
     }
   }else{     
      return [];
   }
   }else{
     if ($report == 'income') { 
       $data = [];      
       $income = $CI->common_database->get_data('sp_income',false,false,'account',$_SESSION['account']);
       $incomes = [];
       $inc_total = 0;
       array_push($incomes, ['data' => ['Income','','','',''],'styles'=> 'bold']);     
       if ($income) {
        foreach ($income as $in) {
         $cat = $CI->common_database->get_data('sp_categories',1,false,'id',$in['category']);
         if ($cat) {
           $cat = $cat[0]['name'];
         }else{
           $cat = '';
         }
         $amnt = $in['amount'];
         $inc_total += $amnt;
         $v = [];
         $v['data'] = [$cat,'','',number_format($amnt,2),number_format($amnt,2)];
         $v['styles'] = 'none';
         array_push($incomes, $v);
       }
     } 
     array_push($incomes, ['data' => ['','','','',''],'styles'=> 'bold']);
     array_push($incomes, ['data' => ['Expenses','','','',''],'styles'=> 'bold']);
     $expenses  = [];
     $exp_tot   = 0;    
     $expense =  $CI->common_database->get_data('sp_categories',false,false,'account',$_SESSION['account'],'type','expenses');   
     if ($expense) {
       foreach ($expense as $expense) {
         $v = [];        
         $v['styles'] = 'none';        
         $exp = $CI->common_database->get_data('sp_expenses',false,false,'category',$expense['id'],'account',$_SESSION['account']);
         $ex_t = 0; 
         if ($exp) {
          foreach ($exp as $expense) {
              // $v = [];
           $amnt = $expense['amount'];
           $exp_tot += $amnt;
           $ex_t += $amnt;
              // $v['data'] = ['',$expense['name'],number_format(0,2),number_format($amnt,2),number_format($amnt,2)];
               //$v['styles'] = 'none';
               //array_push($expenses, $v);
         }
       }
       $v['data'] = [$expense['name'],'','',number_format($ex_t,1),number_format($ex_t,1)];
       array_push($expenses, $v);
     }

   } 
   array_push($expenses, ['data' => ['','','','',''],'styles'=> 'bold']);
   $g_profit = $inc_total - $exp_tot; 
   array_push($expenses, ['data' => ['Net Income','','','',number_format($g_profit,2)],'styles'=> 'bold']);      
   foreach ($incomes as $income) {
    array_push($data, $income);
   }
   foreach ($expenses as $expense) {
    array_push($data, $expense);
   }
   return $data;
   }
   }
   }else{
     return [];
   }
   }
   public static function getProps($ac = false){
     ///get start dates
     //using_defaults  

     $CI = & get_instance();
     if ($ac) {
          $acc = $ac;
        }else{
         $acc = $_SESSION['account'];
       }
       $account_data = $CI->common_database->get_data('sp_business',1,false,'group_identifier',$acc);
       if (!$account_data) {
         exit("Oops! Something went wrong");
       }
       $end_days    = cal_days_in_month(CAL_GREGORIAN,date('m'),date('Y'));
       $end_month   = strtotime($end_days."-".date('m')."-".date('Y'));
       if (is_numeric($account_data[0]['year_start'])) {
        $start_month = strtotime("01-".$account_data[0]['year_start']."-".date('Y'));
      }else{
        $start_month = strtotime("01-".'01'."-".date('Y'));
      }
      $start = date('M','01');
      $end = date("M",$end_month);

      $in = $CI->common_database->get_data('sp_income',false,false,'account',$acc);
      $ins = [];
      $in_tot = 0;
      $o_ins = [];
      $o_ins_tot = 0;   
      $vat_val = $CI->config->item("vat");
      $income_tax_val = $CI->config->item("income_tax");
      $vat_in    = 0;
      if (is_array($in)) {
        for ($i=0; $i < sizeof($in) ; $i++) { 
          if ($in[$i]['type'] == 'business' && strtotime($in[$i]['date']) >= $start_month && strtotime($in[$i]['date']) <= $end_month) {
            $c = $CI->common_database->get_data('sp_categories',1,false,'account',$acc,'id',$in[$i]['category']);
            $ins[$i]['name']  = $c[0]['name'];          
            $ins[$i]['budget'] = 0;

            if ($in[$i]['taxed'] == 'true') {
              $vat_in += $vat_val * $in[$i]['amount'];
              $ins[$i]['value'] = $in[$i]['amount'] - ($vat_val * $in[$i]['amount']);           
            }else{
              $ins[$i]['value'] = $in[$i]['amount'];
            }
            $in_tot += $ins[$i]['value'];
          }
        }

        for ($i=0; $i < sizeof($in) ; $i++) { 
          if ($in[$i]['type'] != 'business' && strtotime($in[$i]['date']) >= $start_month && strtotime($in[$i]['date']) <= $end_month) {
            $c = $CI->common_database->get_data('sp_categories',1,false,'account',$acc,'id',$in[$i]['category']);
            if ($in[$i]['taxed'] == 'true') {
              $vat_in += $vat_val * $in[$i]['amount'];
              $x['value'] = $in[$i]['amount'] - ($vat_val * $in[$i]['amount']);           
            }else{
              $x['value'] = $in[$i]['amount'];
            }
            $x['name']  = $c[0]['name'];
            $x['budget'] = 0;
            array_push($o_ins,$x);
            $o_ins_tot += $x['value'];
          }
        }
      }     
      $ex = $CI->common_database->get_data('sp_categories',false,false,'account',$acc,'type','expenses');
      $ex_tot = 0;
      $exs = [];    
      $vat_out   = 0;
      $vat_total = 0;   
      if (is_array($ex)) {
        for ($i=0; $i < sizeof($ex) ; $i++) { 
          $exs[$i]['name'] = $ex[$i]['name'];
          $vs = $CI->common_database->get_data('sp_expenses',false,false,'account',$acc,'category',$ex[$i]['id']);
          $amnt = 0;
          if ($vs) {
            for ($v=0; $v < sizeof($vs) ; $v++) { 
              if (strtotime($vs[$v]['date']) >= $start_month && strtotime($vs[$v]['date']) <= $end_month) {
                $exs[$i]['values'][$v]['name'] = $vs[$v]['name'];
                $exs[$i]['values'][$v]['budget'] = 0;
                if ($vs[$v]['taxed'] == 'true') {
                  $vat_out += $vat_val * $vs[$v]['amount']; 
                  $exs[$i]['values'][$v]['value'] = $vs[$v]['amount'] - ($vat_val * $vs[$v]['amount']);     
                }else{
                  $exs[$i]['values'][$v]['value'] = $vs[$v]['amount'];
                }
                $ex_tot += $exs[$i]['values'][$v]['value'];
                $amnt   +=  $exs[$i]['values'][$v]['value'];
              }
            }
          }
          $exs[$i]['amnt'] = $amnt;
        } 
      }
      if (sizeof($exs) < 1) {
        $exs = [];
      }
      $vat_payable = $vat_in - $vat_out;    
      $profit = $in_tot - $ex_tot;
      $profit_before = ($profit + $o_ins_tot);
      $income_tax = $income_tax_val * $profit_before;
      $net = $profit_before - $income_tax;
      $data['vat'] = $vat_payable;
      $data['equity'] = $net;
      $data['in_tax'] = $income_tax;
      return $data;
   }
   static function getTable($table_data = [], $head_data = []){
    $table = '';
    if (is_array($table_data) && sizeof($table_data) > 0 && is_array($head_data) && sizeof($head_data) > 0) {     
      $table .= '<table>
                  <tbody>';
      $table_data = json_decode(json_encode($table_data));

      $rows = $table_data->rows;
      $currency = $table_data->currency;
      $headers = $head_data;
      foreach ($rows as $row) {
        $table .= '<tr class="main-row">';
         if (is_array($row)) {
            for ($i=0; $i < sizeof($row) ; $i++) { 
              if (isset($row[$i]->colspan)) {
                $colspan = $row[$i]->colspan > 0 ? $row[$i]->colspan : 1;         
              }else{            
                $colspan = $headers[$i]['colspan'];
              }
              if (isset($row[$i]->type) && $row[$i]->type == "number") {
                $value = $table_data->currency." ".number_format($row[$i]->value,2);
              }else{
               if (isset($row[$i]->value)) {
                 $value = $row[$i]->value;
               }else{
                 $value = "";
               }
              }
              if (isset($row[$i]->class)) {
                $class = $row[$i]->class;
              }else{
               $class = "";
              }
              $table .= '<td tabindex="-1" colspan="'.$colspan.'" class="'.$class.'">'.$value.'</td>';                      
            }
         }
        $table .= '</tr>';
      }
      $table .= '</tbody>
                </table>';
    }
    return $table;
  }
  static function get_margin($headers = false){
    $cols = 0;
    $c = [];
    foreach ($headers as $h) {
     $r = [
        "type" =>  "text",
        "class" => "bold-td center no-value",
        "value" => "", 
        "colspan" => $h["colspan"]           
      ];
      array_push($c, $r);
    }    
    return $c;
  }
   static function ledger_data($account = false, $start_month = false, $end_month = false){
      $ac_no = $account;
      $assets = self::instance()->common_database->exec("* from sp_props where account='$ac_no' and type='asset' and (date_added >= '$start_month' and date_added <= '$end_month') order by id desc");
      $lias = self::instance()->common_database->exec("* from sp_props where account='$ac_no' and type='liability' and (date_added >= '$start_month' and date_added <= '$end_month') order by id desc");
      $acs = self::instance()->common_database->exec("* from sp_accounts where account='$ac_no' and balance>0 and (date_added <= '$end_month') order by id asc");
      $cats = self::instance()->common_database->exec("* from sp_categories where account='$ac_no' order by id desc");
      $debited = 0;
      $credited = 0;

      $ins   = [];
      $exps  = [];
      $items = [];

      if ($acs) {
         $acs_v = [
                  "type" => "cr",
                  "name" => "Accounts",
                  "total" => 0,
                  "items" => []
               ];
         foreach ($acs as $ac) {
            array_push($acs_v["items"], [ucfirst($ac["name"]), number_format($ac["balance"], 2)]);
            $credited += $ac["balance"];
            $acs_v["total"] += $ac["balance"]; 
         }
         array_push($items, $acs_v);
      }
      if ($assets) {
         $ass_v =  [
                  "type" => "cr",
                  "name" => "Assets",
                  "total" => 0,
                  "items" => []
               ];
         foreach ($assets as $asset) {
            array_push($ass_v["items"], [ucfirst($asset["name"]), number_format($asset["amount"], 2)]);
            $credited += $asset["amount"];
            $ass_v["total"] += $asset["amount"]; 
         }
         array_push($items, $ass_v);
      }
      if ($lias) {
         $lia_v =  [
                  "type" => "dr",
                  "name" => "Liabilities",
                  "total" => 0,
                  "items" => []
               ];
         foreach ($lias as $lia) {
            array_push($lia_v["items"], [ucfirst($lia["name"]), number_format($lia["amount"], 2)]);
            $debited += $lia["amount"];
            $lia_v["total"] += $lia["amount"]; 
         }
         array_push($items, $lia_v);      
      }
      foreach ($cats as $i => $cat) {
        $c = $cat["id"];
        $in_tot = 0;
        $ex_tot = 0;

        $c_item["name"] = $cat["name"];
        $c_item["total"] = 0;
        $c_item["items"] = [];

        if ($cat["type"] == "income") {                    
          $c_item["name"] = $cat["name"]. " - Revenue ";
          $c_item["type"] = "cr";
          $in = self::instance()->common_database->exec("* from sp_income where account='$ac_no' and (date_added >= '$start_month' and date_added <= '$end_month') and category='$c' order by id asc");           
          if ($in) {       
            foreach ($in as $in_i) {
              array_push($c_item["items"], [($in_i["description"] != "" ? $in_i["description"] : "Unlabelled Item"), number_format($in_i["amount"], 2)]);
              $credited += $in_i["amount"]; 
              $c_item["total"] += $in_i["amount"];
            }         
          }
        }elseif($cat["type"] == "expenses"){
          $c_item["type"] = "dr";
          $c_item["name"] = $cat["name"]. " - Expenditure";
          $ex = self::instance()->common_database->exec("*, amount as amount from sp_expenses where account='$ac_no' and (date_added >= '$start_month' and date_added <= '$end_month') and category='$c' order by id asc");
          if ($ex) {
             foreach ($ex as $ex_i) {
                array_push($c_item["items"], [($ex_i["description"] != "" ? $ex_i["description"] : "Unlabelled Item"), number_format($ex_i["amount"], 2)]);
             }      
             $debited += $ex_i["amount"];
             $c_item["total"] += $ex_i["amount"];
          }
        }

        if (sizeof($c_item["items"]) > 0) {
           array_push($items, $c_item);
        }
      } 
      return ["debited" => $debited, "credited" => $credited, "items" => $items];  
   }
   static function trialData($ac_no = false, $start_month, $end_month){
      $assets = self::instance()->common_database->exec("* from sp_props where account='$ac_no' and type='asset' and (date_added >= '$start_month' and date_added <= '$end_month') order by id desc");
      $lias = self::instance()->common_database->exec("* from sp_props where account='$ac_no' and type='liability' and (date_added >= '$start_month' and date_added <= '$end_month') order by id desc");
      $acs = self::instance()->common_database->exec("* from sp_accounts where account='$ac_no' and balance>0 and (date_added <= '$end_month') order by id asc");
      $cats = self::instance()->common_database->exec("* from sp_categories where account='$ac_no' order by id desc");
      $debited = 0;
      $credited = 0;

      $ins   = [];
      $exps  = [];
      $items = [];

      if ($acs) {
         foreach ($acs as $ac) {
            array_push($items, [ucfirst($ac["name"]), number_format($ac["balance"], 2), "cr"]);
            $credited += $ac["balance"];
         }
      }      
      if ($assets) {
         foreach ($assets as $asset) {
            array_push($items, [ucfirst($asset["name"]), number_format($asset["amount"], 2), "cr"]);
            $credited += $asset["amount"];
         }
      }
      if ($lias) {
         foreach ($lias as $lia) {
            array_push($items, [ucfirst($lia["name"]), number_format($lia["amount"], 2), "dr"]);
            $debited += $lia["amount"];
         }
      }
      foreach ($cats as $i => $cat) {
        $c = $cat["id"];
        $in_tot = 0;
        $ex_tot = 0;

        if ($cat["type"] == "income") {
          $in = self::instance()->common_database->exec("*, sum(amount) as amount from sp_income where account='$ac_no' and (date >= '$start_month' and date <= '$end_month') and category='$c' group by category order by id asc");  
          if ($in) {
            array_push($ins, [ucfirst($cat["name"]), number_format($in[0]["amount"], 2)]);
            array_push($items, [ucfirst($cat["name"]), number_format($in[0]["amount"], 2), "cr"]);
            $credited += $in[0]["amount"];
          }
        }elseif($cat["type"] == "expenses"){
          $ex = self::instance()->common_database->exec("*, sum(amount) as amount from sp_expenses where account='$ac_no' and (date_added >= '$start_month' and date_added <= '$end_month') and category='$c' group by category order by id asc");
          if ($ex) {
             array_push($exps, [ucfirst($cat["name"]), number_format($ex[0]["amount"], 2)]);
             array_push($items, [ucfirst($cat["name"]), number_format($ex[0]["amount"], 2), "dr"]);
             $debited += $ex[0]["amount"];
          }
        }
      }       
      return ["items" => $items, "debited" => $debited, "credited" => $credited];  
   }
} 