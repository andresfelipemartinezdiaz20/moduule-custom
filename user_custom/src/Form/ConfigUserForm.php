<?php 

namespace Drupal\user_custom\Form;  // The namespace should be 'Form', not 'ConfigForm'.

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form for configuring labels in the user registration form.
 */
class ConfigUserForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'user_custom_config_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['user_custom.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Label for the Full Name field
    $form['full_name_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label for the Full Name field'),
      '#default_value' => $this->config('user_custom.settings')->get('full_name_label'),
      '#required' => TRUE,
    ];

    // Label for the Email field
    $form['email_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label for the Email field'),
      '#default_value' => $this->config('user_custom.settings')->get('email_label'),
      '#required' => TRUE,
    ];

    // Label for the DNI field
    $form['dni_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label for the DNI field'),
      '#default_value' => $this->config('user_custom.settings')->get('dni_label'),
      '#required' => TRUE,
    ];

    // Label for the Birth Date field
    $form['birth_date_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label for the Birth Date field'),
      '#default_value' => $this->config('user_custom.settings')->get('birth_date_label'),
      '#required' => TRUE,
    ];

    // The "Save configuration" button is handled by the ConfigFormBase API
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Save the custom configurations
    $this->config('user_custom.settings')
      ->set('full_name_label', $form_state->getValue('full_name_label'))
      ->set('email_label', $form_state->getValue('email_label'))
      ->set('dni_label', $form_state->getValue('dni_label'))
      ->set('birth_date_label', $form_state->getValue('birth_date_label'))
      ->save();

    // Success message
    $this->messenger()->addMessage($this->t('Configuration has been saved.'));
  }
}
