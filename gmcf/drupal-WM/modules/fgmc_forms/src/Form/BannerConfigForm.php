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
class BannerConfigForm extends FormBase  {

	public function getFormId() {
		return 'fgmc_forms_banner_config_form';
	}
	public function setFormType($type) {
		return $this->formtype = $type;
	}

	 public function buildForm(array $form, FormStateInterface $form_state) {
		global $base_url;
		$this->base_url = $base_url;
		$banner_config = \Drupal::configFactory()->getEditable('demo.settings')->get("banner_config");
		$form['banner_title'] = array(
			'#type' => 'text_format',
			'#title' => $this->t('Title'),
			'#required' => TRUE,
			'#default_value' => $banner_config['banner_title'],
			
		);	
		$form['banner_subtitle'] = array(
			'#type' => 'text_format',
			'#title' => $this->t('Sub Title'),
			'#required' => TRUE,
			'#default_value' => $banner_config['banner_subtitle'],
			
		);
		$form['banner_image'] = array(
			'#type' => 'managed_file',
			'#title' => $this->t('Image'),
			'#upload_location' => 'public://images/',
			'#default_value' => array($banner_config['banner_id']),
			'#required' => TRUE,
			'#description' => 'The minimum allowed image size expressed as WIDTH×HEIGHT (e.g. 1600x795) <br/> Allowed types: png gif jpg jpeg',
		);			  
	    $form['submit'] = array(
			'#type' => 'submit',
			'#value' => t('Save')
	   );
	   return $form;
	}
	public function validateForm(array &$form, FormStateInterface $form_state) {
		if ($form_state->hasValue('banner_title')) {
		 $title = $form_state->getValue('banner_title');

		 if (strlen(trim(strip_tags($title['value']))) > 150) {
			$form_state->setErrorByName('banner_title', "Title Can't be more than 150");
		 }

	  }
	  if ($form_state->hasValue('banner_subtitle')) {
		 $desc = $form_state->getValue('banner_subtitle');

		 if (strlen(trim(strip_tags($desc['value']))) > 150) {
			$form_state->setErrorByName('banner_subtitle', "Description Can't be more than 150");
		 }

	  }
	}
	public function submitForm(array &$form, FormStateInterface $form_state) {
	  $formst = $form_state->getValues();
	  $fid = $form_state->getValue ('banner_image')[0];
	  $file = \Drupal\file\Entity\File::load($fid);
	  $banner_path = $file->getFileName();
	  $formvalues = array();
	  $formvalues['banner_title'] = $formst['banner_title']['value'];
	  $formvalues['banner_subtitle'] = $formst['banner_subtitle']['value'];
	  $formvalues['banner_image'] = $banner_path;
	  $formvalues['banner_id'] = $fid;	  
	  \Drupal::configFactory()->getEditable('demo.settings')->set("banner_config", $formvalues)->save();
	  drupal_set_message(t('Configuration saved.'), 'status');
	}  
}
?>