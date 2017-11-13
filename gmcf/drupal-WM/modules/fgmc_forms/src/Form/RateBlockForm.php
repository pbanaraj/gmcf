<?php

/**
 * @file
 * Contains \Drupal\fgmc_forms\Form\RateBlockForm.
 */

namespace Drupal\fgmc_forms\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\CssCommand;

/**
 * Class TestModalForm.
 *
 * @package Drupal\modal\Form
 */
class RateBlockForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rate_block_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $arg = NULL) {
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
	$blockid = $arg;
	$section_title = \Drupal::config('block.block.'.$blockid)->get('settings.label');
	$section_desc = \Drupal::config('block.block.'.$blockid)->get('settings.description');
    $form['block_title'] = array(
      '#type' => 'text_format',
      '#title' => $this->t('Title'),
	  '#description' => 'Please enter the title of block',
	  '#default_value' => $section_title,
	  '#attributes' => array(
			'class' => array(
			  'desc-custom-class' 
			),
		),
    );
	$form['block_title']['#prefix'] = '<div id="block_title_message"></div>';	
	if($blockid!= "careerblock") {
		$form['block_desc'] = array(
		  '#type' => 'text_format',
		  '#title' => $this->t('Description'),
		  '#default_value' => $section_desc,
		  '#description' => 'Please enter the description of block',
		  '#attributes' => array(
				'class' => array(
				  'desc-custom-class' 
				),
			),
		);
	} else {
		$form['block_desc'] = array(
		  '#type' => 'hidden',
		  '#default_value' => ""
		);
	}
	$form['block_desc']['#prefix'] = '<div id="block_desc_message"></div>';
	$form['block_id'] = array(
      '#type' => 'hidden',
      '#title' => $this->t('Block`s id'),
	  '#default_value' => $blockid,
	  '#attributes' => array(
			'class' => array(
			  'block-id-custom-class' 
			),
		),
    );
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Update'),
	  '#attributes' => array(
			'class' => array(
			  'submit-custom-class' 
			),
		),
      '#ajax' => array(
      'callback' => 'Drupal\fgmc_forms\Form\RateBlockForm::ajax_example_submit_driven_callback',
	  'wrapper' => 'rate_block_form',
      'access' => TRUE
		), 
    );
	
    $form['#title'] = 'Update Section Title/Description';
	//drupal_get_messages();
    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }
public function ajax_example_submit_driven_callback(array &$form, FormStateInterface $form_state) {	
    $response = new AjaxResponse();   
	 $formst = $form_state->getValues();
	 $section_keys = \Drupal::config('demo.settings')->get('section_keys');	 
	  $block_id = $formst['block_id'];
	  $new_title = $formst['block_title']['value'];
	  $new_description = $formst['block_desc']['value'];
	  $validate = true;
	  if ($new_title) {
		 if (strlen(trim(strip_tags($new_title))) > 150) {
			 $validate = false;			
			$form['block_title']['#attributes']['class'][] = 'error';
			$response->addCommand(new HtmlCommand('#block_title_message', "<p style='color:red'><b>Title Can't be more than 150</b></p>"));
			
		 }
		 else{
			$response->addCommand(new HtmlCommand('#block_title_message', ""));  
		}

	  }
	  if ($new_description) {
		 if (strlen(trim(strip_tags($new_description))) > 150) {
			 $validate = false;
			$form['block_desc']['#attributes']['class'][] = 'error';
			$response->addCommand(new HtmlCommand('#block_desc_message', "<p style='color:red'><b>Description Can't be more than 150<p style='color:red'></b>"));
		 }else{
		 $response->addCommand(new HtmlCommand('#block_desc_message', "")); 
		}

	  }
	  if($validate){
		\Drupal::configFactory()->getEditable('block.block.'.$block_id)->set("settings.label",$new_title)->save();
		\Drupal::configFactory()->getEditable('block.block.'.$block_id)->set("settings.description",$new_description)->save();
		$response->addCommand(new HtmlCommand('#'.$block_id.'_title', $new_title));	  
		$command = new CloseModalDialogCommand(); 
		$response->addCommand($command);  
	  }else{
		  $form_state->setRebuild();
		    
	  }
	  return $response;
  }
  public function open_modal(&$form, FormStateInterface $form_state) {
    $node_title = $form_state->getValue('node_title');
    $query = \Drupal::entityQuery('node')
      ->condition('title', $node_title);
    $entity = $query->execute();
    $title = 'Node ID';
    $key = array_keys($entity);
    $id = !empty($key[0]) ? $key[0] : NULL;
	$id = NULL ;
	$title = ''; 
	$content = '';
    $response = new AjaxResponse();
    //if ($id !== NULL) {
      $content = '<div class="test-popup-content"> Node ID is: ' . $id . '</div>';
      $options = array(
        'dialogClass' => 'popup-dialog-class',
        'width' => '600px',
        'height' => '500',
      );
      $response->addCommand(new OpenModalDialogCommand($title, $content, $options));

   // }
   // else {
      //$content = 'Not found record with this title <strong>' . $node_title .'</strong>';
      //$response->addCommand(new OpenModalDialogCommand($title, $content));
   // }
    return $response;
  }
}
