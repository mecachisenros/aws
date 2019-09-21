<?php

namespace Civi\Aws\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use \CRM_Aws_ExtensionUtil as E;

/**
 * NavigationMenu subscriber class.
 *
 * Subscribes to navigation menu events.
 *
 * @since 1.0
 */
class NavigationMenu implements EventSubscriberInterface {

  public static function getSubscribedEvents(): array {
    return [
      'hook_civicrm_navigationMenu' => [
        ['addMenu', -255],
      ],
    ];
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
  public function addMenu(\Civi\Core\Event\GenericHookEvent $event): void {
    _aws_civix_insert_navigation_menu($event->params, 'Administer', [
      'label' => E::ts('AWS'),
      'name' => 'AWS',
      'url' => null,
      'permission' => 'administer CiviCRM',
      'operator' => 'AND',
      'separator' => 0
    ]);
    _aws_civix_insert_navigation_menu($event->params, 'Administer/AWS', [
      'label' => E::ts('User Credentials'),
      'name' => 'User Credentials',
      'url' => 'civicrm/admin/setting/preferences/aws-credentials',
      'permission' => 'administer CiviCRM',
      'operator' => 'AND',
      'separator' => 0
    ]);
    _aws_civix_insert_navigation_menu($event->params, 'Administer/AWS', [
      'label' => E::ts('SES Settings'),
      'name' => 'SES Settings',
      'url' => 'civicrm/admin/setting/preferences/aws-ses',
      'permission' => 'administer CiviCRM',
      'operator' => 'AND',
      'separator' => 0
    ]);
    _aws_civix_insert_navigation_menu($event->params, 'Administer/AWS', [
      'label' => E::ts('SES Identities and Configuration Sets'),
      'name' => 'SES Identities and Configuration Sets',
      'url' => 'civicrm/aws-ses/identities',
      'permission' => 'administer CiviCRM',
      'operator' => 'AND',
      'separator' => 0
    ]);
    _aws_civix_navigationMenu($event->params);
  }

}
