<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Jdideal\Gateway;

require_once __DIR__ . '/iDEALConnector_config.inc.php';

/**
 * Gets current date and time.
 *
 * @return string    Current date and time.
 */
function getCurrentDateTime()
{
	return utf8_encode(gmdate('Y-m-d\TH:i:s.000\Z'));
}

/**
 * Logs a message to the file.
 *
 * @param   string  $desiredVerbosity  The desired verbosity of the message
 * @param   string  $message           The message to log
 *
 * @return  void
 */
function logMessage($desiredVerbosity, $message)
{
	$logId = JFactory::getApplication()->input->get('logid');
	$jdideal = new Gateway;
	$jdideal->log($message, $logId);
}

/**
 * Configuration manager.
 *
 * @package  JDiDEAL
 * @since    1.0
 */
class ConfigurationManager
{
	/**
	 * Hold the configuration data.
	 *
	 * @var    array
	 * @since  1.0
	 */
	private $config = array();

	private static $configManagerInstance;

	/**
	 * A list of banks for iDEAL Advanced.
	 *
	 * @var    array
	 * @since  3.0
	 */
	private $banks = array();

	/**
	 * Get a ConfigurationManager instance.
	 *
	 * @param   Gateway  $jdideal  An instance of JdidealGateway.
	 *
	 * @return  ConfigurationManager  An instance of itself.
	 *
	 * @since   1.0
	 *
	 * @throws  \Exception
	 * @throws  \RuntimeException
	 * @throws  \InvalidArgumentException
	 */
	public static function getInstance(Gateway $jdideal)
	{
		if (!self::$configManagerInstance)
		{
			self::$configManagerInstance = new ConfigurationManager($jdideal);
		}

		return self::$configManagerInstance;
	}

	/**
	 * Loads the configuration for the MPI interface
	 *
	 * @param   Gateway  $jdideal  An instance of JdidealGateway.
	 *
	 * @throws  Exception
	 * @throws  RuntimeException
	 * @throws  InvalidArgumentException
	 */
	private function __construct(Gateway $jdideal)
	{
		// Setup the bank details
		$this->setupBanks();

		$selectedBank = $jdideal->get('IDEAL_Bank');

		// Get the configuration data
		$this->config['MERCHANTID']             = $jdideal->get('IDEAL_MerchantID');
		$this->config['SUBID']                  = $jdideal->get('IDEAL_SubID');
		$this->config['MERCHANTRETURNURL']      = JUri::root() . 'cli/notify.php';
		$this->config['ACQUIRERURL']            = $this->getBank('IDEAL_AcquirerURL', $selectedBank);
		$this->config['ACQUIRERDIRECTORYURL']   = $this->getBank('IDEAL_AcquirerURL', $selectedBank);
		$this->config['ACQUIRERTRANSACTIONURL'] = $this->getBank('IDEAL_AcquirerURL', $selectedBank);
		$this->config['ACQUIRERSTATUSURL']      = $this->getBank('IDEAL_AcquirerURL', $selectedBank);
		$this->config['ACQUIRERTIMEOUT']        = $this->getBank('IDEAL_AcquirerTimeout', $selectedBank);
		$this->config['EXPIRATIONPERIOD']       = 'PT1H';
		$this->config['PRIVATEKEY']             = 'priv.pem';
		$this->config['PRIVATEKEYPASS']         = $jdideal->get('IDEAL_PrivatekeyPass');
		$this->config['PRIVATECERT']            = 'cert.cer';
		$this->config['CERTIFICATE0']           = $this->getBank('IDEAL_Certificate' . $jdideal->get('certificateID', 0), $selectedBank);
	}

