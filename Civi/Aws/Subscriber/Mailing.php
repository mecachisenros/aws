<?php

namespace Civi\Aws\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Civi\Aws\Constant\MailHeader;

/**
 * Mailing subscriber class.
 *
 * Subscribes to mailing events.
 *
 * @since 1.0
 */
class Mailing implements EventSubscriberInterface {

  public static function getSubscribedEvents(): array {
    return [
      'hook_civicrm_post' => [
        ['createMailingConfigSetOnMailingCreation', -255],
      ],
      'hook_civicrm_alterMailParams' => [
        ['addTrackingDataHeaders', -255],
      ],
      'hook_civicrm_pageRun' => [
        ['alterTrackingVarsOnMailingReportPage', -255],
      ],
    ];
  }

  /**
   * Creates a Mailing config set
   * when a mailing is created.
   *
   * @uses hook_civicrm_post
   *
   * @since 1.0
   * @param \Civi\Core\Event\GenericHookEvent $event
   * @return void
   */
  public function createMailingConfigSetOnMailingCreation(
    \Civi\Core\Event\GenericHookEvent $event
  ): void {

    if ($event->entity == 'Mailing' && $event->action == 'create') {

      $params = [
        'mailing_id' => $event->id,
      ];

      if ($defaultConfigSet = \Civi::settings()->get('aws_ses_default_config_set')) {
        $params['config_set'] = $defaultConfigSet;
        $params['is_active'] = 1;
      } else {
        $params['is_active'] = 0;
      }

      try {
        civicrm_api3(
          'MailingConfigSet',
          'create',
          $params
        );
      } catch (\CiviCRM_API3_Exception $e) {

      }
    }

  }

  /**
   * Sets 'url_tracking' and 'open_tracking'
   * template vars to true if the mailing
   * has an SES configuration set.
   *
   * @uses hook_civicrm_pageRun
   *
   * @since 1.0
   * @param \Civi\Core\Event\GenericHookEvent $event
   * @return void
   */
  public function alterTrackingVarsOnMailingReportPage(
    \Civi\Core\Event\GenericHookEvent $event
  ): void {

    if ($event->page->getVar('_name') != 'CRM_Mailing_Page_Report') {
      return;
    }

    if (!($configSet = $this->getConfigSet($event->page->getVar('_mailing_id')))
      || !$configSet['is_active']
    ) {
      return;
    }

    $report = $event->page->get_template_vars('report');
    $report['mailing']['url_tracking'] = 1;
    $report['mailing']['open_tracking'] = 1;
    $event->page->assign('report', $report);

  }

  /**
   * Retrieves and adds the necessary tracking data
   * as email headers for logging SES opens/clicks/bounces.
   *
   * @uses hook_civicrm_alterMailParams
   *
   * @since 1.0
   * @param \Civi\Core\Event\GenericHookEvent $event
   * @return void
   */
  public function addTrackingDataHeaders(
    \Civi\Core\Event\GenericHookEvent $event
  ): void {

    if (!in_array($event->context, ['civimail', 'flexmailer'])) {
      return;
    }

    if (empty($event->params['job_id'])) {
      return;
    }

    $job = $this->getJob($event->params['job_id']);

    if (
      !($configSet = $this->getConfigSet($job['mailing_id']))
      || empty($configSet['is_active'])
    ) {
      return;
    }

    if (!$email = $this->getEmail($event->params['toEmail'])) {
      return;
    }

    $params = [
      'job_id' => $job['id'],
      'email_id' => $email['id'],
      'contact_id' => $email['contact_id'],
      'mailing_id' => $job['mailing_id'],
    ];

    if (!$recipient = $this->getRecipient($params)) {
      return;
    }

    if (!$eventQueue = $this->getEventQueue($params)) {
      return;
    }

    $event->params['headers'][MailHeader::JOB_ID] = $job['job_id'];
    $event->params['headers'][MailHeader::MAILING_ID] = $job['mailing_id'];
    $event->params['headers'][MailHeader::EVENT_QUEUE] = $eventQueue['id'];
    $event->params['headers'][MailHeader::CONFIG_SET] = $configSet['config_set'];

  }

  /**
   * Retrieves the job data for a given job id.
   *
   * @since 1.0
   * @param int $jobId
   * @return array|null The job data or null
   */
  protected function getJob(int $jobId): ?array {
    try {
      $job = civicrm_api3(
        'MailingJob',
        'getsingle',
        ['id' => $jobId]
      );
    } catch (\CiviCRM_API3_Exception $e) {
      $job = null;
    }
    return $job;
  }

  /**
   * Retrieves the SES config set
   * for a given mailing id.
   *
   * @since 1.0
   * @param int $mailingId
   * @return array|null The config set data or null
   */
  protected function getConfigSet(int $mailingId): ?array {
    try {
      $configSet = civicrm_api3(
        'MailingConfigSet',
        'getsingle',
        ['mailing_id' => $mailingId]
      );
    } catch (\CiviCRM_API3_Exception $e) {
      $configSet = null;
    }
    return $configSet;
  }

  /**
   * Retreives the email data
   * for a given email address.
   *
   * @since 1.0
   * @param string $emailAddress
   * @return array|null $email The email data or null
   */
  protected function getEmail(string $emailAddress): ?array {
    try {
      $email = civicrm_api3(
        'Email',
        'getsingle',
        [
          'email' => $emailAddress,
          'options' => [
            'limit' => 1,
          ],
        ]
      );
    } catch (\CiviCRM_API3_Exception $e) {
      $email = null;
    }
    return $email;
  }

  /**
   * Retreives the recipient data.
   *
   * @since 1.0
   * @param array $params
   * @return array|null $recipient The recipient data or null
   */
  protected function getRecipient(array $params): ?array {
    try {
      $recipient = civicrm_api3(
        'MailingRecipients',
        'getsingle',
        $params
      );
    } catch (\CiviCRM_API3_Exception $e) {
      $recipient = null;
    }
    return $recipient;
  }

  /**
   * Retreives the event queue.
   *
   * @since 1.0
   * @param array $params
   * @return array|null $recipient The recipient data or null
   */
  protected function getEventQueue(array $params): ?array {
    try {
      $eventQueue = civicrm_api3(
        'MailingEventQueue',
        'getsingle',
        $params
      );
    } catch (\CiviCRM_API3_Exception $e) {
      $eventQueue = null;
    }
    return $eventQueue;
  }

}
