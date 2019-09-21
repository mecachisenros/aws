<?php

/**
 * Simple Email Service webhook page class,
 * listens and processes bounce events from Amazon SNS.
 *
 * @since 1.0
 */
class CRM_Aws_Sns_Page_Webhook extends CRM_Core_Page {

  /**
   * The SNS notification event.
   *
   * @var \stdClass
   * @since 1.0
   */
  protected $event;

  /**
   * The SNS message.
   *
   * @var \stdClass
   * @since 1.0
   */
  protected $message;

  /**
   * Constructor.
   *
   * @since 1.0
   */
  public function __construct() {

    // get json input
    $this->event = json_decode(file_get_contents('php://input'));
    // message object
    $this->message = json_decode($this->event->Message);

    parent::__construct();

  }

  /**
   * Run.
   *
   * @since 1.0
   */
  public function run(): void {
    if (
      $this->event->Type == 'SubscriptionConfirmation'
      || $this->event->Type == 'UnsubscribeConfirmation'
    ) {

      new Civi\Aws\Sns\Event\SubscribeUnsubscribe($this->event);

    } else {

      $notificationType = isset($this->message->eventType)
        ? $this->message->eventType
        : $this->message->notificationType;

      switch ($notificationType) {
        case 'Open':
          new Civi\Aws\Sns\Event\Open($this->event);
          break;

        case 'Click':
          new Civi\Aws\Sns\Event\Click($this->event);
          break;

        case 'Bounce':
          new Civi\Aws\Sns\Event\Bounce($this->event);
          break;
      }

    }

    CRM_Utils_System::civiExit();

  }

}
