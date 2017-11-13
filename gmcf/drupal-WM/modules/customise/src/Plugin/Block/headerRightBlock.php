<?php

/**
 * @file
 * Contains \Drupal\customise\Plugin\Block\headerRightBlock.
 */

namespace Drupal\customise\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a 'headerRightBlock' block.
 *
 * @Block(
 *  id = "header_right_block",
 *  admin_label = @Translation("Header right block"),
 * )
 */
class headerRightBlock extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function build() {
    $block_data ="";
    
    
    \Drupal::service('page_cache_kill_switch')->trigger();
    
    $block = \Drupal\block_content\Entity\BlockContent::load(1);
    $ph_number = \Drupal::entityManager()->getViewBuilder('block_content')->view($block);
    
    //$block = \Drupal\block_content\Entity\BlockContent::load('basis_search');
   //$search = \Drupal::entityManager()->getViewBuilder('block_content')->view($block);    

    $query = \Drupal::database()->select('block_content', 'bc');
    $query->fields('bcfcn', ['field_contact_number_value', 'entity_id']);
    $query->join('block_content__field_contact_number', 'bcfcn', 'bc.id = bcfcn.entity_id');
    $query->condition('bcfcn.deleted', 0);
    $query->condition('bc.id', 12);

    $header_phone = ($query->execute()->fetchField());
	
	$query_picon = \Drupal::database()->select('block_content', 'bc');
    $query_picon->fields('bcb', ['body_value', 'entity_id']);
    $query_picon->join('block_content__body', 'bcb', 'bc.id = bcb.entity_id');
    $query_picon->condition('bcb.deleted', 0);
    $query_picon->condition('bc.id', 11);

    $header_phone_icon = ($query_picon->execute()->fetchField());
    
    $landingurl = array('tp_pre_qual', 'tp_pre_qual_thank', 'tp_showleadinfo');
    $request = Request::createFromGlobals();
    $uri = $request->getRequestUri();
    
    $landing = '';
    foreach ($landingurl as $value) {
        if (strpos($uri, $value) !== false) {
            $landing .= $value;
        }
    }
    if(\Drupal::service('path.matcher')->isFrontPage()){
        $page = 'front';
    }elseif($landing != ''){
        $page = 'landng';
    }else{
        $page = 'inner';
    }
    
    return array(
        '#theme' => 'header_right_block', 
        '#block_data' => $block_data, 
        '#ph_number' => $header_phone,
		'#ph_icon' => $header_phone_icon,
        '#page' => $page,
        '#cache' => ['max-age' => 0,],
     );
  }
  
}
