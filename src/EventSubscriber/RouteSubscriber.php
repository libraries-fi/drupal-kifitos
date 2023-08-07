<?php

namespace Drupal\kifitos\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\kifitos\AgreementConfigInterface as AgreementConfig;
use Symfony\Component\Routing\RouteCollection;

class RouteSubscriber extends RouteSubscriberBase {
  protected $entityManager;
  protected $configStorage;

  public const ROUTE_GUARD = '_kifitos_route_access';

  public function __construct(EntityTypeManagerInterface $entity_manager) {
    $this->entityManager = $entity_manager;
    $this->configStorage = $entity_manager->getStorage('kifitos_config');
  }

  protected function alterRoutes(RouteCollection $collection) {
    $cids = $this->configStorage->getQuery()
      ->condition('mode', AgreementConfig::ROUTE_GUARD, '<>')
      ->execute();
    $configs = $this->configStorage->loadMultiple($cids);

    foreach ($configs as $config) {
      foreach ($config->getRoutes() as $route_id) {
        if ($route = $collection->get($route_id)) {
          $route->setRequirement(self::ROUTE_GUARD, 'TRUE');
          $route->setDefault('_kifitos', $config->id());
        } else {
          trigger_error(sprintf('%s: Route \'%d\' does not exist.', self::class,  $route_id));
        }
      }
    }
  }
}
