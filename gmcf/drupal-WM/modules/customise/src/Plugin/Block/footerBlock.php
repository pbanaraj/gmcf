<?php

/**
 * @file
 * Contains \Drupal\customise\Plugin\Block\footerBlock.
 */

namespace Drupal\customise\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Database;

/**
 * Provides a 'footerBlock' block.
 *
 * @Block(
 *  id = "footer_block",
 *  admin_label = @Translation("Footer block"),
 * )
 */
class footerBlock extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function build() {
      
     \Drupal::service('page_cache_kill_switch')->trigger();
    $items = \Drupal::menuTree()->load('footer', new \Drupal\Core\Menu\MenuTreeParameters());
    foreach ($items as $key => $item) {
        if($item->link->isEnabled()){
            $url = $item->link->getUrlObject();
            $left_menu[$key]['weight'] = $item->link->getWeight();
            $left_menu[$key]['title'] = $item->link->getTitle();
            $left_menu[$key]['url'] = $url->toString();
        }
    }
    
    $left_menu = $this->menu_sort($left_menu);
    
    $items = \Drupal::menuTree()->load('footer-right-menu', new \Drupal\Core\Menu\MenuTreeParameters());
    foreach ($items as $key => $item) {
         if($item->link->isEnabled()){
            $url = $item->link->getUrlObject();
            $right_menu[$key]['weight'] = $item->link->getWeight();
            $right_menu[$key]['title'] = $item->link->getTitle();
            $right_menu[$key]['url'] = $url->toString();
         }
    }
    
    $right_menu = $this->menu_sort($right_menu);
    
    $query = \Drupal::database()->select('block_content', 'bc');
    $query->fields('bcb', ['body_value', 'entity_id']);
    $query->join('block_content__body', 'bcb', 'bc.id = bcb.entity_id');
    $query->condition('bcb.deleted', 0);
    $query->condition('bc.id', 5);

    $contact = ($query->execute()->fetchField());
    
    $query = \Drupal::database()->select('block_content', 'bc');
    $query->fields('bcb', ['body_value', 'entity_id']);
    $query->join('block_content__body', 'bcb', 'bc.id = bcb.entity_id');
    $query->condition('bcb.deleted', 0);
    $query->condition('bc.id', 6);
    
    $privacy = ($query->execute()->fetchField());
    
    $query = \Drupal::database()->select('block_content', 'bc');
    $query->fields('bcb', ['body_value', 'entity_id']);
    $query->join('block_content__body', 'bcb', 'bc.id = bcb.entity_id');
    $query->condition('bcb.deleted', 0);
    $query->condition('bc.id', 7);
    
    $footer_heading = ($query->execute()->fetchField());
    
    $query = \Drupal::database()->select('block_content', 'bc');
    $query->fields('bcb', ['body_value', 'entity_id']);
    $query->join('block_content__body', 'bcb', 'bc.id = bcb.entity_id');
    $query->condition('bcb.deleted', 0);
    $query->condition('bc.id', 8);
    
    $footer_text = ($query->execute()->fetchField());
    
    $nids = \Drupal::entityQuery('node')->condition('type','social_media')->execute();
    $nodes =  \Drupal\node\Entity\Node::loadMultiple($nids);
    $social_media = array();
    foreach ($nodes as $key => $node) {
        $social_media[$node->get('title')->getValue()[0]['value']]['icon'] = file_create_url($node->get('field_icon')->entity->uri->value);
        $social_media[$node->get('title')->getValue()[0]['value']]['icon_hover']  = file_create_url($node->get('field_icon_hover')->entity->uri->value);
        $social_media[$node->get('title')->getValue()[0]['value']]['link']  = $node->get('field_link')->getValue()[0]['value'];
    } 
   
  
    if(\Drupal::service('path.matcher')->isFrontPage()){
        $page = 'front';
    }else{
        $page = 'inner';
    }
    $block_data = "";

    return array(
        '#theme' => 'footer_block', 
        '#block_data' => $block_data, 
        '#footer_left' => $left_menu, 
        '#footer_right' => $right_menu,
        '#social_media' => $social_media,
        '#contact' => $contact,
        '#privacy' => $privacy,
        '#heading' => $footer_heading,
        '#footxt' => $footer_text,
        '#page' => $page,
        '#cache' => ['max-age' => 0,],
     );
}

    public function menu_sort( array $menu ){
        $first_part = array(); $second_part = array();
        foreach ($menu as $item) {
            if($item['weight'] != 0){
                $first_part[] = $item;
            }else{
                $second_part[] = $item;
            }
        }
        usort($first_part, function (array $a, array $b) { return $a["weight"] - $b["weight"]; });
        
        return array_merge($first_part, $second_part);
    }

}
