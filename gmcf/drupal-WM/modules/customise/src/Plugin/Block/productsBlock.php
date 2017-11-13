<?php

/**
 * @file
 * Contains \Drupal\customise\Plugin\Block\bannerBlock.
 */

namespace Drupal\customise\Plugin\Block;

use Drupal\Core\Block\BlockBase;



/**
 * Provides a 'productsBlock' block.
 *
 * @Block(
 *  id = "products_block",
 *  admin_label = @Translation("Products block"),
 * )
 */
class productsBlock extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function build() {
    
        $pd_config 	= \Drupal::config('demo.settings')->get('pd_config');
	$page_title =  $pd_config['pd_title']; 
	$page_desc 	=  $pd_config['pd_desc']; 
	$finalResult = array();
	$sid = 'homepage_product';
	$entity_subqueue = \Drupal::entityManager()->getStorage('entity_subqueue')->load($sid);
	$items = $entity_subqueue->get('items')->getValue();
	foreach($items as $item) {
		$nodeids[] = $item['target_id'];
	}
	$nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nodeids);
	$i= 1;
	
	foreach($nodes as $node) {
	  $nid = $node->get('nid')->value;	
	  $product_image = $node->get('field_image')->entity->uri->value;
	  
	  if($product_image && $product_image != '')
			$finalResult[$i]['product_image'] 		= file_create_url($node->get('field_image')->entity->uri->value);
	  else{
		  
		  $finalResult[$i]['product_image'] = '';
	  }
	  $finalResult[$i]['nid'] 				= 'bu'.$nid;
          $finalResult[$i]['title'] 				= $node->get('title')->value;
	  //$finalResult[$i]['product_image'] 		= file_create_url($node->get('field_image')->entity->uri->value);	
	  $finalResult[$i]['product_type'] 		= $node->get('field_product_type')->value; 
	  $finalResult[$i]['product_short_desc'] 	= $node->get('field_product_short_desc')->value;
	  $finalResult[$i]['product_long_desc'] 	= $node->get('field_product_long_desc')->value;	  
	  $finalResult[$i]['product_video_url'] 	= $node->get('field_product_video_url')->value;
	  $finalResult[$i]['product_dyk'] 			= $node->get('field_product_section_desc')->value;
	  $finalResult[$i]['product_vowel'] 		= $this->product_vowel_chk($finalResult[$i]['title']);
	  $i++;
	}
        $count  = count($finalResult);
        $is_mobile = $this->isMobileDev();
        
    return array(      
      '#theme' 	=> 'product_detail',
      '#name' 	=> array('result'=>$finalResult,'page_title'=> $page_title,'page_desc'=>$page_desc,'count'=>$count,'center'=>$center,'is_mobile'=>$is_mobile),
	  '#cache' => array('max-age' => 0),
    ); 
    
  }
  public function product_vowel_chk($title){
	$first = $title[0];	 
	$vowelArr = array('A','E','I','O','U','a','e','i','o','u');
	if(in_array($first,$vowelArr))
		return 'an';
	else
		return 'a';
  }
	public function isMobileDev(){
	
		$tablet_browser = 0;
		$mobile_browser = 0;

		if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
			$tablet_browser++;
		}

		if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
			$mobile_browser++;
		}

		if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
			$mobile_browser++;
		}

		$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
		$mobile_agents = array(
			'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
			'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
			'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
			'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
			'newt','noki','palm','pana','pant','phil','play','port','prox',
			'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
			'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
			'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
			'wapr','webc','winw','winw','xda ','xda-');

		if (in_array($mobile_ua,$mobile_agents)) {
			$mobile_browser++;
		}

		if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'opera mini') > 0) {
			$mobile_browser++;
			//Check for tablets on opera mini alternative headers
			$stock_ua = strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])?$_SERVER['HTTP_X_OPERAMINI_PHONE_UA']:(isset($_SERVER['HTTP_DEVICE_STOCK_UA'])?$_SERVER['HTTP_DEVICE_STOCK_UA']:''));
			if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
			  $tablet_browser++;
			}
		}

		if ($tablet_browser > 0) {
		   // do something for tablet devices
		   return 'tablet';
		}
		else if ($mobile_browser > 0) {
		   // do something for mobile devices
		   return 'mobile';
		}
		else {
		   // do something for everything else
		   return 'desktop';
		}   
		 //_______________END DETECTING DEVICES__________________//
	}

}
