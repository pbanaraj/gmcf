<?php

/**
 * @file
 * Contains \Drupal\customise\Plugin\Block\ourGoalBlock.
 */

namespace Drupal\customise\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
/**
 * Provides a 'ourGoalBlock' block.
 *
 * @Block(
 *  id = "ourgoalblock",
 *  admin_label = @Translation("Our goal block"),
 * )
 */
class ourGoalBlock extends BlockBase {


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
        
        $finalResult = array();
        if(in_array('preview', $path) && in_array('draft', $path)){
            $preview_req = true;
            $query = \Drupal::database()->select('node_field_revision', 'nr');
            //$query->distinct();
            $query->fields('nr', ['nid', 'title', 'vid', 'moderation_state']);
            $query->addField('ud', 'field_why_us_data_value');
            $query->addField('fm', 'uri');
            $query->join('node_revision__field_why_us_data', 'ud', 'nr.vid = ud.revision_id');
            $query->join('node_revision__field_why_us_type_icon', 'ti', 'ud.revision_id = ti.revision_id');
            $query->join('file_managed', 'fm', 'ti.field_why_us_type_icon_target_id = fm.fid');
            //$query->condition('nr.moderation_state', $status, '=');
            $query->condition('ud.bundle', 'why_us', '=');
            //$query->orderBy('nr.vid', 'DESC');
            //$query->range(0,1);field_why_us_type_icon_target_id   

            $revision = $query->execute()->fetchAllAssoc('nid');
            
            $finalResult = array();
            foreach($revision as $key => $info) {
                $nid = $info->nid;
                $vid = $info->vid;
                if(($info->moderation_state ==  'needs_review') && (in_array('review', $path))){
                    $finalResult[$vid]['nid'] = $nid; 
                    $finalResult[$vid]['vid'] = $info->vid; 
                    $finalResult[$vid]['title'] = $info->title;      	  
                    $finalResult[$vid]['why_us_type_icon'] = file_create_url($info->uri);
                    $finalResult[$vid]['why_us_data'] = $info->field_why_us_data_value;
                }else if((($info->moderation_state ==  'draft') || ($info->moderation_state ==  'needs_review')) && (!in_array('review', $path))){
                    $finalResult[$vid]['nid'] = $nid; 
                    $finalResult[$vid]['vid'] = $info->vid; 
                    $finalResult[$vid]['title'] = $info->title;      	  
                    $finalResult[$vid]['why_us_type_icon'] = file_create_url($info->uri);
                    $finalResult[$vid]['why_us_data'] = $info->field_why_us_data_value;
                }
            }
        }else{    
            $sid = 'homepage_why_us';
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
                                    $nid = $node->get('nid')->value;
                                    $finalResult[$nid]['why_us_type_icon'] 	= file_create_url($node->get('field_why_us_type_icon')->entity->uri->value);  
                                    $finalResult[$nid]['title'] 			= $node->get('title')->value;      	  
                                    $finalResult[$nid]['why_us_data']		= $node->get('field_why_us_data')->value;		
                                    //$finalResult[$nid]['why_us_type'] 		= $node->get('field_why_us_type')->value;
                            }		  
                    }
            }
        }
    return array(
        '#theme' => 'our_goal_block', 
        '#block_data' => array('results'=>$finalResult,'block_desc'=>$section_desc,'block_title'=>$section_title,'block_id'=>$blockid,'preview_req'=>$preview_req), 
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
