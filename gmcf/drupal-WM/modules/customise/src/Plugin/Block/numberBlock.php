<?php

/**
 * @file
 * Contains \Drupal\customise\Plugin\Block\numberBlock.
 */

namespace Drupal\customise\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use \Drupal\Core\Url;

/**
 * Provides a 'numberBlock' block.
 *
 * @Block(
 *  id = "number_block",
 *  admin_label = @Translation("Number block"),
 * )
 */
class numberBlock extends BlockBase implements BlockPluginInterface{


  /**
   * {@inheritdoc}
   */
  public function build() {
	$config = $this->getConfiguration();
        $blockid = $config['id'];
        $post_params = \Drupal::request()->server->get('REQUEST_URI');
        $path = explode('/', $post_params);
        $preview_req = false;
        if(in_array('preview', $path) && in_array('draft', $path)){
            $status = (in_array('review', $path))?'needs_review':'draft';
            
            $query = \Drupal::database()->select('node_field_revision', 'nr');
            //$query->distinct();
            $query->fields('nr', ['nid', 'title', 'vid', 'moderation_state']);
            $query->addField('fd', 'type');
            $query->join('node_field_data', 'fd', 'nr.nid = fd.nid');
            //$query->condition('nr.moderation_state', $status, '=');
            $query->condition('fd.type', 'calculators', '=');
            //$query->orderBy('nr.vid', 'DESC');
            //$query->range(0,1);

            $revision = $query->execute()->fetchAllAssoc('nid');
           
            $finalResult = array();
            foreach($revision as $key => $info) {
                $nid = $info->nid;
                $vid = $info->vid;
                if(($info->moderation_state ==  'needs_review') && (in_array('review', $path))){
                    $finalResult[$vid]['nid'] = $nid; 
                    $finalResult[$vid]['vid'] = $info->vid; 
                    $finalResult[$vid]['title'] = $info->title;   
                }else if((($info->moderation_state ==  'draft') || ($info->moderation_state ==  'needs_review')) && (!in_array('review', $path))){
                    $finalResult[$vid]['nid'] = $nid; 
                    $finalResult[$vid]['vid'] = $info->vid; 
                    $finalResult[$vid]['title'] = $info->title;      	  
                    $finalResult[$vid]['why_us_type_icon'] = file_create_url($info->uri);
                    $finalResult[$vid]['why_us_data'] = $info->field_why_us_data_value;
                }                
            }
			$preview_req = true;
        }else{
            $sid = 'homepage_calculator';
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
                              $finalResult[$nid]['title'] = $node->get('title')->value; 
							  //if($node->get('field_linked_calculator_page')[0]->uri)
							  //$finalResult[$nid]['linked_calc_page'] = Url::fromUri($node->get('field_linked_calculator_page')[0]->uri)->toString();
                              $cal_page_id =  $node->get('field_linked_to_calculator_refer')->target_id;
                              if(isset($cal_page_id)) {
                                $finalResult[$nid]['linked_calc_page'] = \Drupal\Core\Url::fromRoute('entity.node.canonical', ['node' =>$cal_page_id ], ['absolute' => TRUE])->toString(); 
                              }else{
                                $finalResult[$nid]['linked_calc_page'] = '#';
                              }
                      }
                    }
            }
        }
        //echo '<pre>';        print_r($finalResult);exit;
    return array(
        '#theme' => 'number_block',		
        '#block_data' => array('calculators'=>$finalResult,'title'=>$config['label'],'description'=>$config['description'],'preview_req'=>$preview_req),
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
