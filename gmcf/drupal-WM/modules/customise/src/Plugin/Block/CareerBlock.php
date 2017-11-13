<?php
namespace Drupal\customise\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;


/**
 * Provides a 'Career' Block
 *
 * @Block(
 *   id = "careerblock",
 *   admin_label = @Translation("career block"),
 * )
 */
class CareerBlock extends BlockBase implements BlockPluginInterface{
  /**
   * {@inheritdoc}
   */
  public function build() {
	
    global $base_url;
   $config = $this->getConfiguration();  
   $blockid = $config['id'];
   
   $section_title = \Drupal::config('block.block.'.$blockid)->get('settings.label');
   $section_desc = \Drupal::config('block.block.'.$blockid)->get('settings.description');  
   if(!$section_desc)
		$section_desc = '';
	
    return array(
	  '#theme' => 'career_block',
	  '#title' => 'Career',
	  '#block_data' => array('block_desc'=>$section_desc,'block_title'=>$section_title,'block_id'=>$blockid),
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
