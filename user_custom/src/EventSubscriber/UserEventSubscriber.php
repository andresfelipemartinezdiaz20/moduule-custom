<?php

namespace Drupal\user_custom\EventSubscriber;

use Drupal\Core\EventSubscriber\EventSubscriberInterface;
use Drupal\user\Event\UserEvents;
use Drupal\user\Event\UserEvent;
use Drupal\user\Entity\User;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event Subscriber for user creation and update events.
 */
class UserEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // The event to listen for is user creation
    $events[UserEvents::USER_INSERT][] = 'onUserInsert';
    $events[UserEvents::USER_UPDATE][] = 'onUserUpdate';

    return $events;
  }

  /**
   * Reaction to a new user creation.
   *
   * @param \Drupal\user\Event\UserEvent $event
   *   The event containing the user.
   */
  public function onUserInsert(UserEvent $event) {
    // Get the user that was created
    $user = $event->getUser();

    // Logic to assign a tag to the user
    $this->assignTag($user);
  }

  /**
   * Reaction to a user update.
   *
   * @param \Drupal\user\Event\UserEvent $event
   *   The event containing the user.
   */
  public function onUserUpdate(UserEvent $event) {
    // Get the user that was updated
    $user = $event->getUser();

    // Logic to perform actions when a user is updated
    // In this case, assign a tag again (if desired)
    $this->assignTag($user);
  }

  /**
   * Function to assign a tag to a user.
   *
   * @param \Drupal\user\Entity\User $user
   *   The user entity object.
   */
  private function assignTag(User $user) {
    // Here you should define the logic to retrieve the tag.
    // In this case, we will load a specific taxonomy term as an example.

    // Load the tag term, for example "Default Tag"
    $tag = Term::load(1); // Assuming the term ID is 1
    if ($tag) {
      // Assign the term to the 'field_tag' field of the user
      $user->field_tag->target_id = $tag->id();
      $user->save();
    }
  }
}
