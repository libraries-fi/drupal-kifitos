<?php

namespace Drupal\kifitos\Entity;

use Drupal;
use Exception;
use Drupal\Component\Datetime\DateTimePlus as DateTime;
use Drupal\Component\Utility\Unicode;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\kifitos\AgreementInterface;
use Drupal\kifitos\AgreementConfigInterface;
use Drupal\user\UserInterface;

/**
 * @ContentEntityType(
 *   id = "kifitos",
 *   label = @Translation("Terms of service"),
 *   handlers = {
 *     "storage" = "Drupal\Core\Entity\Sql\SqlContentEntityStorage",
 *     "access" = "Drupal\kifitos\AgreementAccessControlHandler",
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data" = "Drupal\kifitos\AgreementViewsData",
 *     "form" = {
 *       "approve" = "Drupal\kifitos\Form\AgreementApproveForm",
 *       "default" = "Drupal\kifitos\Form\AgreementForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *   },
 *   base_table = "kifitos_agreements",
 *   revision_table = "kifitos_agreements_revision",
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "label" = "title",
 *     "langcode" = "langcode",
 *     "revision" = "vid",
 *     "status" = "status",
 *   },
 *   links = {
 *     "approve" = "/admin/content/kifitos/{kifitos}",
 *     "canonical" = "/admin/content/kifitos/{kifitos}/view",
 *     "configure" = "/admin/content/kifitos/{kifitos}/configure",
 *     "delete-form" = "/admin/content/kifitos/{kifitos}/delete",
 *     "edit-form" = "/admin/content/kifitos/{kifitos}/edit",
 *     "revision" = "/admin/content/kifitos/{kifitos}/revisions/{kifitos_revision}/view",
 *     "version-history" = "/admin/content/kifitos/{kifitos}/revisions",
 *   }
 * )
 */
class Agreement extends ContentEntityBase implements AgreementInterface {
  use EntityChangedTrait;

  public function getConfig() {
    // retur
    // return $this->get('config')->target_entity;
    return Drupal::entityTypeManager()->getStorage('kifitos_config')->load($this->id());
  }

  public function setConfig($config) {
    $this->set('config', $config);
  }

  public function getTitle() {
    return $this->get('title')->value;
  }

  public function setTitle($title) {
    $this->set('title', $title);
  }

  public function getBody() {
    return $this->get('body')->value;
  }

  public function setBody($body) {
    $this->set('body', $body);
  }

  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->getEntityKey('uid');
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRevisionCreationTime() {
    return $this->get('revision_timestamp')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setRevisionCreationTime($timestamp) {
    $this->set('revision_timestamp', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRevisionAuthor() {
    return $this->getRevisionUser();
  }

  /**
   * {@inheritdoc}
   */
  public function getRevisionUser() {
    return $this->get('revision_uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setRevisionAuthorId($uid) {
    $this->setRevisionUserId($uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setRevisionUser(UserInterface $user) {
    $this->set('revision_uid', $user);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRevisionUserId() {
    return $this->get('revision_uid')->entity->id();
  }

  /**
   * {@inheritdoc}
   */
  public function setRevisionUserId($user_id) {
    $this->set('revision_uid', $user_id);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRevisionLogMessage() {
    return $this->get('revision_log')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setRevisionLogMessage($revision_log_message) {
    $this->set('revision_log', $revision_log_message);
    return $this;
  }

  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage);

    if (!$this->getConfig()) {
      $config = Drupal::entityTypeManager()->getStorage('kifitos_config')->create([
        'id' => $this->id(),
        'label' => sprintf('Configuration for agreement #%d', $this->id()),
      ]);
      $config->save();
    }
  }

  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('Agreement ID'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Question entity'))
      ->setReadOnly(TRUE);

    $fields['vid'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Revision ID'))
      ->setDescription(t('Agreement revision ID'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The node language code.'))
      ->setRevisionable(TRUE)
      ->setDisplayConfigurable('form', FALSE);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -10,
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -10,
      ]);

    $fields['body'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Content'))
      ->setDescription(t('Agreement conditions'))
      ->setSetting('max_length', 10000)
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'weight' => 0,
        'settings' => [
          'rows' => 15,
        ],
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
      ]);

    $fields['revision_log'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Revision log message'))
      ->setDescription(t('Briefly describe the changes you have made.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textarea',
        'weight' => 0,
        'settings' => [
          'rows' => 4,
        ],
      ]);

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    $fields['user'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Author'))
      ->setDescription(t('Creator of the agreement'))
      ->setSettings(['target_type' => 'user'])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', FALSE)
      ->setDisplayOptions('view', [
        'type' => 'hidden',
        'format' => 'hidden',
      ]);

    // $fields['config'] = BaseFieldDefinition::create('entity_reference')
    //   ->setLabel(t('Configuration'))
    //   ->setDescription(t('Configuration for this entity'))
    //   ->setSettings(['target_type' => 'kifitos_config'])
    //   ->setDisplayConfigurable('form', FALSE)
    //   ->setDisplayConfigurable('view', FALSE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Status'))
      ->setDescription(t('A boolean indicating whether the agreement is enabled.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setDefaultValue(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setDescription(t('The time that the node was created.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'timestamp',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the node was last edited.'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }
}
