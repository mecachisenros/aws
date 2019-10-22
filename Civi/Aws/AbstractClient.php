<?php

namespace Civi\Aws;

use CRM_Aws_ExtensionUtil as E;

/**
 * AWS abstract client class.
 *
 * @since 1.0
 */
abstract class AbstractClient {

  /**
   * AWS client instance.
   *
   * @var object
   * @since 1.0
   */
  protected $client;

  /**
   * The AWS client class name to instantiate.
   *
   * @since 1.0
   * @return string $clientName The client name
   */
  abstract protected function clientClassName(): string;

  /**
   * Retrieves the client instance.
   *
   * @since 1.0
   * @return object $client The AWS Client
   */
  public function get() {

    if (!isset($this->client)) {
      $clientClassName = $this->clientClassName();
      $this->client = ($credentials = $this->getCredentials())
        ? new $clientClassName($credentials)
        : new NullClient();
    }

    return $this->client;

  }

  /**
   * Retrieves the AWS user credentials
   * from constants or DB.
   *
   * @since 1.0
   * @return array|null $credentials The AWS credentials array or null
   */
  protected function getCredentials(): ?array {

    $awsKey = defined('CIVICRM_AWS_ACCESS_KEY')
      ? CIVICRM_AWS_ACCESS_KEY
      : \Civi::settings()->get('aws_access_key');

    $awsSecret = defined('CIVICRM_AWS_SECRET_KEY')
      ? CIVICRM_AWS_SECRET_KEY
      : \Civi::settings()->get('aws_secret_key');

    if (empty($awsKey) || empty($awsSecret)) {

      \CRM_Core_Session::setStatus(
        E::ts(
          'Failed to instantiate the <code>%1</code> client. You need to
          <a href="%2">setup your AWS access keys</a>, and also double check your
          <a href="%3" target="_blank">AWS user permissions</a>.',
          [
            1 => get_called_class(),
            2 => \CRM_Utils_System::url('civicrm/admin/setting/preferences/aws-credentials'),
            3 => 'https://console.aws.amazon.com/iam/home',
          ]
        )
      );

      return null;

    }

    $credentials = new \Aws\Credentials\Credentials(
      $awsKey,
      $awsSecret
    );

    return [
      'version' => 'latest',
      'region' => defined('CIVICRM_AWS_REGION')
        ? CIVICRM_AWS_REGION
        : \Civi::settings()->get('aws_region'),
      'credentials' => $credentials,
    ];

  }

}
