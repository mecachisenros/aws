<?php

/**
 * SES Identity Details controller calss.
 *
 * @since 1.0
 */
class CRM_Aws_Ses_Page_IdentityDetails extends CRM_Core_Page {

  /**
   * Run.
   *
   * Retrieves and displays the notification
   * and dkim attributes for a given identity.
   *
   * @since 1.0
   */
  public function run(): void {

    $identityName = CRM_Utils_Request::retrieve('identity', 'String');
    $identity = civicrm_api3('Ses', 'getsingle', ['Identity' => $identityName]);

    unset($identity['id'], $identity['Identity'], $identity['Type']);

    if (isset($identity['Topics'])) {
      foreach ($identity['Topics'] as $topicKey => $topicValue) {
        if (!isset($identity['Subscriptions'])) {
          $identity['Subscriptions'] = [];
        }
        if (isset($topicValue['Subscriptions'])) {
          foreach ($topicValue['Subscriptions'] as $subscriptionKey => $subscriptionValue) {
            $identity['Subscriptions'][] = $subscriptionValue['Endpoint'];
          }
        }
        $identity['Subscriptions'] = implode(', ', $identity['Subscriptions']);
      }
      unset($identity['Topics']);
    }
    $this->assign(
      'identity',
      $identity
    );

    parent::run();

  }

}
