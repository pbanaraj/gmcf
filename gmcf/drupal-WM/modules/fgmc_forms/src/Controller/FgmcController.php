<?php
namespace Drupal\fgmc_forms\Controller;

use Drupal\fgmc_forms\Form\BannerConfigForm;
use Drupal\fgmc_forms\Form\ProductDetailsConfigForm;
use Drupal\fgmc_forms\Form\WhyusDetailsConfigForm;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * key Content Type Controller.
 */
class FgmcController extends ControllerBase {

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
   /**
   * View home page .
   */
  public function homepagepanel() {
    $is_permission = \Drupal::currentUser()->hasPermission('access content fgmc user');
	$section_keys = \Drupal::config('demo.settings')->get('section_keys');
	//echo "<pre>";print_r($section_keys);die;
	foreach($section_keys as $key=>$val) { 
		if($val['section_enable'] == 1) {
			$block = \Drupal\block\Entity\Block::load($val['section_key']);
			$blocks_info[$key] = \Drupal::entityManager()
			  ->getViewBuilder('block')
			  ->view($block);
			$blocks_info[$key]['#weight'] = $key;
		}
	}
	//echo "<pre>";print_r($blocks_info);die;
	$build = array(
          '#theme' => 'homepagepanel',
          '#title' => "",
		  '#data' => array('blocks_info'=>$blocks_info,'is_permission'=>$is_permission,'baseURL'=>$this->base_url)
    );
	return $build;
  }
  /**
   * list of block ids .
   */
  public function viewblocklist() { 
	$prefix = $_REQUEST['q'];
	$ids = \Drupal::entityQuery('block')->condition('id', $prefix , 'CONTAINS')->execute();
	foreach($ids as $key=>$val) {
		$results[] = array('value'=>$val,'label'=>$val);
	}
	return new JsonResponse($results);
  }
  /**
   * remove section from config .
   */
  public function removesection() {
	$basepath = \Drupal::request()->getBasePath();
	$key = $_REQUEST['key'];
	$section_keys = \Drupal::config('demo.settings')->get('section_keys');
	unset($section_keys[$key]);
	\Drupal::configFactory()->getEditable('demo.settings')->set("section_keys", $section_keys)->save();
	$response = new RedirectResponse($basepath.'/admin/sectionsConfig');
	$response->send();
  }
   /**
  * Edit content type
  **/
  public function editbannerconfig(){
    $bannerconfigform = new BannerConfigForm(); 
    $bannerconfigform->setFormType('edit');
    $editbannerconfigform = \Drupal::formBuilder()->getForm($bannerconfigform);
    $banner_config = \Drupal::configFactory()->getEditable('demo.settings')->get("banner_config");
	//echo "<pre>";print_r($banner_config);die;
   // Populate form fields values with $node object.
    $editbannerconfigform['banner_title']['value']['#value'] = $banner_config['banner_title'];
    $editbannerconfigform['banner_subtitle']['value']['#value'] = $banner_config['banner_subtitle']; 
	$editbannerconfigform['banner_image']['#default_value'] = $banner_config['banner_id']; 
    return [
    '#theme' => 'editbannerconfig',
    '#data' => array('form' => $editbannerconfigform)
  ];
  }
  public function adminrates() {
       
	$title = t('Admin Rates');
	//$uri = "http://localhost:8080/getjsoninfo1.php";
    //$response = \Drupal::httpClient()->get($uri, array('headers' => array('Accept' => 'text/plain')));    
	//$stuff = array("30 Year Fixed"=>array("0"=>array("LenderProductName"=>"30 Year Fixed","Rate"=>"3.565","APR"=>"3.615","MonthlyPayment"=>"1700","points"=>"2204","credit"=>"2","TotalFees"=>"1500"),"1"=>array("LenderProductName"=>"30 Year Fixed","Rate"=>"3.665","APR"=>"3.715","MonthlyPayment"=>"1900","points"=>"2204","credit"=>"2","TotalFees"=>"1500"),"2"=>array("LenderProductName"=>"30 Year Fixed","Rate"=>"3.565","APR"=>"3.615","MonthlyPayment"=>"1700","points"=>"2204","credit"=>"2","TotalFees"=>"1500")),"20 Year Fixed"=>array("0"=>array("LenderProductName"=>"30 Year Fixed","Rate"=>"3.565","APR"=>"3.615","MonthlyPayment"=>"1700","points"=>"2204","credit"=>"2","TotalFees"=>"1500"),"1"=>array("LenderProductName"=>"30 Year Fixed","Rate"=>"3.565","APR"=>"3.615","MonthlyPayment"=>"1700","points"=>"2204","credit"=>"2","TotalFees"=>"1500"),"2"=>array("LenderProductName"=>"30 Year Fixed","Rate"=>"3.565","APR"=>"3.615","MonthlyPayment"=>"1700","points"=>"2204","credit"=>"2","TotalFees"=>"1500")),"15 Year Fixed"=>array("0"=>array("LenderProductName"=>"30 Year Fixed","Rate"=>"3.565","APR"=>"3.615","MonthlyPayment"=>"1700","points"=>"2204","credit"=>"2","TotalFees"=>"1500"),"1"=>array("LenderProductName"=>"30 Year Fixed","Rate"=>"3.565","APR"=>"3.615","MonthlyPayment"=>"1700","points"=>"2204","credit"=>"2","TotalFees"=>"1500"),"2"=>array("LenderProductName"=>"30 Year Fixed","Rate"=>"3.565","APR"=>"3.615","MonthlyPayment"=>"1700","points"=>"2204","credit"=>"2","TotalFees"=>"1500")),"10 Year Fixed"=>array("0"=>array("LenderProductName"=>"30 Year Fixed","Rate"=>"3.565","APR"=>"3.615","MonthlyPayment"=>"1700","points"=>"2204","credit"=>"2","TotalFees"=>"1500"),"1"=>array("LenderProductName"=>"30 Year Fixed","Rate"=>"3.565","APR"=>"3.615","MonthlyPayment"=>"1700","points"=>"2204","credit"=>"2","TotalFees"=>"1500"),"2"=>array("LenderProductName"=>"30 Year Fixed","Rate"=>"3.565","APR"=>"3.615","MonthlyPayment"=>"1700","points"=>"2204","credit"=>"2","TotalFees"=>"1500")));
        
        $loanx_api_url =   \Drupal\Core\Site\Settings::get('lnx_api_url_pricing');
        $response = \Drupal::httpClient()->get($loanx_api_url, array('headers' => array('Accept' => 'application/json')));
        $data = (string) $response->getBody();
        $data = json_decode($data,TRUE);
        
        foreach($data['rateList'] as $key => $dat){
			
			switch($dat['productName']){
               
               case (strpos(strtolower($dat['productName']), '20 year fixed') !== false) :
                   $proname = "Conforming 20 Year Fixed";
                   break;
               case (strpos(strtolower($dat['productName']), '30 year fixed') !== false) :
                   $proname = "Conforming 30 Year Fixed";
                   break;
               case (strpos(strtolower($dat['productName']), '25 year fixed') !== false) :
                   $proname = "Conforming 25 Year Fixed";
                   break;  
               case (strpos(strtolower($dat['productName']), '15 year fixed') !== false) :
                   $proname = "Conforming 15 Year Fixed";
                   break;
               
               case (strpos(strtolower($dat['productName']), '10 year fixed') !== false) :
                   $proname = "Conforming 10 Year Fixed";
                   break;
               
               case (strpos(strtolower($dat['productName']), '10/1') !== false) :
                   $proname = "Conforming 10/1 LIBOR ARM";
                   break;
               case (strpos(strtolower($dat['productName']), '7/1') !== false) :
                   $proname = "Conforming 7/1 LIBOR ARM";
                   break;
               case (strpos(strtolower($dat['productName']), '5/1') !== false) :
                   $proname = "Conforming 5/1 LIBOR ARM";
                   break;
               case (strpos(strtolower($dat['productName']), '3/1') !== false) :
                   $proname = " Conforming 3/1 LIBOR ARM";
                   break;
               
               default:
                   $proname = $dat['productName'];
                   break;
               
           }
          
           
                $rate_arr[$proname][$dat['interestRate']][] = $dat['points']; 
               
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
            
        }
        
        
	//$data = json_encode($stuff);
        $rates = $new_data;
	//echo "<pre>";print_r($rates);die;
	$settedadminrates = \Drupal::configFactory()->getEditable('demo.settings')->get("homepagerates");
	//echo "<pre>";print_r($settedadminrates);die;
	$build = array(
          '#theme' => 'adminrates',
          '#title' => $title,
		  '#data' => array('rates'=>$rates,'url'=>$this->url,'settedadminrates'=>$settedadminrates)
    );
	return $build;	
  }
  public function adminratessubmit(Request $request) {
        date_default_timezone_set('US/Eastern');
        
        $calconfig = \Drupal::configFactory()->getEditable('demo.settings')->get("calcconfig");
        
	$basepath = \Drupal::request()->getBasePath();
	$adminrates = array(); $adminrates1 = array();
	foreach($request->request->all() as $key=>$val) {
		if($val['key_data'] && !empty($val['key_data'])) {
			$adminrates['product'] = $key;
			$adminrates['rates']['key_data'] = json_decode($val['key_data'],true);
			$adminrates['rates']['key_row'] = $val['key_row'];
			$adminrates1[$key] = $adminrates;
                        if(strpos($key,$calconfig['LT_default_value']) !== false){
                            $def_int_rate = $adminrates['rates']['key_data']['Rate'];
                            $calconfig['IR_default_value'] = $def_int_rate;
                        }
		}
	}
        \Drupal::configFactory()->getEditable('demo.settings')->set("homepagerates_date_time",strtotime('now'))->save();
        
        \Drupal::configFactory()->getEditable('demo.settings')->set("calcconfig",$calconfig)->save();
        
	if(\Drupal::configFactory()->getEditable('demo.settings')->set("homepagerates",$adminrates1)->save())	
	$response = new RedirectResponse($basepath.'/admin/adminrates');
	$response->send();
	drupal_set_message(t('Home page rates configuration successfully saved.'), 'status');
  }
  public function editproductdetailsconfig(){
    $pdconfigform = new ProductDetailsConfigForm(); 
    $pdconfigform->setFormType('edit');
    $editpdconfigform = \Drupal::formBuilder()->getForm($pdconfigform);
    $pd_config = \Drupal::configFactory()->getEditable('demo.settings')->get("pd_config");
   // Populate form fields values with $node object.
    $editpdconfigform['pd_title']['#value'] = $pd_config['pd_title'];
    $editpdconfigform['pd_desc']['#value'] = $pd_config['pd_desc']; 
	 
    return [
    '#theme' => 'editpdconfig',
    '#data' => array('form' => $editpdconfigform)
  ];
  }
  public function editwhyusdetailsconfig(){
    $whyusconfigform = new WhyusDetailsConfigForm(); 
    $whyusconfigform->setFormType('edit');
    $editconfigform = \Drupal::formBuilder()->getForm($whyusconfigform);
    $whyus_config = \Drupal::configFactory()->getEditable('demo.settings')->get("whyus_detail_config");
   // Populate form fields values with $node object.
    $editconfigform['whyus_top_title']['#value'] 			= $whyus_config['whyus_top_title'];
    $editconfigform['whyus_top_desc']['#value'] 			= $whyus_config['whyus_top_desc']; 
	$editconfigform['whyus_testimony_title']['#value'] 		= $whyus_config['whyus_testimony_title'];
    $editconfigform['whyus_testimony_desc']['#value'] 		= $whyus_config['whyus_testimony_desc']; 
	$editconfigform['whyus_our_process_title']['#value'] 	= $whyus_config['whyus_our_process_title'];
    $editconfigform['whyus_our_process_desc']['#value'] 	= $whyus_config['whyus_our_process_desc']; 
	$editconfigform['whyus_loan_process_title']['#value'] 	= $whyus_config['whyus_loan_process_title'];
    $editconfigform['whyus_loan_process_desc']['#value'] 	= $whyus_config['whyus_loan_process_desc']; 
	 
    return [
    '#theme' => 'editwhydetailconfig',
    '#data' => array('form' => $editconfigform)
  ];
  }
  /**
   * Manage Admin home page .
   */
  public function adminmanage() {
	  		
		$response = new RedirectResponse(\Drupal::request()->getSchemeAndHttpHost().'/admin/content');
		$response->send();  
	  
  }
  public function adminquickrates() {
	$title = t('Admin Quick Rates');
        $loanx_api_url =   \Drupal\Core\Site\Settings::get('lnx_api_url_pricing');
        $response = \Drupal::httpClient()->get($loanx_api_url, array('headers' => array('Accept' => 'application/json')));
        $data = (string) $response->getBody();
        $data = json_decode($data,TRUE);
        
        foreach($data['rateList'] as $key => $dat){
			
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
            
           
           
           
               $rate_arr[$proname][$dat['interestRate']][] = $dat['points']; 
               
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
//                
//           $new_data[$proname][] =  array(
//                                                      "LenderProductName"=>$proname,
//                                                      "Rate"=>$dat['interestRate'],
//                                                      "APR"=>$dat['apr'],
//                                                      "MonthlyPayment"=>$dat['monthlyPayment'],
//                                                      "points"=>$dat['points'],
//                                                      "credit"=>$dat['credit'],
//                                                      "TotalFees"=>$dat['fees']
//                                                     );
            
        }
        
	//$stuff = array("30 Year Fixed"=>array("0"=>array("LenderProductName"=>"30 Year Fixed","Rate"=>"3.165","APR"=>"3.115","MonthlyPayment"=>"1200","points"=>"2200","credit"=>"2","TotalFees"=>"1400"),"1"=>array("LenderProductName"=>"30 Year Fixed","Rate"=>"3.265","APR"=>"3.215","MonthlyPayment"=>"1300","points"=>"2202","credit"=>"2","TotalFees"=>"1500"),"2"=>array("LenderProductName"=>"30 Year Fixed","Rate"=>"3.365","APR"=>"3.315","MonthlyPayment"=>"1400","points"=>"2200","credit"=>"2","TotalFees"=>"1600"),"3"=>array("LenderProductName"=>"30 Year Fixed","Rate"=>"3.465","APR"=>"3.415","MonthlyPayment"=>"1500","points"=>"2300","credit"=>"2","TotalFees"=>"1700"),"4"=>array("LenderProductName"=>"30 Year Fixed","Rate"=>"3.565","APR"=>"3.515","MonthlyPayment"=>"1600","points"=>"2100","credit"=>"2","TotalFees"=>"1800")),"20 Year Fixed"=>array("0"=>array("LenderProductName"=>"20 Year Fixed","Rate"=>"3.165","APR"=>"3.115","MonthlyPayment"=>"1200","points"=>"2200","credit"=>"2","TotalFees"=>"1400"),"1"=>array("LenderProductName"=>"20 Year Fixed","Rate"=>"3.765","APR"=>"3.715","MonthlyPayment"=>"2100","points"=>"2204","credit"=>"2","TotalFees"=>"2150"),"2"=>array("LenderProductName"=>"20 Year Fixed","Rate"=>"3.865","APR"=>"3.815","MonthlyPayment"=>"1650","points"=>"2204","credit"=>"2","TotalFees"=>"1550"),"3"=>array("LenderProductName"=>"20 Year Fixed","Rate"=>"3.104","APR"=>"3.205","MonthlyPayment"=>"1950","points"=>"2110","credit"=>"2","TotalFees"=>"1650"),"4"=>array("LenderProductName"=>"20 Year Fixed","Rate"=>"3.965","APR"=>"3.915","MonthlyPayment"=>"2120","points"=>"2104","credit"=>"2","TotalFees"=>"2250")),"15 Year Fixed"=>array("0"=>array("LenderProductName"=>"15 Year Fixed","Rate"=>"3.165","APR"=>"3.115","MonthlyPayment"=>"1200","points"=>"2200","credit"=>"2","TotalFees"=>"1400"),"1"=>array("LenderProductName"=>"15 Year Fixed","Rate"=>"3.444","APR"=>"3.227","MonthlyPayment"=>"1755","points"=>"2204","credit"=>"2","TotalFees"=>"1970"),"2"=>array("LenderProductName"=>"15 Year Fixed","Rate"=>"3.425","APR"=>"3.349","MonthlyPayment"=>"2750","points"=>"2204","credit"=>"2","TotalFees"=>"2190"),"3"=>array("LenderProductName"=>"15 Year Fixed","Rate"=>"3.306","APR"=>"3.409","MonthlyPayment"=>"1690","points"=>"2104","credit"=>"2","TotalFees"=>"1770"),"4"=>array("LenderProductName"=>"15 Year Fixed","Rate"=>"3.964","APR"=>"3.919","MonthlyPayment"=>"2800","points"=>"2204","credit"=>"2","TotalFees"=>"2900")),"10 Year Fixed"=>array("0"=>array("LenderProductName"=>"10 Year Fixed","Rate"=>"3.165","APR"=>"3.115","MonthlyPayment"=>"1200","points"=>"2200","credit"=>"2","TotalFees"=>"1400"),"1"=>array("LenderProductName"=>"10 Year Fixed","Rate"=>"3.180","APR"=>"3.200","MonthlyPayment"=>"3150","points"=>"2204","credit"=>"2","TotalFees"=>"3170"),"2"=>array("LenderProductName"=>"10 Year Fixed","Rate"=>"3.295","APR"=>"3.299","MonthlyPayment"=>"2990","points"=>"2204","credit"=>"2","TotalFees"=>"3025"),"3"=>array("LenderProductName"=>"10 Year Fixed","Rate"=>"3.300","APR"=>"3.308","MonthlyPayment"=>"3145","points"=>"1904","credit"=>"2","TotalFees"=>"3165"),"4"=>array("LenderProductName"=>"10 Year Fixed","Rate"=>"3.402","APR"=>"3.504","MonthlyPayment"=>"3660","points"=>"2004","credit"=>"2","TotalFees"=>"3678")));
	
        //$data = json_encode($stuff);
        
        $rates = $new_data;
        
	//echo "<pre>";print_r($rates);die;
	
        $settedadminrates = \Drupal::configFactory()->getEditable('demo.settings')->get("quickrates");
	//echo "<pre>";print_r($settedadminrates);die;
	$build = array(
          '#theme' => 'adminquickrates',
          '#title' => $title,
		  '#data' => array('rates'=>$rates,'url'=>$this->url,'settedadminrates'=>$settedadminrates)
        );
	return $build;	
  }
  public function adminquickratessubmit(Request $request) {
  
	$basepath = \Drupal::request()->getBasePath();
	$adminrates1 = array();
	//echo "<pre>";print_r($request->request->all());die;
	foreach($request->request->all() as $key=>$val) {
		$adminrates = array();
		foreach($val as $key1=>$val1) {
			if($val1['key_data'] && !empty($val1['key_data'])) {
				$adminrates['product'] = $key;
				$adminrates['rates']['key_data'] = json_decode($val1['key_data'],true);
				$adminrates['rates']['key_row'][] = $key1;				
			}
		}
		$adminrates1[$key] = $adminrates;
	}
	if(\Drupal::configFactory()->getEditable('demo.settings')->set("quickrates",$adminrates1)->save())	
	$response = new RedirectResponse($basepath.'/admin/adminquickrates');
	$response->send();
	drupal_set_message(t('Quick rate quote configuration successfully saved.'), 'status');
  }
  public function admincalcconfig(){
	$calcconfig_values = \Drupal::configFactory()->getEditable('demo.settings')->get("calcconfig");
	$ha_calcconfig_values = \Drupal::configFactory()->getEditable('demo.settings')->get("hacalcconfig");
	$loan_term = array("10"=>"10 Years","15"=>"15 Years","20"=>"20 Years","30"=>"30 Years");
	for($i=1;$i<=$calcconfig_values['LT_default_value'];$i++) {
		$desired_loan_term[$i] = $i;
	}
        
        
        $adminrates = \Drupal::configFactory()->getEditable('demo.settings')->get("homepagerates");
  
        foreach($adminrates as $key => $val){
            $k = preg_replace('/[^0-9]/', '', $key);
            $defrates [$k]= $val['rates']['key_data']['Rate'];

        }
    
	$build = array(
          '#theme' => 'calculator_default_config',
          '#title' => $title,
		  '#data' => array('calcconfig_values'=>$calcconfig_values,'ha_calcconfig_values'=>$ha_calcconfig_values,'loan_term'=>$loan_term,'desired_loan_term'=>$desired_loan_term, 'defa_rates'=>$defrates)
    );
	return $build;	
  }
  public function admincalcconfigsubmit(Request $request) {
	$basepath = \Drupal::request()->getBasePath();
	if(\Drupal::configFactory()->getEditable('demo.settings')->set("calcconfig",$request->request->all())->save())	
	$response = new RedirectResponse($basepath.'/admin/admincalcconfig');
	$response->send();
	drupal_flush_all_caches();
	drupal_set_message(t('Calculator default configuration successfully saved.'), 'status');	  
  }
  public function adminpmiconfig(){
	$pmiconfig_values = \Drupal::configFactory()->getEditable('demo.settings')->get("pmiconfig");
	$build = array(
          '#theme' => 'pmi_default_config',
          '#title' => $title,
		  '#data' => array('pmiconfig_values'=>$pmiconfig_values)
    );
	return $build;	
  }
  public function adminpmiconfigsubmit(Request $request) {
	$basepath = \Drupal::request()->getBasePath();
	if(\Drupal::configFactory()->getEditable('demo.settings')->set("pmiconfig",$request->request->all())->save())	
	$response = new RedirectResponse($basepath.'/admin/adminpmiconfig');
	$response->send();
	drupal_flush_all_caches();
	drupal_set_message(t('PMI default configuration successfully saved.'), 'status');	  
  }
  public function adminhacalcconfigsubmit(Request $request) {
	//echo "<pre>";print_r($request->request->all());die;
	$basepath = \Drupal::request()->getBasePath();
	if(\Drupal::configFactory()->getEditable('demo.settings')->set("hacalcconfig",$request->request->all())->save())	
	$response = new RedirectResponse($basepath.'/admin/admincalcconfig');
	$response->send();
	drupal_flush_all_caches();
	drupal_set_message(t('Calculator default configuration successfully saved.'), 'status');	  
  }
  public function adminarmconfig(){
	$armconfig_values = \Drupal::configFactory()->getEditable('demo.settings')->get("armconfig");
	$build = array(
          '#theme' => 'arm_default_config',
          '#title' => $title,
		  '#data' => array('armconfig_values'=>$armconfig_values)
    );
	return $build;	
  }
  public function adminarmconfigsubmit(Request $request) {
	$basepath = \Drupal::request()->getBasePath();
	if(\Drupal::configFactory()->getEditable('demo.settings')->set("armconfig",$request->request->all())->save())	
	$response = new RedirectResponse($basepath.'/admin/adminarmconfig');
	$response->send();
	drupal_flush_all_caches();
	drupal_set_message(t('ARM default configuration successfully saved.'), 'status');	  
  }  
  public function admintaxslabconfig(){
	$taxslabconfig_values = \Drupal::configFactory()->getEditable('demo.settings')->get("taxslabconfig");
	$build = array(
          '#theme' => 'taxslab_default_config',
          '#title' => $title,
		  '#data' => array('taxslabconfig_values'=>$taxslabconfig_values)
    );
	return $build;	
  }
  public function admintaxslabconfigsubmit(Request $request) {
	$basepath = \Drupal::request()->getBasePath();
	if(\Drupal::configFactory()->getEditable('demo.settings')->set("taxslabconfig",$request->request->all())->save())	
	$response = new RedirectResponse($basepath.'/admin/admintaxslabconfig');
	$response->send();
	drupal_flush_all_caches();
	drupal_set_message(t('Tax Slab default configuration successfully saved.'), 'status');	  
  }  
}
?>