<?php

namespace Civi\Aws\Sns\Event;

/**
 * SNS Subscribe Unsubscribe event class.
 *
 * Subscribes or unsubscribes to a topic.
 *
 * @since 1.0
 */
class SubscribeUnsubscribe extends AbstractEvent {

  protected function run(): void {

    $curlHandle = curl_init();
    curl_setopt($curlHandle, CURLOPT_URL, $this->event->SubscribeURL);
    curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 2);
    curl_exec($curlHandle);
    curl_close($curlHandle);

  }

}
