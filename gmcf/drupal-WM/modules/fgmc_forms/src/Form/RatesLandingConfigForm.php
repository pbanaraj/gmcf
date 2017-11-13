<?php
/**
 * @file
 * Contains \Drupal\fgmc_forms\Form\RatesLandingConfigForm.
 */

namespace Drupal\fgmc_forms\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
Use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Section form.
 */
class RatesLandingConfigForm extends FormBase  {

	public function getFormId() {
		return 'fgmc_forms_rates_landing_config_form';
	}
	public function setFormType($type) {
		return $this->formtype = $type;
	}

	 public function buildForm(array $form, FormStateInterface $form_state) {
		global $base_url;
		$this->base_url = $base_url;
		$rate_landing_config = \Drupal::configFactory()->getEditable('demo.settings')->get("rates_landing_config");
		$form['default_success'] = array(
			'#type' => 'text_format',
			'#title' => $this->t('Success message for default rates'),
			'#required' => TRUE,
			'#default_value' => $rate_landing_config['default_success'],
			
		);	
		$form['qrq_success'] = array(
			'#type' => 'text_format',
			'#title' => $this->t('Success message for quick rate quote'),
			'#required' => TRUE,
			'#default_value' => $rate_landing_config['qrq_success'],
			
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
	  $formvalues = array();
	  $formvalues['default_success'] = $formst['default_success']['value'];
	  $formvalues['qrq_success'] = $formst['qrq_success']['value'];
	  \Drupal::configFactory()->getEditable('demo.settings')->set("rates_landing_config", $formvalues)->save();
	  drupal_set_message(t('Configuration saved.'), 'status');
	}  
}
?>