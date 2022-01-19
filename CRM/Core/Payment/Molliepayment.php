<?php

// phpcs:disable
use CRM_Ctrl_Molliepayment_ExtensionUtil as E;
use Mollie\Api\MollieApiClient;

// phpcs:enable

class CRM_Core_Payment_Molliepayment extends CRM_Core_Payment {

  /**
   * We only need one instance of this object. So we use the singleton
   * pattern and cache the instance in this variable
   *
   * @var object
   * @static
   */
  static private $_singleton = NULL;

  /**
   * Mode of operation: live or test
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
    $this->_paymentProcessor = $paymentProcessor;
  }

  /**
   * Singleton function used to manage this object
   *
   * @param string $mode the mode of operation: live or test
   *
   * @return object
   * @static
   *
   */
  static function &singleton($mode, &$paymentProcessor) {
    $processorName = $paymentProcessor['name'];
    if (self::$_singleton[$processorName] === NULL) {
      self::$_singleton[$processorName] = new CRM_Core_Payment_Molliepayment($mode, $paymentProcessor);
    }
    return self::$_singleton[$processorName];
  }

  /**
   * This function checks to see if we have the right config values
   *
   * @return string the error message if any
   * @public
   */
  function checkConfig() {
    $config = CRM_Core_Config::singleton();
    $error = [];
    if (empty($this->_paymentProcessor['user_name'])) {
      $error[] = E::ts('The "API key" is not set for this payment processor.', ['domain' => 'be.ctrl.molliepayment']);
    }
    if (!empty($error)) {
      return implode('<p>', $error);
    }
    else {
      return NULL;
    }
  }

  /**
   * Sets appropriate parameters for checking out UCLL Payment
   *
   * @param array $params name value pair of contribution data
   *
   * @return void
   * @access public
   *
   * @throws \Exception
   */
  function doPayment(&$params, $component = 'contribute') {
    if ($component != 'contribute' && $component != 'event') {
      CRM_Core_Error::fatal(ts('Component is invalid'));
    }
    // Mollie object.
    $mollie = new MollieApiClient();
    $mollie->setApiKey($this->_paymentProcessor['user_name']);
    try {
      $payment = $mollie->payments->create([
        "amount" => [
          "currency" => $params['currencyID'],
          "value" => number_format($params['amount'], 2, '.', ''),
        ],
        "description" => $params['description'],
        "redirectUrl" => $this->getReturnSuccessUrl($params['qfKey']),
        "webhookUrl" => $this->getNotifyUrl(),
      ]);
    } catch (\Mollie\Api\Exceptions\ApiException $e) {
      \Civi::log()->error("Molliepayment.php: " . $e->getMessage());
    }
    // Set contribution trxn_id.
    try {
      civicrm_api3("Contribution", 'create', [
        'id' => $params['contributionID'],
        'trxn_id' => $payment->id,
      ]);
    } catch (\CiviCRM_API3_Exception $e) {
      \Civi::log()->error("Molliepayment.php: " . $e->getMessage());
    }
    // Redirect the user to the payment url.
    if (isset($payment->id)) {
      $redirect = $payment->getCheckoutUrl();
      CRM_Utils_System::redirect($redirect);
    }
    else {
      $message = E::ts('Something went wrong with the payment provider connection.', ['domain' => 'be.ucll.ucllpayment']);
      CRM_Core_Session::setStatus(print_r($message, TRUE), '', 'error');
      CRM_Utils_System::redirect($this->getCancelUrl($params['qfKey'], NULL));
    }
  }

  /**
   * New callback function for payment notifications as of Civi 4.2
   */
  public function handlePaymentNotification() {
    require_once 'MolliepaymentIPN.php';
    CRM_Core_Payment_MolliepaymentIPN::main($this->_paymentProcessor);
  }

}
