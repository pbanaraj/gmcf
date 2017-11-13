<?php

/**
 * @file
 * Contains \Drupal\customise\Plugin\Block\logoBlock.
 */

namespace Drupal\customise\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'logoBlock' block.
 *
 * @Block(
 *  id = "logo_block",
 *  admin_label = @Translation("Logo block"),
 * )
 */
class logoBlock extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function build() {
    //$build = [];
    //$build['logo_block']['#markup'] = 'Implement logoBlock.';
    $act_theme = \Drupal::theme()->getActiveTheme()->getName();
    $block_data ="";
    global $base_url;
    $this->base_url = $base_url;
    
    $query = \Drupal::database()->select('block_content', 'bc');
    $query->fields('bcb', ['body_value', 'entity_id']);
    $query->join('block_content__body', 'bcb', 'bc.id = bcb.entity_id');
    $query->condition('bcb.deleted', 0);
    $query->condition('bc.id', 9);
    
    $frontend = ($query->execute()->fetchField());

    $query = \Drupal::database()->select('block_content', 'bc');
    $query->fields('bcb', ['body_value', 'entity_id']);
    $query->join('block_content__body', 'bcb', 'bc.id = bcb.entity_id');
    $query->condition('bcb.deleted', 0);
    $query->condition('bc.id', 10);
    
    $backend = ($query->execute()->fetchField());
    if($act_theme == 'basis'){
        $logo = $frontend;
    }else{
        $logo = $backend;
    }
    return array(
        '#theme' => 'logo_block', 
        '#block_data' => array('base_url'=>$this->base_url),
        '#act_theme' => $act_theme,
        '#logo' => $logo,
        '#cache' => ['max-age' => 0,],
     );

    //return $build;
  }

}
