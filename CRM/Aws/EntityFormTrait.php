<?php

/**
 * Aws entity form trait.
 *
 * Overrides EntityForm's getEntityDefaults()
 * and setEntityFieldsMetadata() methods.
 *
 * @since 1.0
 */
trait CRM_Aws_EntityFormTrait {

  /**
   * Check whether we are in create mode.
   *
   * @since 1.0
   * @return bool
   */
  protected function isCreateContext() {
    return ($this->getAction() & CRM_Core_Action::ADD);
  }

  /**
   * Check whether we are in edit mode.
   *
   * @since 1.0
   * @return bool
   */
  protected function isEditContext() {
    return ($this->getAction() & CRM_Core_Action::UPDATE);
  }

  /**
   * Copy of CRM_Core_Form_EntityFormTrait::getEntityDefaults()
   * which retrieves the defaults using the API instead of BAO.
   *
   * @since 1.0
   * @return array $defaults
   */
  protected function getEntityDefaults() {
    $defaults = $moneyFields = [];

    if (
      !$this->isDeleteContext() &&
      $this->getEntityId()
    ) {
      $params = ['id' => $this->getEntityId()];
      $defaults = civicrm_api3($this->getDefaultEntity(), 'getsingle', $params);
      // $baoName = $this->_BAOName;
      // $baoName::retrieve($params, $defaults);
    }

    foreach ($this->entityFields as $entityFieldName => $fieldSpec) {
      $value = CRM_Utils_Request::retrieveValue($fieldSpec['name'], $this->getValidationTypeForField($fieldSpec['name']));
      if ($value !== FALSE && $value !== NULL) {
        $defaults[$fieldSpec['name']] = json_encode($value);
      }
      // Store a list of fields with money formatters
      if (CRM_Utils_Array::value('formatter', $fieldSpec) == 'crmMoney') {
        $moneyFields[] = $entityFieldName;
      }
    }
    if (!empty($defaults['currency'])) {
      // If we have a money formatter we need to pass the specified currency or it will render as the default
      foreach ($moneyFields as $entityFieldName) {
        $this->entityFields[$entityFieldName]['formatterParam'] = $defaults['currency'];
      }
    }

    // Assign again as we may have modified above
    $this->assign('entityFields', $this->entityFields);
    return $defaults;
  }

  /**
   * Copy of CRM_Core_Form_EntityFormTrait::setEntityFieldsMetadata()
   * which does not retrieve the fieldSpec from the databases.
   *
   * @since 1.0
   */
  protected function setEntityFieldsMetadata() {
    foreach ($this->entityFields as $field => &$props) {
      if (!empty($props['not-auto-addable'])) {
        // We can't load this field using metadata
        continue;
      }
      if ($field != 'id' && $this->isDeleteContext()) {
        // Delete forms don't generally present any fields to edit
        continue;
      }
      // Resolve action.
      if (empty($props['action'])) {
        $props['action'] = $this->getApiAction();
      }
      // $fieldSpec = civicrm_api3($this->getDefaultEntity(), 'getfield', $props);
      $fieldSpec = $props;
      if (!isset($props['description']) && isset($fieldSpec['description'])) {
        $props['description'] = $fieldSpec['description'];
      }
    }
  }

  /**
   * Call api wrapper for default
   * entity and default action.
   *
   * @since 1.0
   * @param array $params
   */
  public function callApi($params) {

    $params = is_numeric($params) ? ['id' => $params] : $params;

    return civicrm_api3(
      $this->getDefaultEntity(),
      $this->getDefaultContext(),
      $params
    );

  }

  /**
   * A wrapper around CRM_Core_Session::setStatus().
   *
   * @since 1.0
   * @param string $message The message
   * @param string $type The type
   */
  public function setStatus(string $message, $type = 'success') {

    $title = $type == 'alert' ? 'Oops!' : 'Success!';

    CRM_Core_Session::setStatus(
      $message,
      $title,
      $type
    );

  }

}
