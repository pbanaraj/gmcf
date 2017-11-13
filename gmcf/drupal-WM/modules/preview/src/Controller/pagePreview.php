<?php

/**
 * @file
 * Contains \Drupal\preview\Controller\pagePreview.
 */

namespace Drupal\preview\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class pagePreview.
 *
 * @package Drupal\preview\Controller
 */
class pagePreview extends ControllerBase {
  
  /**
   * Getpagepreview.
   *
   * @return string
   *   Return Hello string.
   */
    
  public function getHomePagePreview() {
      //$node = Node::load($param_1);
      
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
            '#theme' => 'homepagepreview',
            '#title' => "",
            '#data' => array('blocks_info'=>$blocks_info,'is_permission'=>$is_permission,'baseURL'=>$this->base_url)
          );
	return $build;
  }
  public function getProductsPagePreview() {
			$pd_config 	= \Drupal::config('demo.settings')->get('pd_config');
			$page_title =  $pd_config['pd_title']; 
			$page_desc 	=  $pd_config['pd_desc']; 
	        $query = \Drupal::database()->select('node_field_revision', 'nr');
            //$query->distinct();
            $query->fields('nr', ['nid', 'title', 'vid', 'moderation_state']);
            $query->addField('sd', 'field_product_short_desc_value');
			$query->addField('ld', 'field_product_long_desc_value');
			$query->addField('psd', 'field_product_section_desc_value');
			//$query->addField('pim', 'field_image_target_id');
            $query->leftjoin('node_revision__field_product_short_desc', 'sd', 'nr.vid = sd.revision_id');			
			$query->leftjoin('node_revision__field_product_long_desc', 'ld', 'nr.vid = ld.revision_id');
			$query->leftjoin('node_revision__field_product_section_desc', 'psd', 'nr.vid = psd.revision_id');
			//$query->leftjoin('node_revision__field_image', 'pim', 'nr.vid = pim.revision_id');
            $query->condition('sd.bundle', 'products', '=');
			$query->condition('ld.bundle', 'products', '=');
			$query->condition('psd.bundle', 'products', '=');
			//$query->condition('pim.bundle', 'products', '=');
            $query->orderBy('nr.vid', 'DESC');
            //$query->range(0,1);

            $revision = $query->execute()->fetchAll();  
			//echo "<pre>";print_r($revision);die;
            $finalResult = array();			
		    $tempArr = array();
			$i= 1;
            foreach($revision as $key => $info) {
                $nid = $info->nid;
                $vid = $info->vid;
				if(!in_array($nid,$tempArr)){
					if(($info->moderation_state ==  'draft') || ($info->moderation_state ==  'needs_review')){
					
						$query_img = \Drupal::database()->select('node_revision__field_image', 'nrimg');
						$query_img->fields('nrimg', ['field_image_target_id']);
						$query_img->condition('nrimg.bundle', 'products', '=');
						$query_img->condition('nrimg.revision_id', $vid, '=');
						$revision1 = $query_img->execute()->fetchAll();						
						if(count($revision1) > 0) {
							$file = \Drupal\file\Entity\File::load($revision1[0]->field_image_target_id);
							$product_image = file_create_url($file->getFileUri());
						} else {
							$product_image = '';
						}
						$finalResult[$i]['nid'] = $nid; 
						$finalResult[$i]['vid'] = $info->vid; 
						$finalResult[$i]['title'] = $info->title;      	  
						$finalResult[$i]['product_short_desc'] = $info->field_product_short_desc_value;
						$finalResult[$i]['product_long_desc'] = $info->field_product_long_desc_value;
						$finalResult[$i]['product_video_url'] = $info->field_product_video_url_value;
						$finalResult[$i]['product_dyk'] = $info->field_product_section_desc_value;
						$finalResult[$i]['product_vowel'] = $this->product_vowel_chk($finalResult[$vid]['title']);
						$finalResult[$i]['product_image'] = $product_image;	
						$i++;						
					}											
                }
				$tempArr[] = $nid;
            }
			$count  = count($finalResult);
			$is_mobile = $this->isMobileDev();
			return array(      
			  '#theme' 	=> 'product_detail_page',
			  '#name' 	=> array('result'=>$finalResult,'page_title'=> $page_title,'page_desc'=>$page_desc,'count'=>$count,'center'=>$center,'is_mobile'=>$is_mobile,'isPreview'=>1),	
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
    if(isset($_SERVER['HTTP_USER_AGENT']) and !empty($_SERVER['HTTP_USER_AGENT'])){
       $user_ag = $_SERVER['HTTP_USER_AGENT'];
       if(preg_match('/(Mobile|Android|Tablet|GoBrowser|[0-9]x[0-9]*|uZardWeb\/|Mini|Doris\/|Skyfire\/|iPhone|Fennec\/|Maemo|Iris\/|CLDC\-|Mobi\/)/uis',$user_ag)){
          return true;
       }else{
          return false;
       };
    }else{
       return false;    
    };
  }
}