	/**
	 * Setup the banks.
	 *
	 * @return  void.
	 *
	 * @since   4.0
	 */
	private function setupBanks()
	{
		$this->banks['INGSEPA']['IDEAL_AcquirerURL']				= 'https://ideal.secure-ing.com/ideal/iDEALv3';
		$this->banks['INGSEPA']['IDEAL_Certificate0']				= 'ing_sepa0.cer';
		$this->banks['INGSEPA']['IDEAL_Certificate1']				= 'ing_sepa1.cer';
		$this->banks['INGSEPA']['IDEAL_AcquirerTimeout']			= 10;
		$this->banks['INGSEPATEST']['IDEAL_AcquirerURL']			= 'https://idealtest.secure-ing.com/ideal/iDEALv3';
		$this->banks['INGSEPATEST']['IDEAL_Certificate0']			= 'ing_sepa0.cer';
		$this->banks['INGSEPATEST']['IDEAL_Certificate1']			= 'ing_sepa1.cer';
		$this->banks['INGSEPATEST']['IDEAL_AcquirerTimeout']		= 10;

		$this->banks['RABOBANKSEPA']['IDEAL_AcquirerURL']			= 'https://ideal.rabobank.nl/ideal/iDEALv3';
		$this->banks['RABOBANKSEPA']['IDEAL_Certificate0']		    = 'rabobank_sepa0.cer';
		$this->banks['RABOBANKSEPA']['IDEAL_Certificate1']		    = 'rabobank_sepa1.cer';
		$this->banks['RABOBANKSEPA']['IDEAL_AcquirerTimeout']		= 10;
		$this->banks['RABOBANKSEPATEST']['IDEAL_AcquirerURL']		= 'https://idealtest.rabobank.nl/ideal/iDEALv3';
		$this->banks['RABOBANKSEPATEST']['IDEAL_Certificate0']	    = 'rabobank_sepa0.cer';
		$this->banks['RABOBANKSEPATEST']['IDEAL_Certificate1']	    = 'rabobank_sepa1.cer';
		$this->banks['RABOBANKSEPATEST']['IDEAL_AcquirerTimeout']	= 10;

		$this->banks['ABNAMROSEPA']['IDEAL_AcquirerURL']			= 'https://abnamro.ideal-payment.de/ideal/iDEALv3';
		$this->banks['ABNAMROSEPA']['IDEAL_Certificate0']			= 'abnamro_sepa0.cer';
		$this->banks['ABNAMROSEPA']['IDEAL_Certificate1']			= 'abnamro_sepa1.cer';
		$this->banks['ABNAMROSEPA']['IDEAL_AcquirerTimeout']		= 10;
		$this->banks['ABNAMROSEPATEST']['IDEAL_AcquirerURL']		= 'https://abnamro-test.ideal-payment.de/ideal/iDEALv3';
		$this->banks['ABNAMROSEPATEST']['IDEAL_Certificate0']		= 'abnamro_sepa0.cer';
		$this->banks['ABNAMROSEPATEST']['IDEAL_Certificate1']		= 'abnamro_sepa1.cer';
		$this->banks['ABNAMROSEPATEST']['IDEAL_AcquirerTimeout']	= 10;
	}

	/**
	 *  Get the bank configuration.
	 *
	 * @param   string  $name          The name of the bank configuration value.
	 * @param   string  $selectedBank  The default value to return if no bank is found.
	 *
	 * @return  string  The value for the selected entry.
	 *
	 * @since   3.0
	 */
	public function getBank($name, $selectedBank)
	{
		$value = '';

		if (array_key_exists($name, $this->banks[$selectedBank]))
		{
			$value = $this->banks[$selectedBank][$name];
		}

		return $value;
	}

	/**
	 * Checks if the Configuration is set correctly. If an option is not set correctly, it will return an error. This has
	 * to be checked in the begin of every function that needs these settings and if an error occurs, it must rethrown
	 * to show it to the user.
	 *
	 * @return string    Error message when configsetting is missing, if no errors occur, ok is thrown back
	 */
	public function CheckConfig()
	{
		if ($this->config['MERCHANTID'] == "")
		{
			return "MERCHANTID ontbreekt!";
		}
		elseif (strlen($this->config['MERCHANTID']) > 9)
		{
			return "MERCHANTID too long!";
		}
		elseif ($this->config['SUBID'] == "")
		{
			return "SUBID ontbreekt!";
		}
		elseif (strlen($this->config['SUBID']) > 6)
		{
			return "SUBID too long!";
		}
		elseif ($this->config['ACQUIRERURL'] == "")
		{
			return "ACQUIRERURL ontbreekt!";
		}
		elseif ($this->config['MERCHANTRETURNURL'] == "")
		{
			return "MERCHANTRETURNURL ontbreekt!";
		}
		elseif (strlen($this->config['MERCHANTRETURNURL']) > 512)
		{
			return "MERCHANTRETURNURL too long!";
		}
		elseif ($this->config['EXPIRATIONPERIOD'] == "")
		{
			return "EXPIRATIONPERIOD ontbreekt!";
		}
		else
		{
			return "OK";
		}
	}

	/**
	 * Safely get a configuration item.
	 * Returns the value when $name was found, otherwise an emptry string ("").
	 * If "allowMissing" is set to true, it does not generate an error.
	 *
	 * @param string $name The name of the configuration item.
	 * @param bool $allowMissing
	 * @param $result
	 * @return string    The value as specified in the configuration file.
	 */
	public function GetConfiguration($name, $allowMissing, &$result)
	{
		if (isset($this->config[$name]) && ($this->config[$name] != ""))
		{
			return $this->config[$name];
		}

		if ($allowMissing)
		{
			return "";
		}

		logMessage(TRACE_ERROR, "The configuration item [" . $name . "] is not configured in the configuration file.");
		$result = false;

		return false;
	}
}
