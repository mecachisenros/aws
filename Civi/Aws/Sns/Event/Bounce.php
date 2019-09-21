<?php

namespace Civi\Aws\Sns\Event;

use Civi\Aws\Constant\MailHeader;

/**
 * SNS Bounce event class.
 *
 * @since 1.0
 */
class Bounce extends AbstractEvent {

  /**
   * CiviCRM Bounce types.
   *
   * @var array
   * @since 1.o
   * @access protected
   */
  protected $civiBounceTypes = [];

  /**
   * SES Permanent bounce types (hard bounces).
   *
   * @var array
   * @since 1.0
   */
  protected $sesPermanentBounceTypes = [
    'Undetermined',
    'General',
    'NoEmail',
    'Suppressed',
  ];

  /**
   * SES Transient bounce types (soft bounces).
   *
   * @var array
   * @since 1.0
   */
  protected $sesTransientBounceTypes = [
    'General',
    'MailboxFull',
    'MessageTooLarge',
    'ContentRejected',
    'AttachmentRejected',
  ];

  protected function run(): void {

    list($jobId, $eventQueueId, $hash) = $this->getVerpItems(
      $this->getHeaderValue(MailHeader::BOUNCE)
    );

    $bounceParams = $this->setBounceTypeParams([
      'job_id' => $jobId,
      'event_queue_id' => $eventQueueId,
      'hash' => $hash,
    ]);

    if (\CRM_Utils_Array::value('bounce_type_id', $bounceParams)) {
      \CRM_Mailing_Event_BAO_Bounce::create($bounceParams);
    }

  }

  /**
   * Set bounce type params.
   *
   * @since 1.0
   * @param array $bounceParams The params array
   * @return array $bounceParams The params array
   */
  protected function setBounceTypeParams(array $bounceParams): array {

    // hard bounces
    if (
      $this->message->bounce->bounceType == 'Permanent'
      && in_array(
          $this->message->bounce->bounceSubType,
          $this->sesPermanentBounceTypes
        )
    ) {
      switch ($this->message->bounce->bounceSubType) {
        case 'Undetermined':
          $bounceParams = $this->mapBounceTypes($bounceParams, 'Syntax');
          break;

        case 'General':
        case 'NoEmail':
        case 'Suppressed':
          $bounceParams = $this->mapBounceTypes($bounceParams, 'Invalid');
          break;
      }
    }

    // soft bounces
    if (
      $this->message->bounce->bounceType == 'Transient'
      && in_array(
          $this->message->bounce->bounceSubType,
          $this->sesTransientBounceTypes
        )
    ) {
      switch ($this->message->bounce->bounceSubType) {
        case 'General':
          $bounceParams = $this->mapBounceTypes(
            $bounceParams,
            'Syntax'
          );
          break;

        case 'MessageTooLarge':
        case 'MailboxFull':
          $bounceParams = $this->mapBounceTypes(
            $bounceParams,
            'Quota'
          );
          break;

        case 'ContentRejected':
        case 'AttachmentRejected':
          $bounceParams = $this->mapBounceTypes(
            $bounceParams,
            'Spam'
          );
          break;
      }
    }

    return $bounceParams;

  }

  /**
   * Maps SES bounce types to Civi bounce types.
   *
   * @since 1.0
   * @param  array $bounceParams The params array
   * @param  string $typeToMapTo Civi bounce type to map to
   * @return array $bounceParams The params array
   */
  protected function mapBounceTypes(array $bounceParams, string $typeToMapTo): array {

    $bounceParams['bounce_type_id'] = array_search(
      $typeToMapTo,
      $this->getCiviBounceTypes()
    );

    // it should be one recipient
    $recipient = count($this->message->bounce->bouncedRecipients) == 1
      ? reset($this->message->bounce->bouncedRecipients)
      : null;

    if ($recipient) {
      $bounceParams['bounce_reason'] = $recipient->status
        . ' => '
        . $recipient->diagnosticCode;
    }

    return $bounceParams;

  }

  /**
   * Get CiviCRM bounce types.
   *
   * @since 1.0
   * @return $array $civi_bounce_types
   */
  protected function getCiviBounceTypes(): array {

    if (!empty($this->civiBounceTypes)) {
      return $this->civiBounceTypes;
    }

    $query = 'SELECT id,name FROM civicrm_mailing_bounce_type';
    $dao = \CRM_Core_DAO::executeQuery($query);

    while ($dao->fetch()) {
      $this->civiBounceTypes[$dao->id] = $dao->name;
    }

    return $this->civiBounceTypes;

  }

}
