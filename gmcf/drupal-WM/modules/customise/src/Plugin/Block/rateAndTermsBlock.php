<?php

/**
 * @file
 * Contains \Drupal\customise\Plugin\Block\rateAndTermsBlock.
 */

namespace Drupal\customise\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Database\Database;

/**
 * Provides a 'rateAndTermsBlock' block.
 *
 * @Block(
 *  id = "rateandtermsblock",
 *  admin_label = @Translation("Rate and terms block"),
 * )
 */
class rateAndTermsBlock extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function build() {	
	global $base_url;
	$config = $this->getConfiguration();  
	$blockid = $config['id'];
	$section_title = \Drupal::config('block.block.'.$blockid)->get('settings.label');
	$section_desc = \Drupal::config('block.block.'.$blockid)->get('settings.description'); 
	$preview_req = false;
	if(!$section_title)
		$section_title = '';
	if(!$section_desc)
		$section_desc = '';
        
        
        
        $post_params = \Drupal::request()->server->get('REQUEST_URI');
        $path = explode('/', $post_params);
        
        if(in_array('preview', $path) && in_array('draft', $path)){
            
            $nids = \Drupal::entityQuery('node')
                ->condition('type','products')
                ->condition('moderation_state', 'draft')
                ->execute(); 
            
            $query = \Drupal::database()->select('node_field_revision', 'nr');
            //$query->distinct();
            $query->fields('nr', ['nid', 'title', 'vid', 'moderation_state']);
            $query->addField('sd', 'field_product_short_desc_value');
            //$query->addField('vu', 'field_product_video_url_value');
            $query->join('node_revision__field_product_short_desc', 'sd', 'nr.vid = sd.revision_id');
            //$query->join('node_revision__field_product_video_url', 'vu', 'sd.revision_id = vu.revision_id');
            //$query->condition('nr.moderation_state', $status, '=');
            $query->condition('sd.bundle', 'products', '=');
            $query->orderBy('nr.vid', 'DESC');
            //$query->range(0,1);

            $revision = $query->execute()->fetchAll();            
            $finalResult = array();			
		    $tempArr = array();
            foreach($revision as $key => $info) {
                $nid = $info->nid;
                $vid = $info->vid;
				if(!in_array($nid,$tempArr)){
					if(($info->moderation_state ==  'draft') || ($info->moderation_state ==  'needs_review')){											
						$finalResult[$vid]['nid'] = $nid; 
						$finalResult[$vid]['vid'] = $info->vid; 
						$finalResult[$vid]['title'] = $info->title;      	  
						$finalResult[$vid]['product_short_desc'] = $info->field_product_short_desc_value;
						$finalResult[$vid]['product_url'] = '#';
						//$finalResult[$vid]['product_video_url'] = $info->field_product_video_url_value;
					}					                    
                }
				$tempArr[] = $nid;
            }
            /*$nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);	
            foreach($nodes as $node) {
                $nid = $node->get('nid')->value;
                $vid = $node->get('vid')->value; 
                $finalResult[$vid]['nid'] = $nid; 
                $finalResult[$vid]['vid'] = $node->get('vid')->value; 
                $finalResult[$vid]['title'] = $node->get('title')->value;      	  
                $finalResult[$vid]['product_short_desc'] = $node->get('field_product_short_desc')->value;
                $finalResult[$vid]['product_url'] = $base_url."/products/".$nid;
                //$finalResult[$vid]['product_video_url'] = $node->get('field_product_video_url')->value;
            }
            krsort($finalResult);*/
            //echo '<pre>';print_r($finalResult);exit;
			$preview_req = true;
        }else{    
            
            /*
			$nids = \Drupal::entityQuery('node')
                ->condition('type','products')
                ->condition('moderation_state', 'published')
                ->execute(); 
			*/
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


            $finalResult = array();
            if(!empty($nodeids)) {
                    $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nodeids);	
                    foreach($nodes as $node) {
                      if($node->get('status')->value == 1) {
                              $nid = $node->get('nid')->value;	 
                              $finalResult[$nid]['nid'] = $nid;  
                              $finalResult[$nid]['title'] = $node->get('title')->value;      	  
                              $finalResult[$nid]['product_short_desc'] = $node->get('field_product_short_desc')->value;
                              $finalResult[$nid]['product_url'] = $base_url."/products#".$nid;
                              $finalResult[$nid]['product_video_url'] = $node->get('field_product_video_url')->value;
                     }		  
                    }
            }
        }
	$all_product_url = $base_url."/products";
    return array(
        '#theme' => 'rate_and_terms_block', 
        '#block_data' => array('products'=>$finalResult,'block_desc'=>$section_desc,'block_title'=>$section_title,'block_id'=>$blockid,'all_product_url'=>$all_product_url,'preview_req'=>$preview_req),
		'#cache' => array('max-age' => 0),
     );
  }
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
