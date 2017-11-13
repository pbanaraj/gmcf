<?php
/**
 * @file
 * Contains \Drupal\fgmc_forms\Form\ProductDetailsConfigForm
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
class ProductDetailsConfigForm extends FormBase  {

	public function getFormId() {
		return 'fgmc_forms_product_details_config_form';
	}
	public function setFormType($type) {
		return $this->formtype = $type;
	}

	 public function buildForm(array $form, FormStateInterface $form_state) {
		global $base_url;
		$this->base_url = $base_url;
		
		$prod_config = \Drupal::configFactory()->getEditable('demo.settings')->get("pd_config");
		$form['pd_title'] = array(
            '#type' => 'text_format',
			'#title' => $this->t('Title'),
			'#default_value' => $prod_config['pd_title'],			
			'#required' => TRUE,			
			'#suffix' => '<span class="title-valid-message"></span>'
			
        );
		
	
		$form['pd_desc'] = array(
			'#type' => 'text_format',
			'#title' => $this->t('Product Detail Description'),
			'#default_value' => $prod_config['pd_desc'],
			'#required' => TRUE,
		);
				  
	    $form['submit'] = array(
			'#type' => 'submit',
			'#value' => t('Save Product Detail Config')
	   );
	   //$form['#attached']['library'][] = 'fgmc_forms/char_validate';
	   return $form;
	}
	
	
	public function validateForm(array &$form, FormStateInterface $form_state) {
		if ($form_state->hasValue('pd_title')) {
		 $title = $form_state->getValue('pd_title');

		 if (strlen(trim(strip_tags($title['value']))) > 400) {
			$form_state->setErrorByName('pd_title', "Title Can't be more than 400");
		 }

	  }
	  if ($form_state->hasValue('pd_desc')) {
		 $desc = $form_state->getValue('pd_desc');

		 if (strlen(trim(strip_tags($desc['value']))) > 400) {
			$form_state->setErrorByName('pd_desc', "Description Can't be more than 400");
		 }

	  }
	}
	public function submitForm(array &$form, FormStateInterface $form_state) {
	  $formst = $form_state->getValues();	  
	  $formvalues = array();
	  $formvalues['pd_title'] = $formst['pd_title']['value'];
	  $formvalues['pd_desc'] = $formst['pd_desc']['value'];
	  
	  \Drupal::configFactory()->getEditable('demo.settings')->set("pd_config", $formvalues)->save();
	  drupal_set_message(t('Configuration saved.'), 'status');
	
	}  
}
?>