<?php

namespace Drupal\kifitos\Form;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\Controller\EntityViewController;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\kifitos\Access\RouteGuard;
use Drupal\user\UserDataInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AgreementApproveForm extends ContentEntityForm {
  protected $userData;

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager'),
      $container->get('user.data')
    );
  }

  public function __construct(EntityManagerInterface $entity_manager, UserDataInterface $user_data) {
    parent::__construct($entity_manager);
    $this->userData = $user_data;
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    parent::buildForm($form, $form_state);

    $form['view'] = $this->view($form, $form_state);
    $form['actions'] = $this->actionsElement($form, $form_state);

    return $form;
  }

  public function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['submit']['#value'] = $this->t('Approve');
    return $actions;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $uid = $this->currentUser()->id();
    $this->userData->set('kifitos', $uid, sprintf('agreement:%d', $this->entity->id()), 1);
    $form_state->setRedirect('user.page');

    Cache::invalidateTags([RouteGuard::userCacheTag($uid)]);
  }

  public function view(array $form, FormStateInterface $form_state) {
    $view = [];
    $view['body'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Terms of service'),

      'value' => [
        '#markup' => nl2br($this->entity->getBody()),
      ]
    ];

    return $view;
  }
}
