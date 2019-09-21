<?php

/**
 * AWS SES AJAX controller class.
 *
 * @since 1.0
 */
class CRM_Aws_Ses_Page_AJAX {

  /**
   * Retrieves the verifed identities
   * formatted for dataTable.
   *
   * @since 1.0
   */
  public static function getDataTableForamattedIdentities() {

    $verifiedIdentities = civicrm_api3('Ses', 'get', []);

    if (!$verifiedIdentities['count']) {

      $result['data'] = '';
      $result['recordsTotal'] = $result['recordsFiltered'] = 0;

    } else {

      $result['data'] = CRM_Aws_Ses_Utils::formatResultForDataTable(
        $verifiedIdentities['values']
      );
      $result['recordsTotal'] = $result['recordsFiltered'] = $verifiedIdentities['count'];

    }

    CRM_Utils_JSON::output($result);

  }

}
