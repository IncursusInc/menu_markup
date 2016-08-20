<?php

namespace Drupal\menu_markup\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @file
 * Configure form for the menu_markup options.
 */
class ConfigureForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   *
   * @return string
   *    Returns the form ID.
   */
  public function getFormId() {

    return 'menu_markup_configure_form';
  }

  /**
   * {@inheritdoc}
   *
   * @return array
   *    Returns an array of editable config names.
   */
  protected function getEditableConfigNames() {

    return [
      'menu_markup.settings',
    ];
  }

  /**
   * {@inheritdoc}
   *
   * @param array $form
   *    The form object.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *    The form state interface.
   *
   * @return mixed
   *    Returns the result of the buildForm() method of the parent.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('menu_markup.settings');

    $form['menu_markup_configuration'] = array(
      '#type' => 'textarea',
      '#title' => t('Menu Markup Configuration'),
      '#required' => TRUE,
      '#rows' => 10,
      '#default_value' => $config->get('config'),
      '#description' => t('Enter line values in the following format:  MENU TITLE|OPEN MARKUP|CLOSE MARKUP|OPTIONAL_CONTENT_TYPE_MACHINENAME'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   *
   * @param array $form
   *    The form object.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *    The form state interface.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   *
   * @param array $form
   *    The form object.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *    The form state interface.
   *
   * @return mixed
   *    Returns the result of the submitForm() method of the parent.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Flush Drupal cache so that the newly rebuilt menu items will appear :)
    drupal_flush_all_caches();

    $config = $this->config('menu_markup.settings');
    $config->set('config', $form_state->getValue('menu_markup_configuration'))->save();

    parent::submitForm($form, $form_state);
  }

}
