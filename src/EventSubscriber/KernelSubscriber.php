<?php

namespace Drupal\kifitos\EventSubscriber;

use Drupal;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerResolverInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\kifitos\AgreementResolver;
use Drupal\kifitos\AgreementInterface;
use Drupal\kifitos\AgreementConfigInterface;
use Drupal\user\UserDataInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpFoundation\Request;

class KernelSubscriber implements EventSubscriberInterface {
  protected $controllerResolver;
  protected $agreementResolver;
  protected $userData;
  protected $banned = [];

  public static function getSubscribedEvents() {
    return [
      // KernelEvents::REQUEST => [['onRequest']],
      KernelEvents::CONTROLLER => [['onController']],
    ];
  }

  public function __construct(ControllerResolverInterface $controller_resolver, AgreementResolver $agreement_resolver, ConfigFactoryInterface $config, UserDataInterface $user_data) {
    $this->controllerResolver = $controller_resolver;
    $this->agreementResolver = $agreement_resolver;
    $this->config = $config;
    $this->userData = $user_data;
  }

  public function onController(FilterControllerEvent $event) {
    $user = Drupal::currentUser();

    $controller = $event->getController();
    $request = $event->getRequest();
    $route = $request->attributes->get('_route');
    $agreement = $this->agreementResolver->getAgreement($request, $user);

    if ($agreement && !$this->hasAccepted($user, $agreement)) {
      switch ($agreement->getConfig()->getMode()) {
        case AgreementConfigInterface::ROUTE_GUARD:
          $path = $agreement->url();
          $copy = Request::create($agreement->url());
          $copy->attributes->set('_controller', 'Drupal\kifitos\Controller\AgreementController::view');

          $request->attributes->set('kifitos', $agreement);

          $controller = $this->controllerResolver->getControllerFromDefinition('Drupal\kifitos\Controller\AgreementController::approve', $path);
          $event->setController($controller);
          break;

        case AgreementConfigInterface::MESSAGE:
          if ($event->getRequest()->isMethod('get')) {
            \Drupal::messenger()->addWarning(new FormattableMarkup($agreement->getConfig()->getMessage(), []), FALSE);
          }
          break;
      }
    }
  }

  protected function hasAccepted(AccountInterface $user, AgreementInterface $agreement) {
    return $this->userData->get('kifitos', $user->id(), sprintf('agreement:%d', $agreement->id()));
  }
}
