<?php

namespace Drupal\kifitos;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\Request;

class AgreementResolver {
  protected $storage;
  protected $config;

  public function __construct(EntityTypeManagerInterface $entities, CacheBackendInterface $cache) {
    $this->storage = $entities->getStorage('kifitos');
    $this->cache = $cache;
    $this->config = NULL;
  }

  public function getAgreement(Request $request, AccountInterface $account, bool $cache_recreated = NULL) {
    $route = $request->attributes->get('_route');

    $this->getConfig();

    if ($agreement = $this->byRoute($route)) {
      return $agreement;
    }

    if ($agreement = $this->byRole($account)) {
      return $agreement;
    }

  }

  protected function getConfig() {
    if(!$this->config) {
      if($cache = $this->config = $this->cache->get('config', TRUE)) {
        $this->config = $cache->data;
        print_r("Config loytyi:");
        print_r($this->config);
      }
      else {
        $this->reCreateCache();
        print_r("Config piti luoda uudestaan:");
        print_r($this->config);
      }
    }

    return $this->config;
  }

  protected function byRoute($route) {
    if ($item = $this->config['route.' . $route]) {
      return $this->storage->load($item->data);
    }
  }

  protected function byRole(AccountInterface $account) {
    foreach ($account->getRoles() as $role) {
      if ($item = $this->config['role.' . $role]) {
        return $this->storage->load($item->data);
      }
    }
  }

  // NOTE: Only one kifitos_config can be assigned  to one role or one route. Is this desirable?
  protected function populateConfigData(&$data, AgreementConfigInterface $config) {
    foreach ($config->getRoutes() as $route) {
      $data['route.' . $route] = [
        'data' => $config->id()
      ];
    }
    foreach ($config->getRoles() as $role) {
      $data['role.' . $role] = [
        'data' => $config->id()
      ];
    }
  }

  public function reCreateCache() {
    \Drupal::logger('kifitos')->notice("Re-creating config cache... this should not be called often.");
    $storage = \Drupal::service('entity_type.manager')->getStorage('kifitos_config');
    $configs = $storage->loadMultiple();
    $this->cacheConfigs($configs);
  }

  public function cacheSingleConfig(AgreementConfigInterface $config) {
    $data = [];
    $this->populateConfigData($data, $config);
    $this->getConfig();
    $this->config = array_merge($this->config, $data);
    $this->cache->set('config', $this->config);
  }

  public function cacheConfigs(array $configs) {
    $data = [];
    foreach ($configs as $config) {
      $this->populateConfigData($data, $config);
    }
    if (!empty($data)) {
      $data['expire'] = CacheBackendInterface::CACHE_PERMANENT;
      $this->cache->set('config', $data);
      $this->config = $data;
    }
  }
}
