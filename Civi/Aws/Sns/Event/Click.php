<?php

namespace Civi\Aws\Sns\Event;

use Civi\Aws\Constant\MailHeader;

/**
 * SNS Click event class.
 *
 * @since 1.0
 */
class Click extends AbstractEvent {

  protected function run(): void {

    if (!$eventQueueId = $this->getEventQueueFromHeader()) {
      list($jobId, $eventQueueId, $hash) = $this->getVerpItems();
    }

    $this->trackClick($eventQueueId, $this->message->click->link);

  }

  /**
   * Tracks a url click.
   *
   * @since 1.0
   * @param $eventQueueId
   * @param string $url
   * @return void
   */
  protected function trackClick(int $eventQueueId, string $url): void {

    \CRM_Mailing_Event_BAO_TrackableURLOpen::track(
      $eventQueueId,
      $this->getTrackedUrlId($url)
    );

  }

  /**
   * Retrieves the tracked url id,
   * creating one if it does not exists.
   *
   * @since 1.0
   * @param string $url
   * @return int $urlId
   */
  protected function getTrackedUrlId(string $url): ?int {

    if (!$mailingId = $this->getHeaderValue(MailHeader::MAILING_ID)) {
      return null;
    };

    $tracker = new \CRM_Mailing_BAO_TrackableURL();

    $tracker->url = $this->parseUrlandRemoveUndesiredQueryArgs($url);
    $tracker->mailing_id = $mailingId;

    if (!$tracker->find(true)) {
      $tracker->save();
    }

    return $tracker->id;

  }

  /**
   * Parses the url and removes undesired
   * query arguments like 'cid' and 'cs'.
   *
   * @since 1.0
   * @param string $url The url
   * @return string $url The modified url
   */
  protected function parseUrlandRemoveUndesiredQueryArgs(string $url): string {

    $parts = parse_url($url);

    if (!empty($parts['query'])) {
      parse_str($parts['query'], $query);
      unset($query['cid'], $query['cs']);
      $parts['query'] = urldecode(http_build_query($query));
    }

    return $this->buildUrl($parts);

  }

  /**
   * Builds a url from it's parts (parse_url).
   *
   * @since 1.0
   * @param array $parts The url parts
   * @return string $url The url
   */
  protected function buildUrl(array $parts): string {

    $scheme = isset($parts['scheme']) ? ($parts['scheme'] . '://') : '';
    $host = $parts['host'] ?? '';
    $port = isset($parts['port']) ? (':' . $parts['port']) : '';
    $user = $parts['user'] ?? '';
    $pass = isset($parts['pass']) ? (':' . $parts['pass'])  : '';
    $pass = ($user || $pass) ? ($pass . '@') : '';
    $path = $parts['path'] ?? '';
    $query = isset($parts['query']) ? ('?' . $parts['query']) : '';
    $fragment = isset($parts['fragment']) ? ('#' . $parts['fragment']) : '';

    return implode('', [$scheme, $user, $pass, $host, $port, $path, $query, $fragment]);

  }
}
