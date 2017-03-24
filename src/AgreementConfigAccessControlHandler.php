<?php

namespace Drupal\kifitos;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

class AgreementConfigAccessControlHandler extends EntityAccessControlHandler {
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'administer kifitos_config');
  }
}
