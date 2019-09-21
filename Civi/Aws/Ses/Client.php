<?php

namespace Civi\Aws\Ses;

use Civi\Aws\AbstractClient;

/**
 * AWS SES Client class.
 *
 * @since 1.0
 */
class Client extends AbstractClient {

  /**
   * The AWS client class name to instantiate.
   *
   * @since 1.0
   * @return string $clientName
   */
  protected function clientClassName(): string {
    return 'Aws\Ses\SesClient';
  }

}
