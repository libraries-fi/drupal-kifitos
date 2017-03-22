<?php

namespace Drupal\kifitos;

use Drupal\Core\Cache\CacheBackendInterface;

class AgreementConfigCache {
  protected $backend;

  public function __construct(CacheBackendInterface $backend) {
    $this->backend = $backend;
  }

  public function cacheConfig(AgreementConfigInterface $config) {
    $this->cacheConfigs([$config]);
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
    }
    $this->backend->setMultiple($data);
  }
}
