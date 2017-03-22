<?php

namespace Drupal\kifitos\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

class AgreementForm extends ContentEntityForm {
  public function form(array $form, FormStateInterface $form_state) {
    return parent::form($form, $form_state);
  }
}
