<?php

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
   */
  static function main() {
    // Fetch POST variables.
    $variables = json_decode(file_get_contents('php://input'), TRUE);

    // @todo handle payment webhook!
    \Civi::log()->error("MolliepaymentIPN.php: " . print_r($variables, true));

  }

}