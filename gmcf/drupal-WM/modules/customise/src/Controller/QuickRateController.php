<?php
namespace Drupal\customise\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use \Drupal\Core\Url;
use Drupal\Core\Routing\TrustedRedirectResponse;


/**
 * Quick Rate Controller.
 */
class QuickRateController extends ControllerBase {

  /**
   * Initializing the things here.
   */
  public function __construct() {
    global $base_url;
    $this->storage = \Drupal::entityTypeManager()->getStorage("node");
    $this->user_storage = \Drupal::entityTypeManager()->getStorage("user");
    $this->current_user = \Drupal::currentUser();
    $this->current_user_uid = \Drupal::currentUser()->id();
    $this->base_url = $base_url;
  }
  public function quick_rate_landing(Request $request){
      
       $loanx_api_url =   \Drupal\Core\Site\Settings::get('lnx_api_url_pricing');
       $form_info = $request->request->all();
       $email_valid = true;
       if($form_info){
         if (isset($form_info['qrq_purchase_price'])) {
             $active_tab = "purchase";
         } else {
             $active_tab = "refinance";
         }
       }
       else{
             $active_tab = "purchase";
       }
	
      //  $response = \Drupal::httpClient()->get($loanx_api_url, array('headers' => array('Accept' => 'application/json')));
        
       
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $loanx_api_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $data = curl_exec($curl);
        $error_code = curl_getinfo($curl);
        curl_close($curl);

        
       // = (string) $response->getBody();
        $data = json_decode($data,TRUE);
        
        foreach($data['rateList'] as $key => $dat){
           
        //   $needle = preg_replace('#\d.*$#', '', $dat['productName']);
          // $proname = str_replace($needle, "", $dat['productName']);
           
           
            switch($dat['productName']){
               
               case (strpos(strtolower($dat['productName']), '20 year fixed') !== false) :
                   $proname = "20 Year Fixed";
                   break;
               case (strpos(strtolower($dat['productName']), '30 year fixed') !== false) :
                   $proname = "30 Year Fixed";
                   break;
               case (strpos(strtolower($dat['productName']), '25 year fixed') !== false) :
                   $proname = "25 Year Fixed";
                   break;  
               case (strpos(strtolower($dat['productName']), '15 year fixed') !== false) :
                   $proname = "15 Year Fixed";
                   break;
               
               case (strpos(strtolower($dat['productName']), '10 year fixed') !== false) :
                   $proname = "10 Year Fixed";
                   break;
               
               case (strpos(strtolower($dat['productName']), '10/1') !== false) :
                   $proname = "10/1 LIBOR ARM";
                   break;
               case (strpos(strtolower($dat['productName']), '7/1') !== false) :
                   $proname = "7/1 LIBOR ARM";
                   break;
               case (strpos(strtolower($dat['productName']), '5/1') !== false) :
                   $proname = "5/1 LIBOR ARM";
                   break;
               case (strpos(strtolower($dat['productName']), '3/1') !== false) :
                   $proname = "3/1 LIBOR ARM";
                   break;
               default:
                   $proname = $dat['productName'];
                   break;
               
           }
           
           //if(!array_key_exists($dat['interestRate'], $rate_arr[$dat['productName']])){
                $rate_arr[$proname][$dat['interestRate']][] = $dat['points']; 
                
//                $new_data[$proname][$dat['interestRate']] =  array(
//                                                      "LenderProductName"=>$proname,
//                                                      "Rate"=>$dat['interestRate'],
//                                                      "APR"=>$dat['apr'],
//                                                      "MonthlyPayment"=>$dat['monthlyPayment'],
//                                                      "points"=>max($rate_arr[$proname][$dat['interestRate']]),
//                                                      "credit"=>$dat['credit'],
//                                                      "TotalFees"=>$dat['fees']
//                                                     );
//                
                
                
                $rate_arr[$proname][$dat['points']] = array(
                                                            $dat['interestRate'],
                                                            $dat['apr'],
                                                            $dat['monthlyPayment'],
                                                            $dat['credit'],
                                                            $dat['fees'],
                                                            
                                                        ); 

               
               $new_data[$proname][$dat['interestRate']] =  array(
                                                      "LenderProductName"=>$proname,
                                                      "Rate"=>$rate_arr[$proname][max($rate_arr[$proname][$dat['interestRate']])][0],
                                                      "APR"=>$rate_arr[$proname][max($rate_arr[$proname][$dat['interestRate']])][1],
                                                      "MonthlyPayment"=>$rate_arr[$proname][max($rate_arr[$proname][$dat['interestRate']])][2],
                                                      "points"=>max($rate_arr[$proname][$dat['interestRate']]),
                                                      "credit"=>$rate_arr[$proname][max($rate_arr[$proname][$dat['interestRate']])][3],
                                                      "TotalFees"=>$rate_arr[$proname][max($rate_arr[$proname][$dat['interestRate']])][4]
                                                     );
            //}
          
            
            
        }
        
	$finalResult = array("0"=>array("label"=>"Rates","help_text"=>""),"1"=>array("label"=>"APR","help_text"=>""),"2"=>array("label"=>"Monthly Payment","help_text"=>"A regularly scheduled payment which at a minimum includes principal and interest paid for the home loan. The payment amount may or may not include real estate taxes and property insurance."),"3"=>array("label"=>"Credits/Points","help_text"=>"You can pay a higher amount up front (points) in exchange for a lower interest rate."),"4"=>array("label"=>"Fees","help_text"=>"Fees paid at the closing of the transaction."));
	
        $loan_products = array(
                               "1"=>"30 year fixed",
                               "5"=>"25 year fixed",
                               "2"=>"20 year fixed",
                               "3"=>"15 year fixed",
                               "4"=>"10 year fixed",
                               "11"=>"10/1 ARM",
                               "12"=>"7/1 ARM",
                               "13"=>"5/1 ARM",
                               "14"=>"3/1 ARM",
//                               "15"=>"2/1 ARM"
                             );

        $credit_scores = array(
                               "740"=>"Excellent ( >=740 )",
                               "729"=>"Very Good: 720 – 739",
                               "709"=>"Good: 700 – 719",
                               "689"=>"Above Average: 680 – 699",
                               "659"=>"Average: 640 – 679",
                               "629"=>"Fair: 620 – 639",
                               "559"=>"Needs Some Work: 580 – 619",
                               "579"=>"Needs Much Work ( <=579 )",
                             );
        
        $property_types = array("SingleFamily"=>"Single Family Home",
                                "TownHome"=>"TownHome",
                                "Condo"=>"Condominium",
                                "HiRiseCondo"=>"Hi-Rise Condo",
                                "DetachedCondo"=>"Detached Condo",
                                "PuDs"=>"PUD",
                                "Manufactured"=>"Manufactured Home",
                                "Leasehold"=>"Leasehold",
                                "MultiUnit"=>"Multi Family Home"
                                );
        
	$occupency_types = array(
                                 "PrimaryResidence"=>"Primary Home",
                                 "SecondHome"=>"Second Home",
                                 "Investment"=>"Investment Property"
                                );
        
	$settedadminrates = \Drupal::configFactory()->getEditable('demo.settings')->get("quickrates");

        $rates = $new_data;
        
	$rates1 = array();
	foreach($rates as $key => $rate) {
		$newkey = str_replace(" ","_",$key);
             //   $i = 0;
		foreach($rate as $key1=>$rate1) {
			if (in_array($key1,$settedadminrates[$newkey]['rates']['key_row'])) {
				$rates1[$key][] = $rate1;
			}
                    //    $i++;
		}
	}
        
	$results = array();
	foreach($rates1 as $array) {
			foreach($array as $inner) {
				$results[] = $inner;
			}    
	}
	// sort data based on monthly payment
	$rates = array();
	$temp = array();$temp1 = array();$temp2 = array();
		
	foreach($results as $aEntry1) {
		if(!in_array($aEntry1['MonthlyPayment'],$temp)) {
			$temp1[] = $aEntry1;
			$temp[] = $aEntry1['MonthlyPayment'];
		} else {
			$temp2[] = $aEntry1;
		}
	}
				
	$val_temp = array();
	$val_temp = $temp1[0];
	unset($temp1[0]);
	array_push($temp1,$val_temp);
	if(count($temp2) > 0) {
		$result = array_merge($temp1,$temp2);
	} else {
		$result = $results;
	}
	usort($result, function ($a, $b) {
			return strcmp($a["MonthlyPayment"],$b["MonthlyPayment"]);
	});
	foreach($result as $aEntry) {
		$rates[$aEntry['LenderProductName']][] = $aEntry;
	}		 
	$rates_new = array();
	foreach($rates as $key => $rate1) {			
		usort($rate1, function ($a, $b) {
			return strcmp($a["MonthlyPayment"],$b["MonthlyPayment"]);
		});
		$rates_new[$key] = $rate1;
	}
	$success_msg = \Drupal::configFactory()->getEditable('demo.settings')->get("rates_landing_config");
	$success_msg_default = $success_msg['default_success'];
	$loan_terms = array("10"=>"10 Years","15"=>"15 Years","20"=>"20 Years","30"=>"30 Years");

	// get arm disclaimer content
        $alias = \Drupal::service('path.alias_manager')->getPathByAlias('/arm_disclaimer');
		$params = Url::fromUri("internal:" . $alias)->getRouteParameters();
		$entity_type = key($params);
		$node = \Drupal::entityTypeManager()->getStorage($entity_type)->load($params[$entity_type]);
		$arm_disclaimer = $node->get('body')->value;
	return array(      
	  '#theme' 	=> 'quick_rate_landing_submit',
	  '#name' 	=> array('result'=>$finalResult,'loan_products'=>$loan_products,'credit_scores'=>$credit_scores,'property_types'=>$property_types,'occupency_types'=>$occupency_types,'rates'=>$rates_new,'settedadminrates'=>$settedadminrates,'quick_rates'=>$results,'success_msg'=>$success_msg_default,'loan_terms'=>$loan_terms,'hide'=>false,'form_info' => $form_info,'active_tab' => $active_tab,'email_valid'=>$email_valid,'arm_disclaimer'=>$arm_disclaimer),
    );  
  }
  
