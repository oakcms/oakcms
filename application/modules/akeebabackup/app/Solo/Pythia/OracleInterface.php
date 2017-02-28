<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Pythia;

interface OracleInterface
{
	/**
	 * Creates a new oracle objects
	 *
	 * @param   string  $path  The directory path to scan
	 */
	public function __construct($path);

	/**
	 * Does this class recognises the script / CMS type?
	 *
	 * @return  boolean
	 */
	public function isRecognised();

	/**
	 * Return the name of the CMS / script
	 *
	 * @return  string
	 */
	public function getName();

	/**
	 * Return the default installer name for this CMS / script
	 *
	 * @return  string
	 */
	public function getInstaller();

	/**
	 * Return the database connection information for this CMS / script
	 *
	 * @return  array
	 */
	public function getDbInformation();

	/**
	 * Return extra directories required by the CMS / script
	 *
	 * @return array
	 */
	public function getExtradirs();

    /**
     * Return extra databases required by the CMS / script (ie Drupal multi-site)
     *
     * @return array
     */
    public function getExtraDb();
}