<?php
/**
 * @file
 * Contains \Drupal\menu_markup\Form\ConfigureForm.
 */

namespace Drupal\menu_markup\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure form.
 */
class ConfigureForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
		return 'menu_markup_configure_form';
  }

/** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'menu_markup.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

		$config = $this->config('menu_markup.settings');

		$form['menu_markup_configuration'] = array(
			'#type' => 'textarea',
			'#title' => t('Menu Markup Configuration'),
			'#required' => TRUE,
			'#default_value' => $config->get('config'),
			'#description' => t('Enter line values in the following format:  MENU TITLE|OPEN MARKUP|CLOSE MARKUP'),
		);

		return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
		$config = \Drupal::service('config.factory')->getEditable('menu_markup.settings');
		$config->set('config', $form_state->getValue('menu_markup_configuration'))->save();

		parent::submitForm($form, $form_state);
  }
}
?>
