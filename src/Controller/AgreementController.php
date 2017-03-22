<?php

namespace Drupal\kifitos\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\kifitos\AgreementInterface;

class AgreementController extends ControllerBase {
  public function configure(AgreementInterface $kifitos) {
    $config = $kifitos->getConfig();
    return $this->entityFormBuilder()->getForm($config);
  }

  public function approve(AgreementInterface $kifitos) {
    return $this->entityFormBuilder()->getForm($kifitos, 'approve');
  }
}
