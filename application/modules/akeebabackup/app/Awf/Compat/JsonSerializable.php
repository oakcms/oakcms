<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

/**
 * JsonSerializable interface. This file should only be loaded on PHP < 5.4
 * It allows us to implement it in classes without requiring PHP 5.4
 *
 * @link   http://www.php.net/manual/en/jsonserializable.jsonserialize.php
 *
 * @codeCoverageIgnore
 */
interface JsonSerializable
{
	/**
	 * Return data which should be serialized by json_encode().
	 *
	 * @return  mixed
	 *
	 * @since   1.0
	 */
	public function jsonSerialize();
}
