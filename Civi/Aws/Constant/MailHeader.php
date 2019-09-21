<?php

namespace Civi\Aws\Constant;

/**
 * Constant class.
 *
 * @since 1.0
 */
class MailHeader {

  /**
   * Job id header name.
   *
   * @since 1.0
   */
  const JOB_ID = 'X-CiviMail-JobId';

  /**
   * Mailing id header name.
   *
   * @since 1.0
   */
  const MAILING_ID = 'X-CiviMail-MailingId';

  /**
   * EventQueue id header name.
   *
   * @since 1.0
   */
  const EVENT_QUEUE = 'X-CiviMail-EventQueue';

  /**
   * SES Configuration set header name.
   *
   * @since 1.0
   */
  const CONFIG_SET = 'X-SES-CONFIGURATION-SET';

  /**
   * CiviCRM bounce header name.
   *
   * @since 1.0
   */
  const BOUNCE = 'X-CiviMail-Bounce';

}
