<?php

namespace Civi\Aws\Sns;

use Civi\Aws\AbstractClient;

/**
 * AWS SNS Client class.
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
    return 'Aws\Sns\SnsClient';
  }

}
