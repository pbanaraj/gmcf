<?php

/**
 * @file
 * Contains \Drupal\customise\Plugin\Block\bannerBlock.
 */

namespace Drupal\customise\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'bannerBlock' block.
 *
 * @Block(
 *  id = "banner_block",
 *  admin_label = @Translation("Banner block"),
 * )
 */
class bannerBlock extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function build() {
    $banner_config = \Drupal::config('demo.settings')->get('banner_config');
    $post_params = \Drupal::request()->server->get('REQUEST_URI');
        $path = explode('/', $post_params);
         
        $finalResult = array();
    if(in_array('preview', $path) && in_array('draft', $path)){
        
        $query = \Drupal::database()->select('node_field_revision', 'nr');
        //$query->distinct();
        $query->fields('nr', ['nid', 'title', 'vid', 'moderation_state']);
        $query->addField('cn', 'field_customer_name_value');
        $query->addField('rb', 'body_value');
        $query->addField('cs', 'field_customer_since_value');
        $query->join('node_revision__body', 'rb', 'nr.vid = rb.revision_id');
        $query->join('node_revision__field_customer_name', 'cn', 'rb.revision_id = cn.revision_id');
        $query->join('node_revision__field_customer_since', 'cs', 'cn.revision_id = cs.revision_id');
        //$query->condition('nr.moderation_state', $status, '=');
        $query->condition('rb.bundle', 'testimonials', '=');
        $query->orderBy('nr.vid', 'DESC');
        
        $revision = $query->execute()->fetchAll();
        $finalResult = array();	
		$tempArr = array();
        foreach($revision as $key => $info) {
            $nid = $info->nid;
            $vid = $info->vid;
			 if(!in_array($nid,$tempArr)) {
				if(($info->moderation_state ==  'draft') || ($info->moderation_state ==  'needs_review')){					
					$finalResult[$nid]['nid'] = $nid; 
					$finalResult[$nid]['vid'] = $info->vid; 
					$finalResult[$nid]['title'] = $info->title; 
					$finalResult[$nid]['title'] = $info->moderation_state;            
					$finalResult[$nid]['body'] = $info->body_value;  
					$finalResult[$nid]['customer_name'] = $info->field_customer_name_value;
					$finalResult[$nid]['customer_since'] = $info->field_customer_since_value; 
				}
            }
			$tempArr[] = $nid;
        }
		
    }else{
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
    }
	global $base_url;
	$this->base_url = $base_url;
	
    $block_data ="";
    return array(
        '#theme' => 'banner_block', 
        '#block_data' => array('banner_config'=>$banner_config,'testimonials'=>$finalResult,'base_url' => $this->base_url),
		'#cache' => array('max-age' => 0),
     );
  }

}
