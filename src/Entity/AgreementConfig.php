<?php

namespace Drupal\kifitos\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\kifitos\AgreementConfigInterface;

/**
 * Defines the contact form entity.
 *
 * @ConfigEntityType(
 *   id = "kifitos_config",
 *   label = @Translation("Agreement configuration"),
 *   handlers = {
 *     "access" = "Drupal\kifitos\AgreementConfigAccessControlHandler",
 *     "list_builder" = "Drupal\kifimail\TemplateListBuilder",
 *     "form" = {
 *       "default" = "Drupal\kifitos\Form\AgreementConfigForm",
 *     }
 *   },
 *   config_prefix = "kifitos_config",
 *   admin_permission = "administer kifimail",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {},
 *   config_export = {
 *     "id",
 *     "label",
 *     "roles",
 *     "routes",
 *     "mode",
 *     "message",
 *     "format",
 *   }
 * )
 */
class AgreementConfig extends ConfigEntityBase implements AgreementConfigInterface {
  protected $id;
  protected $label;
  protected $roles = [];
  protected $routes = [];
  protected $mode;
  protected $format;

  /**
   * Used when $mode is MESSAGE.
   *
   * Message can contain HTML formatting.
   */
  protected $message;

  public function getLabel() {
    return $this->label;
  }

  public function setLabel($label) {
    $this->label = $label;
  }

  public function getRoles() {
    return $this->roles;
  }

  public function setRoles(array $roles) {
    $this->roles = $roles;
  }

  public function getRoutes() {
    return $this->routes;
  }

  public function setRoutes(array $routes) {
    $this->routes = $routes;
  }

  public function getMode() {
    return $this->mode;
  }

  public function setMode($mode) {
    $this->mode = $mode;
  }

  public function getMessage() {
    return $this->message;
  }

  public function setMessage($message) {
    $this->message = $message;
  }

  public function getMessageFormat() {
    return $this->format;
  }

  public function setMessageFormat($format) {
    $this->format = $format;
  }
}