  public function quick_rate_submit(Request $request) {

        $loanx_api_srch_url =   \Drupal\Core\Site\Settings::get('lnx_api_url_preql_srch');
        $loanx_api_preql_url =   \Drupal\Core\Site\Settings::get('lnx_api_url_preql');
        $loanx_api_pricing_url = \Drupal\Core\Site\Settings::get('lnx_api_url_pricing');
        $email_valid = true;
        $hide = true; 
        $form_info = $request->request->all();
        if (isset($form_info['qrq_purchase_price'])) {
            $active_tab = "purchase";
        } else {
            $active_tab = "refinance";
        }
        if(!$form_info['qrq_customer_firstname'] || !$form_info['qrq_customer_lastname'] 
                || !$form_info['qrq_customer_phone'] || !$form_info['qrq_customer_email']){
            
             $loanx_data1["isDummyLead"] = true;
             $hide = false;
             $form_info['qrq_leadid'] = "f1dc76dc-92ae-4b36-8682-891570de1c0e";
            
        }
        else if($form_info['qrq_leadid'] == 'f1dc76dc-92ae-4b36-8682-891570de1c0e'){
              $hide = true;
              $form_info['qrq_leadid'] = "";
        }
        
        $form_info['qrq_customer_name'] = $form_info['qrq_customer_firstname']." ". $form_info['qrq_customer_lastname'];

        $loanx_data["informationDataType"] = "leadInfo";
        $loanx_data["id"] = "00000000-0000-0000-0000-000000000000";
        $loanx_data1["firstName"] = $form_info['qrq_customer_firstname']?$form_info['qrq_customer_firstname']:"dummyfname";
        
        $loanx_data1["lastName"] =  $form_info['qrq_customer_lastname']?$form_info['qrq_customer_lastname']:"dummylname";
        $loanx_data1["leadMobileNumber"] = preg_replace("/[^0-9]/","",$form_info['qrq_customer_phone']?$form_info['qrq_customer_phone']:"1111111111");
        $loanx_data1["leadEmail"] =  $form_info['qrq_customer_email']?$form_info['qrq_customer_email']:"dummy@goodmortgage.com";
       
        $loanx_data1["loanPurpose"] = $active_tab;
        $loanx_data1["cashOutAmount"] = $form_info['qrq_cash_out']?$form_info['qrq_cash_out']:"";
        $loanx_data1["currentMortgageBalance"] =  $form_info['qrq_current_balance']?$form_info['qrq_current_balance']:"";

        $prop_type = $form_info['qrq_property_type']?$form_info['qrq_property_type']:"SingleFamily";
        $loanx_data1["houseTypeId"] = str_replace(" ", "", $prop_type);

        $occup_type = $form_info['qrq_occupency_type']?$form_info['qrq_occupency_type']:"PrimaryResidence";
        
       $loanx_data1["occupancyTypeId"] =   str_replace(" ", "", $occup_type);
        if($active_tab == "refinance" ){
           $loanx_data1["estimatedPurchaseAmount"] =  $form_info['qrq_home_value'];
           $loanx_data1["CurrentMortgageBalance"] = $form_info['qrq_current_balance'];
        }
        else {
            
           $loanx_data1["estimatedPurchaseAmount"] =  $form_info['qrq_purchase_price'];
           $loanx_data1["downPayment"] = $form_info['qrq_down_payment'];
        }
       $loanx_data1["city"] = $form_info['qrq_city'];
       $loanx_data1["stateCode"] = $form_info['qrq_state'];
       $loanx_data1["zipCode"] =  $form_info['qrq_zipcode'];
       $loanx_data1["creditScoreRange"] =  $form_info['qrq_credit_score'];
       $loanx_data1["isVaLoanType"] =   false;
       $loanx_data1["pushToLoanX"] = true; //$loanx_info_data->pushToLoanX;
       $loanx_data1["requestedProducts"] = implode(",", $form_info['qrq_loan_product']);
       $loanx_data1["status"] = "Assigned";
       $loanx_data1["source"] ="Website";

        if($form_info['qrq_leadid']){
            $loanx_data["id"] = $form_info['qrq_leadid']; //$leadinfo['leadId'];
                      
            $info_data = json_encode($loanx_data1);
            $loanx_data["informationData"]=$info_data;
            $json_body = json_encode($loanx_data);
            
            // update the data              
             
            $ch = curl_init($loanx_api_pricing_url."/".$loanx_data["id"]);                                                                      
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");                                                                     
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_body);  
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                'Content-Type: application/json',                                                                                
                'Content-Length: ' . strlen($json_body))                                                                       
            );                                                                                                                   

            $data = curl_exec($ch);
           $error = curl_getinfo($ch);
            curl_close($ch);
             
            
        }else{
            
            $info_data = json_encode($loanx_data1);
            $loanx_data["informationData"]=$info_data;
            $json_body = json_encode($loanx_data);

            $ch = curl_init($loanx_api_preql_url);                                                                      
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_body);                                                                  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                'Content-Type: application/json',                                                                                
                'Content-Length: ' . strlen($json_body))                                                                       
            );                                                                                                                   
           // curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
            $data = curl_exec($ch);
            
            curl_close($ch);
                       
            if(strtolower($data) == "invalid domain" ||  strtolower($data) == "invalid account" || strtolower($data) == "invalid pattern"){
                $email_valid = false;
                $hide = false;
                goto templ;
            }
            
            
            
            $loanx_data["id"] = str_replace("\"", "", $data);
              
            
             $json_body = json_encode($loanx_data);
             
             
        $ch = curl_init($loanx_api_pricing_url."/".$loanx_data["id"]);                                                                      
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_body);                                                                  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);      
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                   'Content-Type: application/json',                                                                                
                'Content-Length: ' . strlen($json_body))                                                                       
                );                                                                                                                   

        $data = curl_exec($ch);
        curl_close($ch);
             
        }




        
        
        $data_to_be_popltd = json_decode($data,TRUE);
        
        
        templ:
        
        foreach($data_to_be_popltd['rateList'] as $key => $dat){

           switch($dat['productName']){
               
               case (strpos(strtolower($dat['productName']), '20 year fixed') !== false) :
                   $proname = "20 Year Fixed";
                   break;
               case (strpos(strtolower($dat['productName']), '30 year fixed') !== false) :
                   $proname = "30 Year Fixed";
                   break;
               case (strpos(strtolower($dat['productName']), '25 year fixed') !== false) :
                   $proname = "25 Year Fixed";
                   break;  
               case (strpos(strtolower($dat['productName']), '15 year fixed') !== false) :
                   $proname = "15 Year Fixed";
                   break;
               
               case (strpos(strtolower($dat['productName']), '10 year fixed') !== false) :
                   $proname = "10 Year Fixed";
                   break;
               
               case (strpos(strtolower($dat['productName']), '10/1') !== false) :
                   $proname = "10/1 LIBOR ARM";
                   break;
               
               case (strpos(strtolower($dat['productName']), '7/1') !== false) :
                   $proname = "7/1 LIBOR ARM";
                   break;
               
               case (strpos(strtolower($dat['productName']), '5/1') !== false) :
                   $proname = "5/1 LIBOR ARM";
                   break;
               case (strpos(strtolower($dat['productName']), '3/1') !== false) :
                   $proname = "3/1 LIBOR ARM";
                   break;
               
               default:
                   $proname = $dat['productName'];
                   break;
               
           }
           
           //preg_match("/[^\\d*](\\d.*)/", $proname, $matches);
           $rate_arr[$proname][$dat['interestRate']][] = $dat['points']; 
          
           $new_data[$proname][$dat['interestRate']] =  array(
                                                      "LenderProductName"=>$proname,
                                                      "Rate"=>$dat['interestRate'],
                                                      "APR"=>$dat['apr'],
                                                      "MonthlyPayment"=>$dat['monthlyPayment'],
                                                      "points"=>max($rate_arr[$proname][$dat['interestRate']]),
                                                      "credit"=>$dat['credit'],
                                                      "TotalFees"=>$dat['fees']
                                                     );
            
        }
            
        $rates = $new_data;
        

        $loan_products = array(
                               "1"=>"30 year fixed",
                               "5"=>"25 year fixed",
                               "2"=>"20 year fixed",
                               "3"=>"15 year fixed",
                               "4"=>"10 year fixed",
                               "11"=>"10/1 ARM",
                               "12"=>"7/1 ARM",
                               "13"=>"5/1 ARM",
                               "14"=>"3/1 ARM",
//                               "15"=>"2/1 ARM"
                             );
	
    	
	
	
        $finalResult = array("0"=>array("label"=>"Rates","help_text"=>""),"1"=>array("label"=>"APR","help_text"=>""),"2"=>array("label"=>"Monthly Payment","help_text"=>"A regularly scheduled payment which at a minimum includes principal and interest paid for the home loan. The payment amount may or may not include real estate taxes and property insurance."),"3"=>array("label"=>"Credits/Points","help_text"=>"You can pay a higher amount up front (points) in exchange for a lower interest rate."),"4"=>array("label"=>"Fees","help_text"=>"These are the lender fees charged to process an application at this particular rate."));		
        $credit_scores = array(
                               "740"=>"Excellent ( >=740 )",
                               "729"=>"Very Good: 720 – 739",
                               "709"=>"Good: 700 – 719",
                               "689"=>"Above Average: 680 – 699",
                               "659"=>"Average: 640 – 679",
                               "629"=>"Fair: 620 – 639",
                               "559"=>"Needs Some Work: 580 – 619",
                               "579"=>"Needs Much Work ( <=579 )",
                             );
        
        $property_types = array("SingleFamily"=>"Single Family Home",
                                "TownHome"=>"TownHome",
                                "Condo"=>"Condominium",
                                "HiRiseCondo"=>"Hi-Rise Condo",
                                "DetachedCondo"=>"Detached Condo",
                                "PuDs"=>"PUD",
                                "Manufactured"=>"Manufactured Home",
                                "Leasehold"=>"Leasehold",
                                "MultiUnit"=>"Multi Family Home"
                                );
        
	$occupency_types = array(
                                 "PrimaryResidence"=>"Primary Home",
                                 "SecondHome"=>"Second Home",
                                 "Investment"=>"Investment Property"
                                );
        
   
        $results = array();

        foreach ($rates as $array) {
            foreach ($array as $inner) {
                $results[] = $inner;
            }
        }
        // sort data based on monthly payment
        $rates = array();
        $rates1 = array();
        $temp = array();
        $temp1 = array();
        $temp2 = array();

        foreach ($results as $aEntry1) {
            if (!in_array($aEntry1['MonthlyPayment'], $temp)) {
                $temp1[] = $aEntry1;
                $temp[] = $aEntry1['MonthlyPayment'];
            } else {
                $temp2[] = $aEntry1;
            }
        }

        $val_temp = array();
        $val_temp = $temp1[0];
        unset($temp1[0]);
        array_push($temp1, $val_temp);
        if (count($temp2) > 0) {
            $result = array_merge($temp1, $temp2);
        } else {
            $result = $results;
        }
        foreach ($result as $aEntry) {
            $rates[$aEntry['LenderProductName']][] = $aEntry;
        }
        $rates_new = array();
        foreach ($rates as $key => $rate1) {
            usort($rate1, function ($a, $b) {
                return strcmp($a["MonthlyPayment"], $b["MonthlyPayment"]);
            });
            $rates_new[$key] = $rate1;
        }
        $success_msg = \Drupal::configFactory()->getEditable('demo.settings')->get("rates_landing_config");
		$success_msg_selection = $success_msg['qrq_success'];
		// get arm disclaimer content
        $alias = \Drupal::service('path.alias_manager')->getPathByAlias('/arm_disclaimer');
		$params = Url::fromUri("internal:" . $alias)->getRouteParameters();
		$entity_type = key($params);
		$node = \Drupal::entityTypeManager()->getStorage($entity_type)->load($params[$entity_type]);
		$arm_disclaimer = $node->get('body')->value;
        
        return array(
            '#theme' => 'quick_rate_landing_submit',

            '#name' => array('result' => $finalResult, 'hide'=>$hide, 'loan_products' => $loan_products, 'credit_scores' => $credit_scores, 'property_types' => $property_types, 'occupency_types' => $occupency_types, 'rates' => $rates_new, 'form_info' => $form_info, 'quick_rates' => $results, 'active_tab' => $active_tab, 'success_msg' => $success_msg_selection,'leadId'=>$loanx_data["id"],'email_valid'=>$email_valid,'arm_disclaimer'=>$arm_disclaimer),
        );
    }

    public function quick_rate_sort() { 
  
		$finalResult = array("0"=>array("label"=>"Rates","help_text"=>""),"1"=>array("label"=>"APR","help_text"=>""),"2"=>array("label"=>"Monthly Payment","help_text"=>"A regularly scheduled payment which at a minimum includes principal and interest paid for the home loan. The payment amount may or may not include real estate taxes and property insurance."),"3"=>array("label"=>"Credits/Points","help_text"=>"You can pay a higher amount up front (points) in exchange for a lower interest rate."),"4"=>array("label"=>"Fees","help_text"=>"These are the lender fees charged to process an application at this particular rate."));		
		$result = $_POST['rates'];
		$type_sort = $_POST['type_sort'];
		$rates = array();
		$rates1 = array();$temp = array();$temp1 = array();$temp2 = array();
		switch($type_sort) {
			case "LMP":
				foreach($result as $aEntry1) {
					if(!in_array($aEntry1['MonthlyPayment'],$temp)) {
						$temp1[] = $aEntry1;
						$temp[] = $aEntry1['MonthlyPayment'];
					} else {
						$temp2[] = $aEntry1;
					}
				}
				break;
			case "LR":
				foreach($result as $aEntry1) {
					if(!in_array($aEntry1['Rate'],$temp)) {
						$temp1[] = $aEntry1;
						$temp[] = $aEntry1['Rate'];
					} else {
						$temp2[] = $aEntry1;
					}
				}
				break;
			case "LF":
				foreach($result as $aEntry1) {
					if(!in_array($aEntry1['TotalFees'],$temp)) {
						$temp1[] = $aEntry1;
						$temp[] = $aEntry1['TotalFees'];
					} else {
						$temp2[] = $aEntry1;
					}
				}
				break;
		}
		$val_temp = array();
		$val_temp = $temp1[0];
		unset($temp1[0]);
		array_push($temp1,$val_temp);
		if(count($temp2) > 0) {
			$result = array_merge($temp1,$temp2);
		} else {
			$result = $result;
		}
		foreach($result as $aEntry) {
			$rates[$aEntry['LenderProductName']][] = $aEntry;
		}		 
		$rates_new = array();
		foreach($rates as $key => $rate1) {
			switch($type_sort) {
				case "LMP":
						usort($rate1, function ($a, $b) {
							return strcmp($a["MonthlyPayment"],$b["MonthlyPayment"]);
						});
						$rates_new[$key] = $rate1;
						break;
				case "LR":
						usort($rate1, function ($a, $b) {
							return strcmp($a["Rate"],$b["Rate"]);
						});
						$rates_new[$key] = $rate1;
						break;
				case "LF":
						usort($rate1, function ($a, $b) {
							return strcmp($a["TotalFees"],$b["TotalFees"]);
						});
						$rates_new[$key] = $rate1;
						break;
			}
			
		}
		$i = 0;
		$html_data = "";
		foreach($rates_new as $key => $rate) {
			$newkey = str_replace(" ","_",$key);
			if($i == 0) {
				$openclass = "open";
				$openactive = "openactive";
			} else {
				$openclass = "";
				$openactive = "";
			}
			
			$html_data .='<div class="accordion-head '.$openclass.'"><h4>'.$key.'</h4><div class="arrow down"></div></div><div class="accordion-body '.$openactive.'"><div class="table-responsive"><table class="table cal-table"><thead><tr>';
			foreach($finalResult as $key => $val_qrq) {
				$html_data .= '<th>'.$val_qrq["label"].'&nbsp;';
				if (!empty($val_qrq['help_text'])) {
					$html_data .= '<a href="#" data-toggle="tooltip" data-placement="top" title="'.$val_qrq["help_text"].'"><i class="fa fa-info-circle" aria-hidden="true"></i></a>';
				}
				$html_data .= '</th>';
			}
			$html_data .='<th></th></tr></thead><tbody>';
			foreach($rate as $key1=>$rate1) {
				$html_data .= '<tr><td data-title="Rates">'.$rate1["Rate"].'%</td><td data-title="APR">'.$rate1["APR"].'%</td><td data-title="Monthly Payment">$'.$rate1["MonthlyPayment"].'</td><td data-title="Credits/Points">$'.$rate1["credit"].'<span class="small-txt">@'.$rate1["points"].' points</span></td><td data-title="Fees">$'.$rate1["TotalFees"].'</td><td class="text-center"><a class="select-rate btn" href="javascript:void(0)">Select Rate</a></td></tr>';										
			}
			$html_data .= '</tbody></table></div></div>';
			$i++;
		}
		
		echo $html_data;exit;
  }
  public function testimonial_info() {  
	$query = \Drupal::entityTypeManager()->getStorage("node")->getQuery();
    $query->condition('type' ,'testimonials','=')
              ->condition('status' ,1);
    $result = $query->execute();
    $nodes = \Drupal::entityTypeManager()->getStorage("node")->loadMultiple($result);
    foreach($nodes as $node) {
            $nid = $node->get('nid')->value;	  
            $finalResult[$nid]['body'] = $node->get('body')->value;
            $finalResult[$nid]['customer_name'] = $node->get('field_customer_name')->value;
            $finalResult[$nid]['customer_since'] = $node->get('field_customer_since')->value;
    }
	$testimonial_html = "";
	foreach($finalResult as $key => $testimonial) {
		$testimonial_html .= '<li><div class="col-sm-8">'.$testimonial['body'].'</div><div class="col-sm-4"> - '.$testimonial['customer_name'].',<br> Customer since '.$testimonial['customer_since'].'</div></li>';                                                
    }
	echo $testimonial_html;exit;	
  }
  public function products_info() {
            $sid = 'homepage_product';	
            $queue_status = \Drupal::config('entityqueue.entity_queue.'.$sid)->get('status');
            $nodeids = array();
            if($queue_status) {
                    $entity_subqueue = \Drupal::entityManager()->getStorage('entity_subqueue')->load($sid);
                    $items = $entity_subqueue->get('items')->getValue();
                    foreach($items as $item) {
                            $nodeids[] = $item['target_id'];
                    }
            }
			//echo "<pre>";print_r($nodeids);die;

            $finalResult = array();
            if(!empty($nodeids)) {
                    $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nodeids);	
                    foreach($nodes as $node) {
                      if($node->get('status')->value == 1) {
                              $nid = $node->get('nid')->value;	 
                              $finalResult[$nid]['nid'] = $nid;  
                              $finalResult[$nid]['title'] = $node->get('title')->value;      	  
                              $finalResult[$nid]['product_short_desc'] = $node->get('field_product_short_desc')->value;
                              $finalResult[$nid]['product_url'] = $base_url."/products/".$nid;
                              $finalResult[$nid]['product_video_url'] = $node->get('field_product_video_url')->value;
                     }		  
                    }
            }
			//echo "<pre>";print_r($finalResult);die;
			$prod_html = "";
			foreach($finalResult as $key => $prod) {
				$prod_url = $prod["product_url"];
				$onclick = "location.href='".$prod_url."'";
				$prod_html .= '<li><div class="col-item"><div class="header text-center">'.$prod["title"].'</div>'.$prod["product_short_desc"].'<div class="text-center footer"><button class="btn btn-info custom-btn" onclick="'.$onclick.'">Details</button></div></div></li>';
			}
			echo $prod_html;exit;
  }
  
  public function loan_purpose(){

      $current_path = \Drupal::service('path.current')->getPath();
      $api_url = \Drupal\Core\Site\Settings::get('wm_cd_integration_api');
      $redir_url = \Drupal\Core\Site\Settings::get('wm_cd_redirection');
      $loanx_data["informationDataType"] = "leadInfo";
      $loanx_data["id"] = "00000000-0000-0000-0000-000000000000";
      if($current_path == '/purchase'){
          $loan_purp = "Purchase";
      }
      else{
          $loan_purp = "Refinance";
      }
      $loanx_data1['LoanPurpose']= $loan_purp;      
      $info_data = json_encode($loanx_data1);
      $loanx_data["informationData"]=$info_data;
      $jsonbody = json_encode($loanx_data);
      $response = \Drupal::httpClient()->post($api_url, 
                                                    [
                                                     'body' => $jsonbody,
                                                     'headers' => [
                                                                   'Content-Type' => 'application/json'
                                                                  ],
                                                    ]
                );
      $data = str_replace('"', '', $response->getBody());

      return new TrustedRedirectResponse($redir_url."external?targetRoute=prequal&id=".$data); 
        
      
  }
 
}
