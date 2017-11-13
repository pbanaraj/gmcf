<?php

/**
 * @file
 * Contains \Drupal\customise\Plugin\Block\feesBlock.
 */

namespace Drupal\customise\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\Core\Url;

/**
 * Provides a 'feesBlock' block.
 *
 * @Block(
 *  id = "feesblock",
 *  admin_label = @Translation("Fees block"),
 * )
 */
class feesBlock extends BlockBase implements BlockPluginInterface{


  /**
   * {@inheritdoc}
   */
  public function build() {
  date_default_timezone_set('US/Eastern');
  $config = $this->getConfiguration();
   $blockid = $config['id'];
   $section_title = \Drupal::config('block.block.'.$blockid)->get('settings.label');
   $section_desc = \Drupal::config('block.block.'.$blockid)->get('settings.description');
	$preview_req = false;
	global $base_url;
	$this->base_url = $base_url;
        // preg_match("/[^\\d*](\\d.*)/", $str, $matches);
        
        $data_rates = \Drupal::config('demo.settings')->get('homepagerates');
        foreach($data_rates as $key => $value){
            
            preg_match("/[^\\d*](\\d.*)/", $key, $matches);
            $data_rates1[$matches[1]]  = $value;
        }
    
    $post_params = \Drupal::request()->server->get('REQUEST_URI');
        $path = explode('/', $post_params);
         
        $finalResult = array(); $tandd_content = '';
    if(in_array('preview', $path) && in_array('draft', $path)){
        $preview_req = true;
        $query = \Drupal::database()->select('node_field_revision', 'nr');
        //$query->distinct();
        $query->fields('nr', ['nid', 'title', 'vid', 'moderation_state']);
        $query->addField('rb', 'body_value');
        $query->join('node_revision__body', 'rb', 'nr.vid = rb.revision_id');
        //$query->condition('nr.moderation_state', $status, '=');
        $query->condition('rb.bundle', 'terms_and_disclosure', '=');
        $query->orderBy('nr.vid', 'DESC');
        $query->range(0,1);   

        $revision = $query->execute()->fetchAllAssoc('vid'); //echo '<pre>'; print_r($revision);exit;

        foreach($revision as $key => $info) {
            if(($info->moderation_state ==  'needs_review') && (in_array('review', $path))){
                $tandd_content = $info->body_value; 
            }else if((($info->moderation_state ==  'draft') || ($info->moderation_state ==  'needs_review')) && (!in_array('review', $path))){
                $tandd_content = $info->body_value;  
            }
        }
        
    }else{
	// get terms and disclosure content
	$sid = 'homepage_termsanddisclosure';	
	$queue_status = \Drupal::config('entityqueue.entity_queue.'.$sid)->get('status');
	$nodeids = array();
	if($queue_status) {
		$entity_subqueue = \Drupal::entityManager()->getStorage('entity_subqueue')->load($sid);
		$items = $entity_subqueue->get('items')->getValue();
		foreach($items as $item) {
			$nodeids[] = $item['target_id'];
		}
	}

	if(!empty($nodeids)) {
		$nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nodeids);	
		foreach($nodes as $node) {
		  if($node->get('status')->value == 1) {
			  $tandd_content = $node->get('body')->value;      	  
		  }		  
		}
	}	
    }
        $hide = ($tandd_content == '')?'hide':'';
        
        $dttimestp = \Drupal::config('demo.settings')->get('homepagerates_date_time');
        
        $time = date("h:i A", $dttimestp);
        $date = date("m/d/Y", $dttimestp);
        
	$seeall_rates_url = $this->base_url."/quick_rate_landing";
    return array(
        '#theme' => 'fees_block', 
        '#block_data' => array('data_rates'=>$data_rates1,'time'=>$time, 'date'=>$date,  'title'=>$section_title,'description'=>$section_desc,'tandd_content'=>$tandd_content,'seeall_rates_url'=>$seeall_rates_url, 'class'=> $hide,'preview_req'=>$preview_req),
		'#cache' => array('max-age' => 0),
     );
  }
  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $form['Description'] = array (
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
	  '#default_value' => isset($config['description']) ? $config['description'] : '',
    );

    return $form;
  }
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->setConfigurationValue('description', $form_state->getValue('Description'));
  }

}
