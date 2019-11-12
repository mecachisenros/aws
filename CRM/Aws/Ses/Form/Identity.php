<?php
use CRM_Aws_ExtensionUtil as E;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Identity Form controller class.
 *
 * @since 1.0
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Aws_Ses_Form_Identity extends CRM_Core_Form {

  use CRM_Aws_EntityFormTrait, CRM_Core_Form_EntityFormTrait {
    CRM_Aws_EntityFormTrait::setEntityFieldsMetadata insteadof CRM_Core_Form_EntityFormTrait;
    CRM_Aws_EntityFormTrait::getEntityDefaults insteadof CRM_Core_Form_EntityFormTrait;
  }

  /**
   * The SES identity.
   *
   * @var string
   * @since 1.0
   */
  protected $_id;

  /**
   * Fields for the entity to be assigned to the template.
   *
   * @var array
   * @since 1.0
   */
  protected $entityFields = [];

  /**
   * Sets the entity fields to be assigned to the form.
   *
   * @since 1.0
   */
  protected function setEntityFields(): void {

    $this->entityFields = CRM_Aws_Ses_BAO_Ses::getFields($this->getApiAction());

    if ($this->isCreateContext()) {
      $this->entityFields = array_filter(
        $this->entityFields,
        function($field) {
          return !in_array(
            $field['name'],
            [
              'BounceTopic',
              'ComplaintTopic',
              'DeliveryTopic',
              'HeadersInBounceNotificationsEnabled',
              'HeadersInComplaintNotificationsEnabled',
              'HeadersInDeliveryNotificationsEnabled',
            ]
          );
        }
      );
      $this->entityFields['Identity']['required'] = 1;
    } else {
      $this->entityFields = array_filter(
        $this->entityFields,
        function($field) {
          return !in_array(
            $field['name'],
            [
              'TopicName',
              'TopicDisplayName',
            ]
          );
        }
      );
      $this->entityFields['Identity']['is_freeze'] = 1;
    }

    $this->setEntityFieldsMetadata();

  }

  /**
   * Retrieves the entity name.
   *
   * @since 1.0
   * @return string $entity
   */
  public function getDefaultEntity(): string {
    return 'Ses';
  }

  /**
   * Retrieve the form context
   * (i.e. the action get, create, delete, etc.).
   *
   * This function must be dclared when
   * using CRM_Core_Form->addSelect().
   *
   * @since 1.0
   * @return string $action The api action based on 'action' param
   */
  public function getDefaultContext(): string {
    return $this->getApiAction();
  }

  /**
   * Sets the delete message.
   *
   * @since 1.0
   */
  public function setDeleteMessage() {
    $this->deleteMessage = E::ts('
      WARNING: This action will delete this identity from the verified lists of identities.'
    ) . ' ' . E::ts('Do you want to continue?');
  }

  /**
   * Retreieves the entity label.
   *
   * @since 1.0
   * @return string $entityLabel
   */
  public function getEntityLabel(): string {
    return E::ts('SES Identity');
  }

  /**
   * Called prior to building the form.
   *
   * @since 1.0
   */
  public function preProcess() {

    $this->_id = CRM_Utils_Request::retrieve('id', 'String');

    $this->setPageTitle($this->getEntityLabel());

    parent::preProcess();

  }

  /**
   * Called prior outputting and rendering the html.
   *
   * @since 1.0
   */
  public function buildQuickForm() {

    $this->buildQuickEntityForm();

    parent::buildQuickForm();

  }

  /**
   * Called when submitting the form.
   *
   * @since 1.0
   */
  public function postProcess() {

    $values = $this->exportValues();

    // FIXME
    // unchecked checkbox fields don't seem to save,
    // I can reproduce on core forms...
    array_map(
      function($element) use (&$values) {
        if ($element->getType() != 'checkbox') {
          return;
        }
        if (!in_array($element->getName(), array_keys($values))) {
          $values[$element->getName()] = 0;
        }
      },
      $this->_elements
    );

    if ($this->isCreateContext() || $this->isEditContext()) {

      $identity = $this->callApi($values);
      $message = 'Identity %1 created.';
      $message .= !empty($identity['values']['Type']) && $identity['values']['Type'] == 'email'
        ? ' A verfication email has been sent to %1.'
        : '';

    } elseif ($this->isDeleteContext()) {
      // FIXME
      // delete generic APIs require 'id' to
      // be present and to be of type int
      $values = [
        'id' => 1,
        'Identity' => $this->getEntityId() ?? CRM_Utils_Request::retrieve('id', 'String'),
      ];

      $this->callApi($values);
      $message = 'Identity %1 deleted.';

    }

    $this->setStatus(
      E::ts(
        $message ?? 'n/a',
        [1 => $values['Identity'] ?? '']
      )
    );

  }

  /**
   * Set default values.
   *
   * @since 1.0
   * @return array $defaults
   */
  public function setDefaultValues() {

    $defaults = [];

    if ($this->isEditContext() || $this->isViewContext()) {
      $identity = civicrm_api3(
        $this->getDefaultEntity(),
        'getsingle',
        ['Identity' => $this->getEntityId() ?? CRM_Utils_Request::retrieve('id', 'String')]
      );

      if ($identity) {
        $defaults = $identity;
      }
    }

    return $defaults;

  }

}
