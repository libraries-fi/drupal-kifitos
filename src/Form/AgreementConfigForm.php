<?php

namespace Drupal\kifitos\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\kifitos\AgreementConfigInterface as Agreement;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AgreementConfigForm extends EntityForm {
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $form['mode'] = [
      '#type' => 'select',
      '#title' => $this->t('Operating mode'),
      '#required' => TRUE,
      '#description' => $this->t('Behaviour for notifying about terms.'),
      '#default_value' => $this->entity->getMode(),
      '#empty_option' => '',
      '#options' => [
        Agreement::MESSAGE => $this->t('Persistent message'),
        Agreement::ROUTE_GUARD => $this->t('Replace page'),
      ],
    ];

    $form['roles'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Roles'),
      '#required' => TRUE,
      '#description' => $this->t('Roles that this agreement is enforced on.'),
      '#default_value' => implode("\n", $this->entity->getRoles()),
    ];

    $form['routes'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Routes'),
      '#description' => $this->t('Routes that cannot be accessed without signing the agreement.'),
      '#default_value' => implode("\n", $this->entity->getRoutes()),
    ];

    $form['message'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Message'),
      '#description' => $this->t('Used with mode \'message\'.'),
      '#default_value' => $this->entity->getMessage(),
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $roles = array_filter(preg_split('/[\n\s]/', $form_state->getValue('roles')));
    $this->entity->setRoles($roles);

    $routes = array_filter(preg_split('/[\n\s]/', $form_state->getValue('routes')));
    $this->entity->setRoutes($routes);

    $this->entity->setMode($form_state->getValue('mode'));

    $message = $form_state->getValue('message');
    preg_match('/<p>(.+)<\/p>/', $message['value'], $body);
    $this->entity->setMessage($body[1]);
    $this->entity->setMessageFormat($message['format']);
  }

  public function save(array $form, FormStateInterface $form_state) {
    $this->entity->save();
    $this->messenger()->addStatus($this->t('Configuration updated.'));
  }
}
