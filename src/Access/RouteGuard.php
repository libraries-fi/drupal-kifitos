<?php

namespace Drupal\kifitos\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\UserDataInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

class RouteGuard implements AccessInterface {
  protected $userData;

  public static function userCacheTag($user) {
    $uid = is_object($user) ? $user->id() : $user;
    return sprintf('kifitos.user:%d', $uid);
  }

  public function __construct(UserDataInterface $user_data) {
    $this->userData = $user_data;
  }

  public function access(AccountInterface $account, Route $route) {
    $aid = $route->getDefault('_kifitos');
    $accepted = $this->userData->get('kifitos', $account->id(), sprintf('agreement:%d', $aid));
    $result = AccessResult::allowedIf($accepted);
    $result->addCacheTags([self::userCacheTag($account)]);
    return $result;
  }
}
