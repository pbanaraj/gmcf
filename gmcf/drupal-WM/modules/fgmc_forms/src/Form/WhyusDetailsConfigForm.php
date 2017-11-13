<?php
/**
 * @file
 * Contains \Drupal\fgmc_forms\Form\WhyusDetailsConfigForm
 */

namespace Drupal\fgmc_forms\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
Use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax;

/**
 * Section form.
 */
class WhyusDetailsConfigForm extends FormBase  {

	public function getFormId() {
		return 'fgmc_forms_whyus_details_config_form';
	}
	public function setFormType($type) {
		return $this->formtype = $type;
	}

	 public function buildForm(array $form, FormStateInterface $form_state) {
		global $base_url;
		$this->base_url = $base_url;
		
		$whyus_config = \Drupal::configFactory()->getEditable('demo.settings')->get("whyus_detail_config");
		$form['whyus_top_title'] = array(
            '#type' => 'text_format',
			'#title' => $this->t('Top Title'),
			'#default_value' => $whyus_config['whyus_top_title'],			
			'#required' => TRUE,			
			'#suffix' => '<span class="top_title-valid-message"></span>'
			
        );
		$form['whyus_top_desc'] = array(
            '#type' => 'text_format',
			'#title' => $this->t('Top Description'),
			'#default_value' => $whyus_config['whyus_top_desc'],			
			'#required' => TRUE,			
			'#suffix' => '<span class="top_desc-valid-message"></span>'
			
        );
	
		$form['whyus_testimony_title'] = array(
			'#type' => 'text_format',
			'#title' => $this->t('Testimonial Title'),
			'#default_value' => $whyus_config['whyus_testimony_title'],
			'#required' => TRUE,
			'#suffix' => '<span class="testimony_title-valid-message"></span>'
		);
		$form['whyus_testimony_desc'] = array(
			'#type' => 'text_format',
			'#title' => $this->t('Testimonial Description'),
			'#default_value' => $whyus_config['whyus_testimony_desc'],
			'#required' => TRUE,
			'#suffix' => '<span class="testimony_desc-valid-message"></span>'
		);
		$form['whyus_our_process_title'] = array(
			'#type' => 'text_format',
			'#title' => $this->t('Our Process Title'),
			'#default_value' => $whyus_config['whyus_our_process_title'],
			'#required' => TRUE,
			'#suffix' => '<span class="process_our_process-valid-message"></span>'
		);
		$form['whyus_our_process_desc'] = array(
			'#type' => 'text_format',
			'#title' => $this->t('Our Process Description'),
			'#default_value' => $whyus_config['whyus_our_process_desc'],
			'#required' => False,
			'#suffix' => '<span class="our_process_desc-valid-message"></span>'
		);
		$form['whyus_loan_process_title'] = array(
			'#type' => 'text_format',
			'#title' => $this->t('Our Loan Process Title'),
			'#default_value' => $whyus_config['whyus_loan_process_title'],
			'#required' => TRUE,
			'#suffix' => '<span class="loan_process_title-valid-message"></span>'
		);
		$form['whyus_loan_process_desc'] = array(
			'#type' => 'text_format',
			'#title' => $this->t('Our Process Description'),
			'#default_value' => $whyus_config['whyus_loan_process_desc'],
			'#required' => TRUE,
			'#suffix' => '<span class="loan_process_desc-valid-message"></span>'
		);		  
	    $form['submit'] = array(
			'#type' => 'submit',
			'#value' => t('Save why us config')
	   );
	   //$form['#attached']['library'][] = 'fgmc_forms/char_validate';
	   return $form;
	}
	
	
	public function validateForm(array &$form, FormStateInterface $form_state) {
		if ($form_state->hasValue('whyus_top_title')) {
		 $title = $form_state->getValue('whyus_top_title');

		 if (strlen(trim(strip_tags($title['value']))) > 150) {
			$form_state->setErrorByName('whyus_top_title', "Top Title Can't be more than 150");
		 }

		}
		if ($form_state->hasValue('whyus_top_desc')) {
		 $desc = $form_state->getValue('whyus_top_desc');

		 if (strlen(trim(strip_tags($desc['value']))) > 500) {
			$form_state->setErrorByName('whyus_top_desc', "Top Description Can't be more than 500");
		 }

		}
		if ($form_state->hasValue('whyus_testimony_title')) {
		 $title = $form_state->getValue('whyus_testimony_title');

		 if (strlen(trim(strip_tags($title['value']))) > 150) {
			$form_state->setErrorByName('whyus_testimony_title', "Testimonial title can't be more than 150");
		 }

		}
		if ($form_state->hasValue('whyus_testimony_desc')) {
		 $desc = $form_state->getValue('whyus_testimony_desc');

		 if (strlen(trim(strip_tags($desc['value']))) > 500) {
			$form_state->setErrorByName('whyus_testimony_desc', "Testimonial description can't be more than 500");
		 }

		}
		if ($form_state->hasValue('whyus_our_process_title')) {
		 $title = $form_state->getValue('whyus_our_process_title');

		 if (strlen(trim(strip_tags($title['value']))) > 150) {
			$form_state->setErrorByName('whyus_our_process_title', "Our process title can't be more than 150");
		 }

		}
		if ($form_state->hasValue('whyus_our_process_desc')) {
		 $desc = $form_state->getValue('whyus_our_process_desc');

		 if (strlen(trim(strip_tags($desc['value']))) > 200) {
			$form_state->setErrorByName('whyus_our_process_desc', "Our process description can't be more than 200");
		 }

		}
		if ($form_state->hasValue('whyus_loan_process_title')) {
		 $title = $form_state->getValue('whyus_loan_process_title');

		 if (strlen(trim(strip_tags($title['value']))) > 150) {
			$form_state->setErrorByName('whyus_loan_process_title', "Loan process title can't be more than 150");
		 }

		}
		if ($form_state->hasValue('whyus_loan_process_desc')) {
		 $desc = $form_state->getValue('whyus_loan_process_desc');

		 if (strlen(trim(strip_tags($desc['value']))) > 500) {
			$form_state->setErrorByName('whyus_loan_process_desc', "Loan process description can't be more than 500");
		 }

		}
	}
	public function submitForm(array &$form, FormStateInterface $form_state) {
	  $formst = $form_state->getValues();	  
	  $formvalues = array();
	  $formvalues['whyus_top_title'] 			= $formst['whyus_top_title']['value'];
	  $formvalues['whyus_top_desc'] 			= $formst['whyus_top_desc']['value'];
	  $formvalues['whyus_testimony_title'] 		= $formst['whyus_testimony_title']['value'];
	  $formvalues['whyus_testimony_desc'] 		= $formst['whyus_testimony_desc']['value'];
	  $formvalues['whyus_our_process_title'] 	= $formst['whyus_our_process_title']['value'];
	  $formvalues['whyus_our_process_desc'] 	= $formst['whyus_our_process_desc']['value'];
	  $formvalues['whyus_loan_process_title'] 	= $formst['whyus_loan_process_title']['value'];
	  $formvalues['whyus_loan_process_desc'] 	= $formst['whyus_loan_process_desc']['value'];
	  
	  \Drupal::configFactory()->getEditable('demo.settings')->set("whyus_detail_config", $formvalues)->save();
	  drupal_set_message(t('Configuration saved.'), 'status');
	  //echo "config saved";
	
	}  
}
?>