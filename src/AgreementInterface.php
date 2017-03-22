<?php

namespace Drupal\kifitos;

use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\user\EntityOwnerInterface;

interface AgreementInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface, RevisionLogInterface {
  public function getTitle();
  public function setTitle($title);
  public function getBody();
  public function setBody($text);
  public function getCreatedTime();
  public function setCreatedTime($timestamp);
}
