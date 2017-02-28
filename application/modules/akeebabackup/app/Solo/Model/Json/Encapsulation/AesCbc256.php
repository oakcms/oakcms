<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model\Json\Encapsulation;

use Akeeba\Engine\Factory;

/**
 * AES CBC 256 encapsulation
 */
class AesCbc256 extends Base
{
	/**
	 * Constructs the encapsulation handler object
	 */
	function __construct()
	{
		parent::__construct(5, 'ENCAPSULATION_AESCBC256', ' Data in AES-256 standard (CBC) mode encrypted JSON');
	}

	/**
	 * Decodes the data. For encrypted encapsulations this means base64-decoding the data, decrypting it and then JSON-
	 * decoding the result. If any error occurs along the way the appropriate exception is thrown.
	 *
	 * The data being decoded corresponds to the Request Body described in the API documentation
	 *
	 * @param   string  $serverKey  The server key we need to decode data
	 * @param   string  $data       Encoded data
	 *
	 * @return  string  The decoded data.
	 *
	 * @throws  \RuntimeException  When the server capabilities don't match the requested encapsulation
	 * @throws  \InvalidArgumentException  When $data cannot be decoded successfully
	 *
	 * @see     https://www.akeebabackup.com/documentation/json-api/ar01s02.html
	 */
	public function decode($serverKey, $data)
	{
		$data = base64_decode($data);
		return Factory::getEncryption()->AESDecryptCBC($data, $serverKey, 256);
	}

	/**
	 * Encodes the data. The data is JSON encoded by this method before encapsulation takes place. Encrypted
	 * encapsulations will then encrypt the data and base64-encode it before returning it.
	 *
	 * The data being encoded correspond to the body > data structure described in the API documentation
	 *
	 * @param   string  $serverKey  The server key we need to encode data
	 * @param   mixed   $data       The data to encode, typically a string, array or object
	 *
	 * @return  string  The encapsulated data
	 *
	 * @see     https://www.akeebabackup.com/documentation/json-api/ar01s02s02.html
	 *
	 * @throws  \RuntimeException  When the server capabilities don't match the requested encapsulation
	 * @throws  \InvalidArgumentException  When $data cannot be converted to JSON
	 */
	public function encode($serverKey, $data)
	{
		return base64_encode(Factory::getEncryption()->AESEncryptCBC($data, $serverKey, 256));
	}
}