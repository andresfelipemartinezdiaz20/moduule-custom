<?php

namespace Drupal\user_custom\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller to handle user-related functionalities.
 */
class UserController extends ControllerBase {

  /**
   * Page to display registered users in a table format.
   *
   * @return array
   *   The renderable to display the list of registered users.
   */
  public function listUsersAdmin() {
    // Set the number of items per page.
    $limit = 10;
  
    // Create the user query.
    $query = \Drupal::entityQuery('user')
      ->condition('status', 1)  // Only active users
      ->sort('name', 'ASC')  // Sort by name
      ->accessCheck(FALSE);  // Disable access check
  
    // Pagination: execute the query with pagination.
    $user_ids = $query->pager($limit)->execute();
  
    // Load the users by their IDs.
    $users = User::loadMultiple($user_ids);
  
    // Create the table to display the users.
    $rows = [];
    foreach ($users as $user) {
      // Get custom field data (assuming they already exist).
      $dni = isset($user->field_dni) ? $user->field_dni->value : 'N/A';
      $birth_date = isset($user->field_birth_date) ? $user->field_birth_date->value : 'N/A';
      
      // Create a row for each user.
      $rows[] = [
        'data' => [
          $user->name->value,
          $user->mail->value,
          $dni,
          $birth_date,
        ],
      ];
    }
  
    // Create the table.
    $table = [
      '#type' => 'table',
      '#header' => [
        $this->t('Name'),
        $this->t('Email'),
        $this->t('DNI'),
        $this->t('Birth Date'),
      ],
      '#rows' => $rows,
      '#empty' => $this->t('No users found.'),
    ];
  
    // Add sorting functionality to the table.
    $table['#attached']['library'][] = 'core/drupal.tableSort';
  
    // Add the pager.
    $table['pager'] = [
      '#type' => 'pager',
      '#quantity' => $limit,
    ];
  
    return $table;
  }
  

  /**
   * Endpoint to get a user by DNI.
   *
   * @param string $dni
   *   The DNI of the user.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response with the user details.
   */
  public function getUserByDni($dni) {
    // Search for the user by the custom DNI field.
    $users = \Drupal::entityTypeManager()->getStorage('user')->loadByProperties(['field_dni' => $dni]);

    // Check if the user was found.
    if (!empty($users)) {
      $user = reset($users); // Take the first user found.

      // Return the user information in JSON format.
      return new JsonResponse([
        'name' => $user->name->value,  // Get the username.
        'email' => $user->mail->value, // Get the email address.
        'tag' => isset($user->field_tag) && $user->field_tag->entity 
          ? $user->field_tag->entity->label() 
          : '', // Get the tag if available.
      ]);
    }
    else {
      // If the user is not found, return a 404 error.
      return new JsonResponse(['error' => 'User not found'], 404);
    }
  }
}
