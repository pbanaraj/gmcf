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
class JobEmailConfigForm extends FormBase  {

	public function getFormId() {
		return 'fgmc_forms_job_email_config_form';
	}
	public function setFormType($type) {
		return $this->formtype = $type;
	}

	 public function buildForm(array $form, FormStateInterface $form_state) {
		$job_email_config = \Drupal::configFactory()->getEditable('demo.settings')->get("job_email_config");
		$form['job_email'] = array(
			'#type' => 'email',
			'#title' => $this->t('Email'),
			'#required' => TRUE,
			'#default_value' => $job_email_config['job_email'],
			
		);		
	    $form['submit'] = array(
			'#type' => 'submit',
			'#value' => t('Save')
	   );
	   return $form;
	}
	public function validateForm(array &$form, FormStateInterface $form_state) {
		if ($form_state->hasValue('job_email')) {
		 $email = $form_state->getValue('job_email');

		 if (!(\Drupal::service('email.validator')->isValid($email))) {
			$form_state->setErrorByName('job_email', "Please enter valid email address.");
		 }

	  }
	}
	public function submitForm(array &$form, FormStateInterface $form_state) {
	  $formst = $form_state->getValues();	  
	  if(\Drupal::configFactory()->getEditable('demo.settings')->set("job_email_config", $formst)->save())
		drupal_set_message(t('Configuration saved.'), 'status');
	}  
}
?>