<?php

/**
 * @file
 * Contains \Drupal\customise\Plugin\Block\slidemenuBlock.
 */

namespace Drupal\customise\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a 'slidemenuBlock' block.
 *
 * @Block(
 *  id = "slidemenu_block",
 *  admin_label = @Translation("Slide Menu block"),
 * )
 */
class slidemenuBlock extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function build() {
	
    $items = \Drupal::menuTree()->load('header-slide-menu', new \Drupal\Core\Menu\MenuTreeParameters());
    foreach ($items as $key => $item) {
         if($item->link->isEnabled()){
            $url = $item->link->getUrlObject();
            $slide_menu[$key]['weight'] = $item->link->getWeight();
            $slide_menu[$key]['title'] = $item->link->getTitle();
			$slide_menu[$key]['class'] = $item->link->getOptions()['attributes']['class'];
			$slide_menu[$key]['target'] = $item->link->getOptions()['attributes']['target'];
            $slide_menu[$key]['url'] = $url->toString();
         }
    }
    $slide_menu = $this->slider_menu_sort($slide_menu);
	
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
	$query_slidecon = \Drupal::database()->select('block_content', 'bc');
    $query_slidecon->fields('bcb', ['body_value', 'entity_id']);
    $query_slidecon->join('block_content__body', 'bcb', 'bc.id = bcb.entity_id');
    $query_slidecon->condition('bcb.deleted', 0);
    $query_slidecon->condition('bc.id', 13);

    $slidemenu_icon = ($query_slidecon->execute()->fetchField());
	
    return array(
        '#theme' => 'slidemenu_block',
        '#slide_menu' => $slide_menu,
		'#page' => $page,
		'#slidemenu_icon' => $slidemenu_icon,
        '#cache' => array('max-age' => 0),
     );
  }

  public function slider_menu_sort( array $menu ){
        $first_part = array(); $second_part = array();
        foreach ($menu as $key => $item) {
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
