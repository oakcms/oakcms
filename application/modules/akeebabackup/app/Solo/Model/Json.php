<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model;

use Awf\Mvc\Model;
use Akeeba\Engine\Platform;
use Akeeba\Engine\Factory;
use Solo\Model\Json\Encapsulation;
use Solo\Model\Json\Task;

// JSON API version number
define('AKEEBA_JSON_API_VERSION', '340');

/*
 * Short API version history:
 * 300	First draft. Basic backup working. Encryption semi-broken.
 * 316	Fixed download feature.
 * 320  Minor bug fixes
 * 330  Introduction of Akeeba Solo
 * 335  Configuration overrides in startBackup
 * 340  Advanced API allows full configuration
 */

if (!defined('AKEEBA_BACKUP_ORIGIN'))
{
	define('AKEEBA_BACKUP_ORIGIN', 'json');
}

class Json extends Model
{
	const    COM_AKEEBA_CPANEL_LBL_STATUS_OK = 200; // Normal reply
	const    STATUS_NOT_AUTH = 401; // Invalid credentials
	const    STATUS_NOT_ALLOWED = 403; // Not enough privileges
	const    STATUS_NOT_FOUND = 404; // Requested resource not found
	const    STATUS_INVALID_METHOD = 405; // Unknown JSON method
	const    COM_AKEEBA_CPANEL_LBL_STATUS_ERROR = 500; // An error occurred
	const    STATUS_NOT_IMPLEMENTED = 501; // Not implemented feature
	const    STATUS_NOT_AVAILABLE = 503; // Remote service not activated

	/** @var int The status code */
	private $status = 200;

	/** @var int Data encapsulation format */
	private $encapsulationType = 1;

	/** @var  Encapsulation */
	private $encapsulation;

	/** @var mixed Any data to be returned to the caller */
	private $data = '';

	/** @var string A password passed to us by the caller */
	private $password = null;

	/** @var string The method called by the client */
	private $method_name = null;

	/**
	 * Override the constructor to construct the objects we need to handle JSON API requests
	 *
	 * @param \Awf\Container\Container $container
	 */
	public function __construct(\Awf\Container\Container $container = null)
	{
		parent::__construct($container);

		$this->encapsulation = new Encapsulation($this->serverKey());
	}

	public function execute($json)
	{
		// Check if we're activated
		$enabled = Platform::getInstance()->get_platform_configuration_option('frontend_enable', 0);

		// Is the Secret Key strong enough?
		$validKey = $this->serverKey();

		if (!\Akeeba\Engine\Util\Complexify::isStrongEnough($validKey, false))
		{
			$enabled = false;
		}

		$rawEncapsulation = $this->encapsulation->getEncapsulationByCode('ENCAPSULATION_RAW');

		if (!$enabled)
		{
			return $this->getResponse('Access denied', 503);
		}

		// Try to JSON-decode the request's input first
		$request = @json_decode($json, true);

		if (is_null($request))
		{
			return $this->getResponse('JSON decoding error', 500);
		}

		// Transform legacy requests
		if (!is_array($request))
		{
			$request = array(
				'encapsulation' => $rawEncapsulation,
				'body' => $request
			);
		}

		// Transform partial requests
		if (!isset($request['encapsulation']))
		{
			$request['encapsulation'] = $rawEncapsulation;
		}

		// Make sure we have a request body
		if (!isset($request['body']))
		{
			$request['body'] = '';
		}

		try
		{
			$request['body'] = $this->encapsulation->decode($request['encapsulation'], $request['body']);
		}
		catch (\Exception $e)
		{
			return $this->getResponse($e->getMessage(), $e->getCode());
		}

		// Replicate the encapsulation preferences of the client for our own output
		$this->encapsulationType = $request['encapsulation'];

		// Store the client-specified key, or use the server key if none specified and the request
		// came encrypted.
		$this->password = isset($request['body']['key']) ? $request['body']['key'] : $this->serverKey();

		// Run the method
		$params = array();

		if (isset($request['body']['data']))
		{
			$params = (array)$request['body']['data'];
		}

		try
		{
			$taskHandler = new Task();
			$data = $taskHandler->execute($request['body']['method'], $params);
		}
		catch (\RuntimeException $e)
		{
			return $this->getResponse($e->getMessage(), $e->getCode());
		}

		return $this->getResponse($data);
	}

	/**
	 * Packages the response to a JSON-encoded object, optionally encrypting the data part with a caller-supplied
	 * password.
	 *
	 * @return  string  The JSON-encoded response
	 */
	private function getResponse($data, $status = 200)
	{
		// Initialize the response
		$response = array(
			'encapsulation' => $this->encapsulationType,
			'body'          => array(
				'status' => $status,
				'data'   => null
			)
		);

		if ($status != 200)
		{
			$response['encapsulation'] = $this->encapsulation->getEncapsulationByCode('ENCAPSULATION_RAW');
		}

		try
		{
			$response['body']['data'] = $this->encapsulation->encode($response['encapsulation'], $data, $this->password);
		}
		catch (\Exception $e)
		{
			$response['encapsulation'] = $this->encapsulation->getEncapsulationByCode('ENCAPSULATION_RAW');
			$response['body'] = array(
				'status' => $e->getCode(),
				'data' => $e->getMessage(),
			);
		}

		return '###' . json_encode($response) . '###';
	}

	/**
	 * Get the server key, i.e. the Secret Word for the front-end backups and JSON API
	 *
	 * @return  mixed
	 */
	private function serverKey()
	{
		static $key = null;

		if (is_null($key))
		{
			$key = Platform::getInstance()->get_platform_configuration_option('frontend_secret_word', '');
		}

		return $key;
	}
}