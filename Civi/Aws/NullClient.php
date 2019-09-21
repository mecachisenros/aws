<?php

namespace Civi\Aws;

/**
 * AWS Null Client class.
 *
 * When instantiating an AWS client, this is the object
 * returned when credentials are not present or the user
 * doesn't have enough permissions/assigned policies to use
 * that specifc client, the only purpouse of this class is
 * to not throw errors all over the place, and kill Civi.
 *
 * @since 1.0
 */
class NullClient {

  /**
   * Silently fail calls to instance methods.
   *
   * @param string $name
   * @param mixed $arguments
   */
  public function __call($name, $arguments) {
    // silence
  }

  /**
   * Silently fail calls to static methods.
   *
   * @param string $name
   * @param mixed $arguments
   */
  public static function __callStatic($name, $arguments) {
    // silence
  }

}
