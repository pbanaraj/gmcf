<?php

/**
 * @file
 * Contains \Drupal\customise\Plugin\Block\menuBlock.
 */

namespace Drupal\customise\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'menuBlock' block.
 *
 * @Block(
 *  id = "menu_block",
 *  admin_label = @Translation("Menu block"),
 * )
 */
class menuBlock extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function build() {
    $items = \Drupal::menuTree()->load('top-menu', new \Drupal\Core\Menu\MenuTreeParameters());
    
    foreach ($items as $key => $item) {
      $tree[$key]['title'] = $item->link->getTitle();
      if(strtolower($item->link->getTitle()) == strtolower('CMS Home')){
          $tree[$key]['url'] = $this->getCmsHomeUrl(); 
      }else{
          $url = $item->link->getUrlObject();
        $tree[$key]['url'] = $url->toString();
      }
      
    }
    
    return array(
        '#theme' => 'menu_block',
        '#menu_items' => $tree,
     );
  }
  
  public function getCmsHomeUrl(){
        $user = \Drupal::currentUser();
        
        $roles = $user->getRoles();
        if(in_array('administrator', $roles)){
            $url = '/admin';
        }elseif (in_array('editor', $roles)) {
            $url = '/my-draft';
        }elseif (in_array('approver', $roles)) {
            $url = '/admin/approval-queue';
        }
        return $url;
  }

}
