<?php

/**
 * Simple Email Service Utils class.
 *
 * @since 1.0
 */
class CRM_Aws_Ses_Utils {

  /**
   * Retrieves formatted identities
   * attributes for dataTable.
   *
   * @since 1.0
   * @param array $identities
   * @return array $formattedIdentities
   */
  public static function formatResultForDataTable(array $identities): array {

    if (empty($identities)) {
      return [
        'identity' => '',
        'type' => '',
        'status' => '',
        'actions' => '',
      ];
    }

    return array_reduce(
      $identities,
      function($list, $identity) {

        $identityName = $identity['Identity'];

        $list[] = [
          'identity' => sprintf(
            '<a title="%1$s" class="crm-expand-row" href="%2$s">%3$s</a>',
            $identityName,
            CRM_Utils_System::url('civicrm/aws-ses/identity/details', ['identity' => $identityName]),
            $identityName
          ),
          'type' => $identity['Type'],
          'status' => $identity['VerificationStatus'],
          'actions' => CRM_Aws_Ses_BAO_Ses::getActionLinks($identityName),
        ];

        return $list;

      },
      []
    );

  }

}
