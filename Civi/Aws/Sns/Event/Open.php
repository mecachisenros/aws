<?php

namespace Civi\Aws\Sns\Event;

/**
 * SNS Open event class.
 *
 * @since 1.0
 */
class Open extends AbstractEvent {

  protected function run(): void {

    if (!$eventQueueId = $this->getEventQueueFromHeader()) {
      list($jobId, $eventQueueId, $hash) = $this->getVerpItems();
    }

    \CRM_Mailing_Event_BAO_Opened::open($eventQueueId);

  }

}
