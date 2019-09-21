<?php

/**
 * AWS Utils class.
 *
 * @since 1.0
 */
class CRM_Aws_Utils {

  /**
   * Retrieves AWS regions.
   *
   * @since 1.0
   * @access public
   * @return array $regions
   */
  public static function getRegions(): array {
    return [
      'us-east-1' => 'email.us-east-1.amazonaws.com',
      'us-west-2' => 'email.us-west-2.amazonaws.com',
      'eu-west-1' => 'email.eu-west-1.amazonaws.com',
    ];
  }

}
