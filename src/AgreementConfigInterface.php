<?php

namespace Drupal\kifitos;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

interface AgreementConfigInterface {

  public const ROUTE_GUARD = 1;
  public const MESSAGE = 2;

  public function getLabel();
  public function setLabel($label);
  public function getRoles();
  public function setRoles(array $roles);
  public function getMode();
  public function setMode($mode);
  public function getMessage();
  public function setMessage($message);
  public function getMessageFormat();
  public function setMessageFormat($format);
}
