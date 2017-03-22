<?php

namespace Drupal\kifitos;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

class AgreementAccessControlHandler extends EntityAccessControlHandler {
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    switch ($operation) {
      case 'view':
      case 'approve':
        // Return here only if this agreement is enabled for the user.
        if (array_intersect($entity->getConfig()->getRoles(), $account->getRoles())) {
          return AccessResult::allowed();
        }

      // case 'view':
      case 'update':
      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'administer kifitos');

      default:
        return AccessResult::neutral();
    }
  }
}
