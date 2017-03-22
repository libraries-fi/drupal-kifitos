<?php

namespace Drupal\kifitos\Controller;

use Drupal\Core\Entity\Controller\EntityViewController;
use Drupal\kifitos\AgreementInterface;

class AgreementViewController extends EntityViewController {
  public function view(AgreementInterface $kifitos) {
    return parent::view($kifitos);
  }
}
