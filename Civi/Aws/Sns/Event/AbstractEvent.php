<?php

namespace Civi\Aws\Sns\Event;

use Civi\Aws\Constant\MailHeader;

/**
 * SNS abstract event class.
 *
 * @since 1.0
 */
abstract class AbstractEvent {

  /**
   * The SNS event.
   *
   * @var \stdClass
   * @since 1.0
   */
  protected $event;

  /**
   * The SNS event message.
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
  public function __construct(\stdClass $event) {

    $this->event = $event;
    $this->message = json_decode($event->Message);

    if (!$this->verifySnsSignature()) {
      \CRM_Utils_System::civiExit();
    }

    $this->run();

    \CRM_Utils_System::civiExit();

  }

  /**
   * Implements the functionality
   * for this event.
   *
   * @since 1.0
   */
  abstract protected function run(): void;

  /**
   * Get header value for a
   * given header name.
   *
   * @since 1.0
   * @param  string $headerName The header name to retrieve
   * @return string|null $headerValue The header value
   */
  protected function getHeaderValue(string $headerName): string {
    foreach ($this->message->mail->headers as $header) {
      if ($header->name == $headerName) {
        return $header->value;
      }
    }
    return null;
  }

  /**
   * Retrieves the event queue
   * from the mail header.
   *
   * @since 1.0
   * @return int|null $eventQueueId The event queue id or null
   */
  protected function getEventQueueFromHeader(): ?int {
    return $this->getHeaderValue(MailHeader::EVENT_QUEUE) ?? null;
  }

  /**
   * Retrieves verp items from
   * a given header, by default retrieves it
   * from the X-CiviMail-Bounce header.
   *
   * @since 1.0
   * @param string $headerValue Optional, the header value, defaults to X-CiviMail-Bounce
   * @return array $verpItems The verp items [$jobId, $eventQueueId, $hash]
   */
  protected function getVerpItems(string $headerValue = ''): array {

    $headerValue = !empty($headerValue)
      ? $headerValue
      : $this->getHeaderValue(MailHeader::BOUNCE);

    $verpSeparator = \Civi::settings()->get('verpSeparator');
    $localpart = \CRM_Core_BAO_MailSettings::defaultLocalpart();

    $verpItems = substr(
      substr($headerValue, 0, strpos($headerValue, '@')),
      strlen($localpart) + 2
    );

    return explode($verpSeparator, $verpItems);

  }

  /**
   * Verify SNS Message signature.
   *
   * @since 1.0
   * @see https://docs.aws.amazon.com/sns/latest/dg/SendMessageToHttp.verify.signature.html
   * @return bool $isSigned true if succesful
   */
  protected function verifySnsSignature(): bool {

    // keys needed for signature
    $keysToSign = [
      'Message',
      'MessageId',
      'Subject',
      'Timestamp',
      'TopicArn',
      'Type',
    ];

    // for SubscriptionConfirmation the keys are slightly different
    if ($this->event->Type == 'SubscriptionConfirmation') {
      $keysToSign = [
        'Message',
        'MessageId',
        'SubscribeURL',
        'Timestamp',
        'Token',
        'TopicArn',
        'Type',
      ];
    }

    $message = '';
    // build message to sign
    foreach ($keysToSign as $key) {
      if (isset($this->event->$key)) {
        $message .= "{$key}\n{$this->event->$key}\n";
      }
    }

    // decode SNS signature
    $snsSignature = base64_decode($this->event->Signature);

    // get certificate from SigningCerURL and extract public key
    $publicKey = openssl_get_publickey(
      file_get_contents($this->event->SigningCertURL)
    );

    // verify signature
    $isSigned = openssl_verify(
      $message,
      $snsSignature,
      $publicKey,
      OPENSSL_ALGO_SHA1
    );

    return $isSigned && $isSigned != -1;

  }

  /**
   * Checks wheather the event type
   * is a Notification.
   *
   * @since 1.0
   * @return bool
   */
  protected function isNotification(): bool {
    return $this->event->Type == 'Notification';
  }

  /**
   * Checks wheather the event type
   * is a SubscriptionConfirmation
   * or a UnsubscribeConfirmation.
   *
   * @since 1.0
   * @return bool
   */
  protected function isSubscribeUnsubscribe(): bool {
    return $this->event->Type == 'SubscriptionConfirmation'
      || $this->event->Type == 'UnsubscribeConfirmation';
  }

}
