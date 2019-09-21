<?php

namespace Civi\Aws;

class Registry {

  /**
   * Registry.
   *
   * @var array
   * @since 1.0
   */
  private $registry;

  /**
   * Constructor.
   *
   * @since 1.0
   */
  public function __construct() {
    $this->registry = [];
    $this->setupObjects();
  }

  /**
   * Registers an object in the registry with the specified id.
   *
   * NOTE: objects will be replaced if specifing the same id,
   * this is intentional to allow other extensions to replace classes.
   *
   * @since 1.0
   * @param string $id The unique id for the object
   * @param object $object Instance of the object to store in the registry
   */
  public function add(string $id, $object) {
    $this->registry[$id] = $object;
  }

  /**
   * Retrieves a reference to a registered object.
   *
   * @since 1.0
   * @param string $id The id of the object to retrieve
   * @return object|bool The reference to the object or false
   */
  public function get(string $id) {
    return $this->registry[$id] ?? false;
  }

  /**
   * Sets up the necessary objects.
   *
   * @since 1.0
   */
  private function setupObjects(): void {

    $this->add(
      'sesClient',
      (new Ses\Client())->get()
    );
    $this->add(
      'snsClient',
      (new Sns\Client())->get()
    );

  }

  /**
   * Retreieves the registered object ids.
   *
   * @since 1.0
   * @return array $registredObjectsIds
   */
  public function getRegisterdObjectsIds(): array {
    return array_keys($this->registry) ?? [];
  }

}
