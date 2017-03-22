<?php

namespace Drupal\kifitos;

use Drupal\views\EntityViewsData;

class AgreementViewsData extends EntityViewsData {
  public function getViewsData() {
    $data = parent::getViewsData();
    $data['kifitos']['table']['wizard_id'] = 'kifitos';

    return $data;
  }
}
