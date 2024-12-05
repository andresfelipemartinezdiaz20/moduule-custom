<?php

namespace Drupal\user_custom\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Form for registering users with custom fields.
 */
class UserRegistryForm extends FormBase {

  protected $config;

  /**
   * Constructor to inject configuration.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->config = $config_factory->get('user_custom.settings');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('config.factory'));
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'user_custom_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get custom labels from configuration
    $name_label = $this->config->get('full_name_label') ?: $this->t('Full Name');
    $email_label = $this->config->get('email_label') ?: $this->t('Email Address');
    $dni_label = $this->config->get('dni_label') ?: $this->t('DNI');
    $birth_date_label = $this->config->get('birth_date_label') ?: $this->t('Birth Date');

    // Form fields with custom labels
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $name_label,
      '#required' => TRUE,
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $email_label,
      '#required' => TRUE,
    ];

    $form['dni'] = [
      '#type' => 'textfield',
      '#title' => $dni_label,
      '#required' => TRUE,
    ];

    $form['birth_date'] = [
      '#type' => 'date',
      '#title' => $birth_date_label,
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Register User'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get sanitized values
    $name = trim($form_state->getValue('name'));
    $email = trim($form_state->getValue('email'));
    $dni = trim($form_state->getValue('dni'));
    $birth_date = $form_state->getValue('birth_date');

    // Sanitize inputs
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $dni = filter_var($dni, FILTER_SANITIZE_STRING);

    // Check if a user with the same DNI already exists
    $existing_user = \Drupal::entityTypeManager()->getStorage('user')->loadByProperties(['field_dni' => $dni]);

    // If the DNI is already registered, show an error message and stop the process
    if (!empty($existing_user)) {
      \Drupal::messenger()->addError($this->t('A user with this DNI is already registered.'));
      return;
    }

    // Create the user
    $user = User::create([
      'name' => $name,
      'mail' => $email,
      'status' => 1, // Active user
    ]);

    // Save the DNI in the custom field
    $user->set('field_dni', $dni);

    // Save the birth date
    $user->set('field_birth_date', $birth_date);

    // Save the user
    $user->save();

    // Success message
    $this->messenger()->addMessage($this->t('User successfully created.'));
  }

  /**
   * Form validation to ensure the user is of legal age.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Get the birth date
    $birth_date = $form_state->getValue('birth_date');

    // Convert the birth date into a DateTime object
    $birth_date = DrupalDateTime::createFromFormat('Y-m-d', $birth_date);

    // Get the current date
    $current_date = new DrupalDateTime();

    // Calculate the age
    $age = $current_date->diff($birth_date)->y;

    // Check if the user is under 18 years old
    if ($age < 18) {
      $form_state->setErrorByName('birth_date', $this->t('You must be at least 18 years old to register.'));
    }
  }
}
