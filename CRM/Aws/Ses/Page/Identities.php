<?php

use CRM_Aws_ExtensionUtil as E;

/**
 * SES Identities controller class.
 *
 * @since 1.0
 */
class CRM_Aws_Ses_Page_Identities extends CRM_Core_Page {

  /**
   * Run.
   *
   * @since 1.0
   */
  public function run(): void {

    CRM_Utils_System::setTitle(E::ts('AWS SES - Identities'));
    Civi::resources()->addScriptFile('civicrm', 'js/crm.expandRow.js');

    $verifiedIdentities = civicrm_api3('Ses', 'get', []);

    $this->assign('identitiesCount', $verifiedIdentities['count']);
    $this->assign('tableHeaders', $this->getTableHeaders());

    parent::run();

  }

  /**
   * Retreieve SES Identities table headers.
   *
   * @since 1.0
   * @return array $tableHeaders
   */
  public function getTableHeaders(): array {
    return [
      'identity' => E::ts('Identity'),
      'type' => E::ts('Type'),
      'status' => E::ts('Verification Status'),
      'actions' => E::ts('Actions'),
    ];
  }

}
