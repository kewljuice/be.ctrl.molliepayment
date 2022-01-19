<?php

use Mollie\Api\MollieApiClient;

class CRM_Core_Payment_MolliepaymentIPN extends CRM_Core_Payment_BaseIPN {

  /**
   * We only need one instance of this object. So we use the singleton
   * pattern and cache the instance in this variable
   *
   * @var object
   * @static
   */
  static private $_singleton = NULL;

  /**
   * mode of operation: live or test
   *
   * @var object
   * @static
   */
  static protected $_mode = NULL;

  /**
   * Constructor
   *
   * @param string $mode the mode of operation: live or test
   *
   * @return void
   */
  function __construct($mode, &$paymentProcessor) {
    parent::__construct();

    $this->_mode = $mode;
    $this->_paymentProcessor = $paymentProcessor;
  }

  /**
   * singleton function used to manage this object
   *
   * @param string $mode the mode of operation: live or test
   *
   * @return object
   * @static
   */
  static function &singleton($mode, $component, &$paymentProcessor) {
    if (self::$_singleton === NULL) {
      self::$_singleton = new CRM_Core_Payment_MolliepaymentIPN($mode,
        $paymentProcessor);
    }
    return self::$_singleton;
  }

  /**
   * This method handles the response that will be invoked
   *
   * @param $processor
   *
   * @throws \Mollie\Api\Exceptions\ApiException
   */
  static function main($processor) {
    // Fetch POST variables.
    $variables = $_REQUEST;
    /* for testing via url /civicrm/payment/ipn/[id]  */
    // $variables["id"] = "tr_wr7gGVCTkp";
    if (isset($variables['id']) && !is_null($variables['id'])) {
      // Fetch contribution
      try {
        $result = civicrm_api3("Contribution", 'getSingle', [
          'sequential' => 1,
          'trxn_id' => $variables['id'],
          'contribution_test' => 1,
        ]);
      } catch (\CiviCRM_API3_Exception $e) {
        \Civi::log()->error("MolliepaymentIPN.php: " . $e->getMessage());
      }
      if (isset($result['id']) && !is_null($result['id'])) {
        $mollie = new MollieApiClient();
        $mollie->setApiKey($processor['user_name']);
        $payment = $mollie->payments->get($variables['id']);
        if ($payment->isPaid()) {
          try {
            // Complete transaction.
            civicrm_api3("Contribution", 'completetransaction', [
              'sequential' => 1,
              'trxn_id' => $variables['id'],
              'id' => $result['id'],
              'is_email_receipt' => 0,
            ]);
          } catch (\CiviCRM_API3_Exception $e) {
            \Civi::log()->error("MolliepaymentIPN.php: " . $e->getMessage());
          }
        }
        else {
          switch ($payment->status) {
            case 'failed':
              try {
                civicrm_api3("Contribution", 'create', [
                  'trxn_id' => $variables['id'],
                  'id' => $result['id'],
                  'contribution_status_id' => 'Failed',
                  'note' => 'failed',
                ]);
              } catch (\CiviCRM_API3_Exception $e) {
                \Civi::log()
                  ->error("MolliepaymentIPN.php: " . $e->getMessage());
              }
              break;
            case 'canceled':
              try {
                civicrm_api3("Contribution", 'create', [
                  'trxn_id' => $variables['id'],
                  'id' => $result['id'],
                  'contribution_status_id' => 'Cancelled',
                  'note' => 'cancelled',
                ]);
              } catch (\CiviCRM_API3_Exception $e) {
                \Civi::log()
                  ->error("MolliepaymentIPN.php: " . $e->getMessage());
              }
              break;
            case 'expired':
              try {
                civicrm_api3("Contribution", 'create', [
                  'trxn_id' => $variables['id'],
                  'id' => $result['id'],
                  'contribution_status_id' => 'Expired',
                  'note' => 'expired',
                ]);
              } catch (\CiviCRM_API3_Exception $e) {
                \Civi::log()
                  ->error("MolliepaymentIPN.php: " . $e->getMessage());
              }
              break;
          }
        }
      }
    }
  }

}
