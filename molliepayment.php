<?php

require_once 'molliepayment.civix.php';
use CRM_Ctrl_Molliepayment_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function molliepayment_civicrm_config(&$config) {
  _molliepayment_civix_civicrm_config($config);
  $extRoot = dirname(__FILE__) . DIRECTORY_SEPARATOR;
  $include_path = $extRoot . DIRECTORY_SEPARATOR . 'vendor' . PATH_SEPARATOR . get_include_path( );
  set_include_path( $include_path );
  require_once 'vendor/autoload.php';
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function molliepayment_civicrm_xmlMenu(&$files) {
  _molliepayment_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function molliepayment_civicrm_install() {
  _molliepayment_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function molliepayment_civicrm_postInstall() {
  _molliepayment_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function molliepayment_civicrm_uninstall() {
  _molliepayment_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function molliepayment_civicrm_enable() {
  _molliepayment_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function molliepayment_civicrm_disable() {
  _molliepayment_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function molliepayment_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _molliepayment_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function molliepayment_civicrm_managed(&$entities) {
  $entities[] = [
    'module' => 'be.ctrl.molliepayment',
    'name' => 'CiviCRM Mollie Payment',
    'entity' => 'PaymentProcessorType',
    'params' => [
      'version' => 3,
      'name' => 'ctrl_mollie_payment',
      'title' => 'CiviCRM Mollie Payment',
      'description' => 'CiviCRM Mollie payment option.',
      'class_name' => 'Payment_Molliepayment',
      'user_name_label' => 'API key',
      'url_site_default' => 'https://mollie.com',
      'url_api_default' => 'https://mollie.com',
      'url_site_test_default' => 'https://mollie.com',
      'url_api_test_default' => 'https://mollie.com',
      'billing_mode' => 4,
      'is_recur' => 0,
      'payment_type' => 1,
    ],
  ];
  _molliepayment_civix_civicrm_managed($entities);
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
function molliepayment_civicrm_caseTypes(&$caseTypes) {
  _molliepayment_civix_civicrm_caseTypes($caseTypes);
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
function molliepayment_civicrm_angularModules(&$angularModules) {
  _molliepayment_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function molliepayment_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _molliepayment_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function molliepayment_civicrm_entityTypes(&$entityTypes) {
  _molliepayment_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_themes().
 */
function molliepayment_civicrm_themes(&$themes) {
  _molliepayment_civix_civicrm_themes($themes);
}
