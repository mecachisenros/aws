<?php

namespace Civi\Aws\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use \CRM_Aws_ExtensionUtil as E;

/**
 * Angular subscriber class.
 *
 * Subscribes to Angular events.
 *
 * @since 1.0
 */
class Angular implements EventSubscriberInterface {

  public static function getSubscribedEvents(): array {
    return [
      'hook_civicrm_alterAngular' => [
        ['addConfigSetAndSesTrackingDirective', -255],
      ],
      'hook_civicrm_angularModules' => [
        ['registerSesTrackingModule', -255],
      ]
    ];
  }

  public function registerSesTrackingModule(
    \Civi\Core\Event\GenericHookEvent $event
  ): void {
    $event->angularModules['crmSesTracking'] = [
      'ext' => E::SHORT_NAME,
      'js' => ['js/ang/*.js'],
    ];
  }

  public function addConfigSetAndSesTrackingDirective(
    \Civi\Core\Event\GenericHookEvent $event
  ): void {

    $crmSesTracking = \Civi\Angular\ChangeSet::create('crmSesTracking')
      ->alterHtml(
      '~/crmMailing/BlockTracking.html',
        function (\phpQueryObject $doc) {
          $doc->find('[name="url_tracking"]')->attr('ng-disabled', 'mailingConfigSet.is_active');
          $doc->find('[name="open_tracking"]')->attr('ng-disabled', 'mailingConfigSet.is_active');
          $doc->find('.crm-group')->append('<div crm-ses-tracking></div>');
        }
      );

    $event->angular->add($crmSesTracking);

  }

}
