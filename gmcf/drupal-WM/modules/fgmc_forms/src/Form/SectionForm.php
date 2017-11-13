<?php
/**
 * @file
 * Contains \Drupal\fgmc_forms\Form\SectionForm.
 */

namespace Drupal\fgmc_forms\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
Use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Section form.
 */
class SectionForm extends FormBase  {

	public function getFormId() {
		return 'fgmc_forms_section_form';
	}
	 public function buildForm(array $form, FormStateInterface $form_state) {
		global $base_url;
		$this->base_url = $base_url;
		$remove_url = $this->base_url."/admin/removesection";
		$section_elements = \Drupal::config('demo.settings')->get('section_keys');
		$markup_data = "<h4>Available sections</h4>";
		$markup_data .= "<ul>";
		foreach($section_elements as $key=>$val) {
			$markup_data .= "<li>".$val['section_label']."(".$val['section_key'].")&nbsp;<a class='remove_section' href='$remove_url' data-key='$key'>Remove</a></li>";
		}
		$markup_data .= "</ul>";
		//echo "<pre>";print_r($section_elements);die;
		$form['section_key'] = array(
			'#type' => 'textfield',
			'#title' => $this->t('Section Key'),
			'#autocomplete_route_name' => 'fgmc_forms_viewblocklist',
			'#required' => TRUE,
		);	
		$form['section_label'] = array(
			'#type' => 'textfield',
			'#title' => $this->t('Section Label'),
			'#required' => TRUE,
		);
		$form['section_enable'] = array(
			'#type' => 'hidden',
			'#value' => 1
		);		
	  
	  $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save Config')
    );
	  $form['#attached']['library'][] = 'fgmc_forms/fgmc_forms.confirm';
	  $form['#attached']['library'][] = 'fgmc_forms/fgmc_forms_custom_css';
	  $form['#markup'] = "<h2>Hello welcome to sections config</h2>".$markup_data;
	  return $form;
	}
	public function validateForm(array &$form, FormStateInterface $form_state) {
		// Validate video URL.
		$formst = $form_state->getValues();
		$block_id = $formst['section_key'];
		$block = \Drupal\block\Entity\Block::load($block_id);
		if(empty($block)) {
			$form_state->setErrorByName('section_key', $this->t("The block id '%blockid' is invalid.", array('%blockid' => $block_id)));
		}
	}

	public function submitForm(array &$form, FormStateInterface $form_state) {
	  $formst = $form_state->getValues();
	  $section_elements = \Drupal::config('demo.settings')->get('section_keys');
	  $formvalues = array();
	  $formvalues['section_key'] = $formst['section_key'];
	  $formvalues['section_label'] = $formst['section_label'];
	  $formvalues['section_enable'] = $formst['section_enable'];
	  if(empty($section_elements)) $section_elements = array();	  
	  array_push($section_elements,$formvalues);		 
	  \Drupal::configFactory()->getEditable('demo.settings')->set("section_keys", $section_elements)->save();
	}
  
}
?>