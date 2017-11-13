<?php
/**
 * @file
 * Contains \Drupal\fgmc_forms\Form\BannerConfigForm.
 */

namespace Drupal\fgmc_forms\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
Use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Section form.
 */
class TermsDisclosureConfigForm extends FormBase  {

	public function getFormId() {
		return 'fgmc_forms_terms_config_form';
	}
	public function setFormType($type) {
		return $this->formtype = $type;
	}

	 public function buildForm(array $form, FormStateInterface $form_state) {
		global $base_url;
		$this->base_url = $base_url;
		$tandd_content = \Drupal::configFactory()->getEditable('demo.settings')->get("tandd_content");
		$form['tandd_content'] = array(
			'#type' => 'text_format',
			'#title' => $this->t('Content'),
			'#required' => TRUE,
			'#default_value' => $tandd_content,
			
		);
	    $form['submit'] = array(
			'#type' => 'submit',
			'#value' => t('Save')
	   );
	   return $form;
	}
	public function validateForm(array &$form, FormStateInterface $form_state) {
	
	}
	public function submitForm(array &$form, FormStateInterface $form_state) {
	  $formst = $form_state->getValues();	  
	  if(\Drupal::configFactory()->getEditable('demo.settings')->set("tandd_content", $formst['tandd_content']['value'])->save()) {
		drupal_set_message(t('Saved terms and disclosure content successfully.'), 'status');
	  }
	}  
}
?>