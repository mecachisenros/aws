<?php

require __DIR__ . '/vendor/autoload.php';
require_once 'aws.civix.php';
use Symfony\Component\DependencyInjection\Definition;

/**
 * Retrieves the AWS Registry service.
 *
 * @since 1.0
 * @return \Civi\Aws\Registry
 */
function awsRegistry(): \Civi\Aws\Registry {
  return Civi::service('awsRegistry');
}

/**
 * Implements hook_civicrm_container().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_container/
 */
function aws_civicrm_container($container) {
  $container->setDefinition('awsRegistry', new Definition('Civi\Aws\Registry'));
}

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function aws_civicrm_config(&$config) {
  _aws_civix_civicrm_config($config);

  $dispatcher = Civi::dispatcher();
  $dispatcher->addSubscriber(new \Civi\Aws\Subscriber\NavigationMenu());
  $dispatcher->addSubscriber(new \Civi\Aws\Subscriber\Mailing());
  $dispatcher->addSubscriber(new \Civi\Aws\Subscriber\Angular());
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function aws_civicrm_xmlMenu(&$files) {
  _aws_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function aws_civicrm_install() {
  _aws_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function aws_civicrm_postInstall() {
  _aws_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function aws_civicrm_uninstall() {
  _aws_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function aws_civicrm_enable() {
  _aws_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function aws_civicrm_disable() {
  _aws_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function aws_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _aws_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function aws_civicrm_managed(&$entities) {
  _aws_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function aws_civicrm_caseTypes(&$caseTypes) {
  _aws_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function aws_civicrm_angularModules(&$angularModules) {
  _aws_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function aws_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _aws_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function aws_civicrm_entityTypes(&$entityTypes) {
  _aws_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function aws_civicrm_themes(&$themes) {
  _aws_civix_civicrm_themes($themes);
}

/**
 * Implements hook_civicrm_alterSettingsMetadata
 */
function aws_civicrm_alterSettingsMetaData(&$settingsMetaData, $domainID, $profile) {
  // Set settings readonly if they are set via environment variables
  $vars = [
    'aws_access_key' => 'CIVICRM_AWS_ACCESS_KEY',
    'aws_secret_key' => 'CIVICRM_AWS_SECRET_KEY',
    'aws_region' => 'CIVICRM_AWS_REGION',
  ];
  foreach ($vars as $setting => $envVar) {
    if (defined($envVar)) {
      $settingsMetaData[$setting]['html_attributes']['readonly'] = TRUE;
    }
  }
}
