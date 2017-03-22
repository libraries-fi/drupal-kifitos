<?php

namespace Drupal\kifitos;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\Request;

class AgreementResolver {
  protected $storage;

  public function __construct(EntityTypeManagerInterface $entities, CacheBackendInterface $cache) {
    $this->storage = $entities->getStorage('kifitos');
    $this->cache = $cache;
  }

  public function getAgreement(Request $request, AccountInterface $account) {
    $route = $request->attributes->get('_route');

    if ($agreement = $this->byRoute($route)) {
      return $agreement;
    }

    if ($agreement = $this->byRole($account)) {
      return $agreement;
    }
  }

  protected function byRoute($route) {
    if ($item = $this->cache->get('route.' . $route, TRUE)) {
      return $this->storage->load($item->data);
    }
  }

  protected function byRole(AccountInterface $account) {
    foreach ($account->getRoles() as $role) {
      if ($item = $this->cache->get('role.' . $role, TRUE)) {
        return $this->storage->load($item->data);
      }
    }
  }

  public function cacheConfigs(array $configs) {
    $data = [];
    foreach ($configs as $config) {
      foreach ($config->getRoutes() as $route) {
        $data['route.' . $route] = [
          'data' => $config->id(),
          'expire' => CacheBackendInterface::CACHE_PERMANENT,
        ];
      }
      foreach ($config->getRoles() as $role) {
        $data['role.' . $role] = [
          'data' => $config->id(),
          'expire' => CacheBackendInterface::CACHE_PERMANENT,
        ];
      }
    }
    if (!empty($data)) {
      $this->cache->setMultiple($data);
    }
  }
}
