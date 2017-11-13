<?php

/**
 * @file
 * Contains \Drupal\customise\Plugin\Block\weBelieveBlock.
 */

namespace Drupal\customise\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'weBelieveBlock' block.
 *
 * @Block(
 *  id = "webelieveblock",
 *  admin_label = @Translation("We believe block"),
 * )
 */
class weBelieveBlock extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function build() {
    global $base_url;
	$config = $this->getConfiguration();  
	$blockid = $config['id'];
	$section_title = \Drupal::config('block.block.'.$blockid)->get('settings.label');
	$section_desc = \Drupal::config('block.block.'.$blockid)->get('settings.description');   
	if(!$section_title)
		$section_title = '';
	if(!$section_desc)
		$section_desc = '';
	/*
	$sid = 'education';
    $entity_subqueue = \Drupal::entityManager()->getStorage('entity_subqueue')->load($sid);
	$items = $entity_subqueue->get('items')->getValue();
	foreach($items as $item) {
		$nodeids[] = $item['target_id'];
	}
	
	$nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nodeids);
	$finalResult = array();
	foreach($nodes as $k => $node) {
      $nid = $node->get('nid')->value;	 
	  $finalResult[$k]['title'] = $node->get('title')->value;
      $finalResult[$k]['body'] = $node->get('body')->value;	  
      $finalResult[$k]['video'] = $node->get('field_education_video')->value;
	  $finalResult[$k]['thumbnail'] = $node->get('field_education_thumbnail')->value;
	  
    }*/
	//print_r($finalResult);exit;
	/*$video_block_id = "education_video_home";	
	$foo = views_embed_view('homepage_education_video', 'block_1');
	$render = drupal_render($foo);
	*/
    return array(
        '#theme' => 'we_believe_block', 
        '#block_data' => array('education'=>$finalResult,'block_desc'=>$section_desc,'block_title'=>$section_title,'block_id'=>$blockid), 
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
