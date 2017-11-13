<?php
/**
 * @file
 * Contains \Drupal\fgmc_forms\Form\SectionForm.
 */

namespace Drupal\fgmc_forms\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
Use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Section form.
 */
class HomePagePanelSettingsForm extends FormBase  {

	public function getFormId() {
		return 'fgmc_forms_section_form';
	}
	 public function buildForm(array $form, FormStateInterface $form_state) {
		global $base_url;
		$this->base_url = $base_url;
		$form['mytable'] = array(
								'#type' => 'table',
								'#header' => array(t('Section name'),t('Title'), t('Status'), t('Weight'),t('Enable/Disable'),t('Actions')),								    
								'#tabledrag' => array(
								  array(
									'action' => 'order',
									'relationship' => 'sibling',
									'group' => 'mytable-order-weight',
								  ),
								),
							);
		$section_keys = \Drupal::config('demo.settings')->get('section_keys');
		//echo "<pre>";print_r($section_keys);die;
		foreach ($section_keys as $id => $entity) {
			// TableDrag: Mark the table row as draggable.
			$form['mytable'][$entity['section_key']]['#attributes']['class'][] = 'draggable';
			// TableDrag: Sort the table row according to its existing/configured weight.
			$form['mytable'][$entity['section_key']]['#weight'] = $key;
			$blockid = $entity['section_key'];
			$section_title = \Drupal::config('block.block.'.$blockid)->get('settings.label');
			$spanid = $blockid.'_title';
			// Some table columns containing raw markup.
			$form['mytable'][$entity['section_key']]['section_name'] = array(
				'#plain_text' => $entity['section_label']
			);
			$form['mytable'][$entity['section_key']]['section_label'] = array(
				'#type' => 'suffix',
			  '#suffix' => "<span id='$spanid'>$section_title</span>",
			);
			if($entity['section_enable'] == 1) {
				$enadisparam = "Enabled";
			} else {
				$enadisparam = "Disabled";
			}
			$form['mytable'][$entity['section_key']]['section_enable'] = array(
			  '#plain_text' => $enadisparam,
			);
			$form['mytable'][$entity['section_key']]['weight'] = array(
			  '#type' => 'weight',
			  '#title' => t('Weight'),
			  '#title_display' => 'invisible',
			  '#default_value' => $key,
			  // Classify the weight element for #tabledrag.
			  '#attributes' => array('class' => array('mytable-order-weight')),
			);
			// Operations (dropbutton) column.
			$form['mytable'][$entity['section_key']]['section_enable_text'] = array(
			  '#type' => 'checkbox'			  
			);			
			$sectionkey = $entity['section_key'];
	
			$form['mytable'][$entity['section_key']]['operations'] = array(
				'#type' => 'suffix',
				'#suffix' => "<a href='$this->base_url/admin/edit_fields/$sectionkey' class='use-ajax' data-dialog-type='modal'>Edit</a>"				
			);			
			$form['mytable'][$entity['section_key']]['section_label_text'] = array(
			  '#type' => 'hidden',
			  '#title_display' => 'invisible',
			  '#default_value' => $entity['section_label'],
			);			
			if($entity['section_enable'] == 1) $form['mytable'][$entity['section_key']]['section_enable_text']['#default_value'] = 1; else $form['mytable'][$entity['section_key']]['section_enable_text']['#default_value'] = 0; 
		}
		$form['actions'] = array('#type' => 'actions');
		$form['actions']['submit'] = array(
									'#type' => 'submit',
									'#value' => t('Save'),    
									'#tableselect' => TRUE,
								  );
		$form['#attached']['library'][] = 'fgmc_forms/fgmc_forms_custom_css';
		return $form;
	}
	public function validateForm(array &$form, FormStateInterface $form_state) {
		
	}

	public function submitForm(array &$form, FormStateInterface $form_state) {
	  $basepath = \Drupal::request()->getBasePath();
	  $formst = $form_state->getValue('mytable');
	  $section_elements = array();
	  foreach($formst as $key=>$val) {
		$formvalues = array();
		$formvalues['section_key'] = $key;
		$formvalues['section_label'] = $val['section_label_text'];
		$formvalues['section_enable'] = $val['section_enable_text'];
		array_push($section_elements,$formvalues);
	  }
	  \Drupal::configFactory()->getEditable('demo.settings')->set("section_keys", $section_elements)->save();
	}
  
}
?>