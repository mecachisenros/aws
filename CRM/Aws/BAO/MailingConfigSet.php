<?php
use CRM_Aws_ExtensionUtil as E;

class CRM_Aws_BAO_MailingConfigSet extends CRM_Aws_DAO_MailingConfigSet {

  /**
   * Create a new MailingConfigSet based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_Aws_DAO_MailingConfigSet|NULL
   *
  public static function create($params) {
    $className = 'CRM_Aws_DAO_MailingConfigSet';
    $entityName = 'MailingConfigSet';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  } */

}
