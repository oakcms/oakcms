<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Awf\Database;

use Awf\Container\Container;
use Awf\Text\Text;
use Awf\Timer\Timer;

if (!defined('DATA_CHUNK_LENGTH'))
{
	define('DATA_CHUNK_LENGTH', 65536); // How many bytes to read per step
	define('MAX_QUERY_LINES', 300); // How many lines may be considered to be one query (except text lines)
}

/**
 * Database restoration class. This is a generic SQL script execution handler which takes a (possibly segmented in
 * multiple files), generalised (prefixes written as #__) SQL script and runs it against the database in many small
 * steps.
 */
abstract class Restore
{

	/**
	 * A list of error codes (numbers) which should not block cause the
	 * restoration to halt. Used for soft errors and warnings which do not cause
	 * problems with the restored site.
	 *
	 * @var  array
	 */
	protected $allowedErrorCodes = array();

	/**
	 * A list of comment line delimiters. Lines starting with these strings are
	 * skipped over during restoration.
	 *
	 * @var  array
	 */
	protected $comment = array();

	/**
	 * A list of the part files of the database dump we are importing
	 *
	 * @var  array
	 */
	protected $partsMap = array();

	/**
	 * The total size of all database dump files
	 *
	 * @var  integer
	 */
	protected $totalSize = 0;

	/**
	 * The part file currently being processed
	 *
	 * @var  string
	 */
	protected $currentPart = null;

	/**
	 * The offset into the part file being processed
	 *
	 * @var  integer
	 */
	protected $fileOffset = 0;

	/**
	 * The total size of all database dump files processed so far
	 *
	 * @var  integer
	 */
	protected $runSize = 0;

	/**
	 * The file pointer to the SQL file currently being restored
	 *
	 * @var  resource
	 */
	protected $file = null;

	/**
	 * The filename of the SQL file currently being restored
	 *
	 * @var  string
	 */
	protected $filename = null;

	/**
	 * The starting line number of processing the current file
	 *
	 * @var  integer
	 */
	protected $start = null;

	/**
	 * The Timer object used to guard against timeouts
	 *
	 * @var  Timer
	 */
	protected $timer = null;

	/**
	 * The database file key used to determine which dump we're restoring
	 *
	 * @var  string
	 */
	protected $dbKey = null;

	/**
	 * The database driver used to connect to this database
	 *
	 * @var  Driver
	 */
	protected $db = null;

	/**
	 * Total queries run so far
	 *
	 * @var  integer
	 */
	protected $totalQueries = null;

	/**
	 * Line number in the current file being processed
	 *
	 * @var  integer
	 */
	protected $lineNumber = null;

	/**
	 * Number of queries run in this restoration step
	 *
	 * @var  integer
	 */
	protected $queries = null;

	/**
	 * The size of the file being processed
	 *
	 * @var int
	 */
	protected $fileSize = 0;

	/**
	 * Total size of SQL already read from the dump files
	 *
	 * @var int
	 */
	protected $totalSizeRead = 0;

	/**
	 * The container this database restoration class is attached to
	 *
	 * @var  Container
	 */
	protected $container = null;

    /** @var array Internal cache for Restore instances */
    protected static $instances = array();

	/**
	 * Public constructor. Initialises the database restoration engine.
	 *
	 * @param   Container $container The container we are attached to. You need a dbrestore array in it.
	 *
	 * @throws \Exception
	 */
	public function __construct(Container $container)
	{
		if (!isset($container['dbrestore']))
		{
			throw new \Exception(Text::_('AWF_RESTORE_ERROR_NORESTOREDATAINCONTAINER'), 500);
		}

		$this->container = $container;

        if(!isset($container['dbrestore']['dbkey']))
        {
            throw new \Exception(Text::_('AWF_RESTORE_ERROR_NORESTOREDBKEYINCONTAINER'), 500);
        }

		$this->dbKey = $container['dbrestore']['dbkey'];

		$maxExecTime = isset($container['dbrestore']['maxexectime']) ? (int)$container['dbrestore']['maxexectime'] : 5;
		$runTimeBias = isset($container['dbrestore']['runtimebias']) ? (int)$container['dbrestore']['runtimebias'] : 75;

		$maxExecTime = ($maxExecTime <  1) ?  1 : $maxExecTime;
		$runTimeBias = ($runTimeBias < 10) ? 10 : $runTimeBias;

		$this->timer = new Timer($maxExecTime, $runTimeBias);

		$this->populatePartsMap();
	}

	/**
	 * Public destructor. Closes open handlers.
	 *
	 * @return  void
	 */
	public function __destruct()
	{
		if (is_object($this->db))
		{
			if ($this->db instanceof Driver)
			{
				try
				{
					$this->db->disconnect();
				}
				catch (\Exception $exc)
				{
					// Nothing. We just never want to fail when closing the
					// database connection.
				}
			}
		}

		if (is_resource($this->file))
		{
			@fclose($this->file);
		}
	}

	/**
	 * Gets an instance of the database restoration class based on the container.
	 *
	 * @staticvar  array  $instances  The array of \Awf\Database\Restore instances
	 *
	 * @param   Container $container The container the class is attached to
	 *
	 * @return  Restore
	 *
	 * @throws \Exception
	 */
	public static function getInstance(Container $container)
	{
		if (!isset($container['dbrestore']))
		{
			throw new \Exception(Text::_('AWF_RESTORE_ERROR_NORESTOREDATAINCONTAINER'), 500);
		}

        if(!isset($container['dbrestore']['dbkey']))
        {
            throw new \Exception(Text::_('AWF_RESTORE_ERROR_NORESTOREDBKEYINCONTAINER'), 500);
        }

		$dbkey = $container['dbrestore']['dbkey'];

		if (!array_key_exists($dbkey, self::$instances))
		{
            if(!isset($container['dbrestore']['dbtype']))
            {
                throw new \Exception(Text::_('AWF_RESTORE_ERROR_NORESTOREDBTYPEINCONTAINER'), 500);
            }

            $class = '\\Awf\\Database\\Restore\\' . ucfirst($container['dbrestore']['dbtype']);

            if(!class_exists($class, true))
            {
                throw new \Exception(Text::_('AWF_RESTORE_ERROR_RESTORECLASSNOTEXISTS'), 500);
            }

			self::$instances[$dbkey] = new $class($container);
		}

		return self::$instances[$dbkey];
	}

	/**
	 * Remove all cached information from the session storage
	 */
	protected function removeInformationFromStorage()
	{
		$variables = array(
			'start', 'foffset', 'totalqueries', 'curpart',
			'partsmap', 'totalsize', 'runsize'
		);

		$session = $this->container->segment;

		foreach ($variables as $var)
		{
			$key = 'restore_' . $this->dbKey . '_' . $var;
			$session->$key = null;
		}
	}

	/**
	 * Return a value from the session storage
	 *
	 * @param   string $var     The name of the variable
	 * @param   mixed  $default The default value (null if ommitted)
	 *
	 * @return  mixed  The variable's value
	 */
	protected function getFromStorage($var, $default = null)
	{
		$session = $this->container->segment;

		$key = 'restore_' . $this->dbKey . '_' . $var;

		if (!isset($session->$key))
		{
			$session->$key = $default;
		}

		return $session->$key;
	}

	/**
	 * Sets a value to the session storage
	 *
	 * @param   string $var   The name of the variable
	 * @param   mixed  $value The value to store
	 */
	protected function setToStorage($var, $value)
	{
		$session = $this->container->segment;

		$key = 'restore_' . $this->dbKey . '_' . $var;

		$session->$key = $value;
	}

	/**
	 * Gets a database configuration variable as cached in the container
	 *
	 * @param   string $key     The name of the variable to get
	 * @param   mixed  $default Default value (null if skipped)
	 *
	 * @return  mixed  The configuration variable's value
	 */
	protected function getParam($key, $default = null)
	{
		if (array_key_exists($key, $this->container['dbrestore']))
		{
			return $this->container['dbrestore'][$key];
		}
		else
		{
			return $default;
		}
	}

	protected function populatePartsMap()
	{
		// Nothing to do if it's already populated, right?
		if (!empty($this->partsMap))
		{
			return;
		}

		// First, try to fetch from the session storage
		$this->totalSize    = $this->getFromStorage('totalsize', 0);
		$this->runSize      = $this->getFromStorage('runsize', 0);
		$this->partsMap     = $this->getFromStorage('partsmap', array());
		$this->currentPart  = $this->getFromStorage('curpart', 0);
		$this->fileOffset   = $this->getFromStorage('foffset', 0);
		$this->start        = $this->getFromStorage('start', 0);
		$this->totalQueries = $this->getFromStorage('totalqueries', 0);

		// If that didn't work try a full initalisation
		if (empty($this->partsMap))
		{
            if(!isset($this->container['dbrestore']['sqlfile']))
            {
                throw new \RuntimeException('AWF_RESTORE_ERROR_NORESTOREFILEINCONTAINER', 500);
            }

			$sqlfile = $this->container['dbrestore']['sqlfile'];

			$parts = $this->getParam('parts', 1);

			$this->partsMap = array();
			$path = $this->container->sqlPath;
			$this->totalSize = 0;
			$this->runSize = 0;
			$this->currentPart = 0;
			$this->fileOffset = 0;

			for ($index = 0; $index <= $parts; $index++)
			{
				if ($index == 0)
				{
					$basename = $sqlfile;
				}
				else
				{
					$basename = substr($sqlfile, 0, -4) . '.s' . sprintf('%02u', $index);
				}

				$file = $path . '/' . $basename;

				if (!file_exists($file))
				{
					$file = 'sql/' . $basename;
				}

				$filesize = @filesize($file);
				$this->totalSize += intval($filesize);
				$this->partsMap[] = $file;
			}

			$this->setToStorage('totalsize', $this->totalSize);
			$this->setToStorage('runsize', $this->runSize);
			$this->setToStorage('partsmap', $this->partsMap);
			$this->setToStorage('curpart', $this->currentPart);
			$this->setToStorage('foffset', $this->fileOffset);
			$this->setToStorage('start', $this->start);
			$this->setToStorage('totalqueries', $this->totalQueries);
		}
	}

	/**
	 * Proceeds to opening the next SQL part file
	 *
	 * @return bool True on success
	 */
	protected function getNextFile()
	{
		$parts = $this->getParam('parts', 1);

		if ($this->currentPart >= ($parts - 1))
		{
			return false;
		}

		$this->currentPart++;
		$this->fileOffset = 0;

		$this->setToStorage('curpart', $this->currentPart);
		$this->setToStorage('foffset', $this->fileOffset);

		return $this->openFile();
	}

	/**
	 * Opens the SQL part file whose ID is specified in the $curpart variable
	 * and updates the $file, $start and $foffset variables.
	 *
	 * @return  boolean  True on success
	 *
	 * @throws  \Exception  When an error occurs
	 */
	protected function openFile()
	{
		if (!is_numeric($this->currentPart))
		{
			$this->currentPart = 0;
		}
		$this->filename = $this->partsMap[$this->currentPart];

		if (!$this->file = @fopen($this->filename, "rt"))
		{
			throw new \Exception(Text::sprintf('AWF_RESTORE_ERROR_CANTOPENDUMPFILE', $this->filename));
		}
		else
		{
			// Get the file size
			if (fseek($this->file, 0, SEEK_END) == 0)
			{
				$this->fileSize = ftell($this->file);
			}
			else
			{
				throw new \Exception(Text::_('AWF_RESTORE_ERROR_UNKNOWNFILESIZE'));
			}
		}

		// Check start and foffset are numeric values
		if (!is_numeric($this->start) || !is_numeric($this->fileOffset))
		{
			throw new \Exception(Text::_('AWF_RESTORE_ERROR_INVALIDPARAMETERS'));
		}

		$this->start = floor($this->start);
		$this->fileOffset = floor($this->fileOffset);

		// Check $foffset upon $filesize
		if ($this->fileOffset > $this->fileSize)
		{
			throw new \Exception(Text::_('AWF_RESTORE_ERROR_AFTEREOF'));
		}

		// Set file pointer to $foffset
		if (fseek($this->file, $this->fileOffset) != 0)
		{
			throw new \Exception(Text::_('AWF_RESTORE_ERROR_CANTSETOFFSET'));
		}

		return true;
	}

	/**
	 * Returns the instance of the database driver, creating it if it doesn't
	 * exist.
	 *
	 * @return  Driver
	 *
	 * @throws \RuntimeException
	 */
	protected function getDatabase()
	{
		if (!is_object($this->db))
		{
			$options = array(
				'driver'   => $this->container['dbrestore']['dbtype'],
				'database' => $this->container['dbrestore']['dbname'],
				'select'   => 0,
				'host'     => $this->container['dbrestore']['dbhost'],
				'user'     => $this->container['dbrestore']['dbuser'],
				'password' => $this->container['dbrestore']['dbpass'],
				'prefix'   => $this->container['dbrestore']['prefix'],
			);

			$class = '\\Awf\\Database\\Driver\\' . ucfirst(strtolower($options['driver']));

			$this->db = new $class($options);
			$this->db->setUTF();
		}

		return $this->db;
	}

	/**
	 * Executes a SQL statement, ignoring errors in the $allowedErrorCodes list.
	 *
	 * @param   string $sql The SQL statement to execute
	 *
	 * @return  mixed  A database cursor on success, false on failure
	 *
	 * @throws  \Exception  On error
	 */
	protected function execute($sql)
	{
		$db = $this->getDatabase();

		try
		{
			$db->setQuery($sql);
			$result = $db->execute();
		}
		catch (\Exception $exc)
		{
			$result = false;
			if (!in_array($exc->getCode(), $this->allowedErrorCodes))
			{
				// Format the error message and throw it again
				$message = '<h2>' . Text::sprintf('AWF_RESTORE_ERROR_ERRORATLINE', $this->lineNumber) . '</h2>' . "\n";
				$message .= '<p>' . Text::_('AWF_RESTORE_ERROR_MYSQLERROR') . '</p>' . "\n";
				$message .= '<code>ErrNo #' . htmlspecialchars($exc->getCode()) . '</code>' . "\n";
				$message .= '<pre>' . htmlspecialchars($exc->getMessage()) . '</pre>' . "\n";
				$message .= '<p>' . Text::_('AWF_RESTORE_ERROR_RAWQUERY') . '</p>' . "\n";
				$message .= '<pre>' . htmlspecialchars($sql) . '</pre>' . "\n";

				// Rethrow the exception if we're not supposed to handle it
				throw new \Exception($message);
			}
		}

		return $result;
	}

	/**
	 * Read the next line from the database dump
	 *
	 * @return  string  The query string
	 *
	 * @throws  \Exception
	 */
	protected function readNextLine()
	{
		$parts = $this->getParam('parts', 1);

		$query = "";

		while (!feof($this->file) && (strpos($query, "\n") === false))
		{
			$query .= fgets($this->file, DATA_CHUNK_LENGTH);
		}

		// An empty query is EOF. Are we done or should I skip to the next file?
		if (empty($query) || ($query === false))
		{
			if ($this->currentPart >= ($parts - 1))
			{
				throw new \Exception('All done', 200);
			}
			else
			{
				// Register the bytes read
				$current_foffset = @ftell($this->file);

				if (is_null($this->fileOffset))
				{
					$this->fileOffset = 0;
				}

				$this->runSize = (is_null($this->runSize) ? 0 : $this->runSize) + ($current_foffset - $this->fileOffset);

				// Get the next file
				$this->getNextFile();

				// Rerun the fetcher
				throw new \Exception('Continue', 201);
			}
		}

		if (substr($query, -1) != "\n")
		{
			// We read more data than we should. Roll back the file...
			$newLinePos = strpos($query, "\n");

			if ($newLinePos !== false)
			{
				$queryLength = strlen($query);
				$rollback = $queryLength - $newLinePos;
				fseek($this->file, -$rollback, SEEK_CUR);
				// ...and chop the line
				$query = substr($query, 0, $rollback);
			}
		}

		// Handle DOS linebreaks
		$query = str_replace("\r\n", "\n", $query);
		$query = str_replace("\r", "\n", $query);

		// Skip comments and blank lines only if NOT in parentheses
		$skipline = false;
		reset($this->comment);

		foreach ($this->comment as $comment_value)
		{
			if (trim($query) == "" || strpos($query, $comment_value) === 0)
			{
				$skipline = true;
				break;
			}
		}

		if ($skipline)
		{
			$this->lineNumber++;
			throw new \Exception('Continue', 201);
		}

		$query = trim($query, " \n");
		$query = rtrim($query, ';');

		return $query;
	}

	/**
	 * Runs a restoration step and returns an array to be used in the response.
	 *
	 * @return  array
	 *
	 * @throws \Exception
	 */
	public function stepRestoration()
	{
		$parts = $this->getParam('parts', 1);
		$this->openFile();

		$this->lineNumber    = $this->start;
		$this->totalSizeRead = 0;
		$this->queries       = 0;

		while ($this->timer->getTimeLeft() > 0)
		{
			$query = '';

			// Get the next query line
			try
			{
				$query = $this->readNextLine();
			}
			catch (\Exception $exc)
			{
				if ($exc->getCode() == 200)
				{
					break;
				}
				elseif ($exc->getCode() == 201)
				{
					continue;
				}
			}

			if (empty($query))
			{
				continue;
			}

			// Update variables
			$this->totalSizeRead += strlen($query);
			$this->totalQueries++;
			$this->queries++;
			$this->lineNumber++;

			// Process the query line, running drop/rename queries as necessary
			$this->processQueryLine($query);
		}

		// Get the current file position
		$current_foffset = ftell($this->file);

		if ($current_foffset === false)
		{
			if (is_resource($this->file))
			{
				@fclose($this->file);
			}

			throw new \Exception(Text::_('AWF_RESTORE_ERROR_CANTREADPOINTER'));
		}
		else
		{
			if (is_null($this->fileOffset))
			{
				$this->fileOffset = 0;
			}

			$bytes_in_step    = $current_foffset - $this->fileOffset;
			$this->runSize    = (is_null($this->runSize) ? 0 : $this->runSize) + $bytes_in_step;
			$this->fileOffset = $current_foffset;
		}

		// Return statistics
		$bytes_togo = $this->totalSize - $this->runSize;

		// Check for global EOF
		if (($this->currentPart >= ($parts - 1)) && feof($this->file))
		{
			$bytes_togo = 0;
		}

		// Save variables in storage
		$this->setToStorage('start', $this->start);
		$this->setToStorage('foffset', $this->fileOffset);
		$this->setToStorage('totalqueries', $this->totalQueries);
		$this->setToStorage('runsize', $this->runSize);

		if ($bytes_togo == 0)
		{
			// Clear stored variables if we're finished
			$this->removeInformationFromStorage();
		}

		// Calculate estimated time
		$bytesPerSecond = $bytes_in_step / $this->timer->getRunningTime();

		if ($bytesPerSecond <= 0.01)
		{
			$remainingSeconds = 120;
		}
		else
		{
			$remainingSeconds = round($bytes_togo / $bytesPerSecond, 0);
		}

		// Return meaningful data
		return array(
			'percent'          => round(100 * ($this->runSize / $this->totalSize), 1),
			'restored'         => $this->sizeformat($this->runSize),
			'total'            => $this->sizeformat($this->totalSize),
			'queries_restored' => $this->totalQueries,
			'current_line'     => $this->lineNumber,
			'current_part'     => $this->currentPart,
			'total_parts'      => $parts,
			'eta'              => $this->etaformat($remainingSeconds),
			'error'            => '',
			'done'             => ($bytes_togo == 0) ? '1' : '0'
		);
	}

	/**
	 * Processes the query line in the best way each restoration engine sees
	 * fit. This method is supposed to take care of backing up and dropping
	 * tables, changing table collation if requested and converting INSERT to
	 * REPLACE if requested. It is also supposed to execute $query against the
	 * database, replacing the metaprefix #__ with the real prefix.
	 *
	 * @param   string $query
	 *
	 * @return  string  The processed query
	 */
	abstract protected function processQueryLine($query);

	/**
	 * Format a raw time in seconds as a human readable string
	 *
	 * @param   integer $Raw       Time in seconds
	 * @param   string  $measureby Unit of measurement, leave blank to auto-detect
	 *
	 * @return  string  Human readable time string
	 */
	private function etaformat($Raw, $measureby = '')
	{
		$Clean = abs($Raw);

		$calcNum = array(
			array('s', 60),
			array('m', 60 * 60),
			array('h', 60 * 60 * 60),
			array('d', 60 * 60 * 60 * 24),
			array('y', 60 * 60 * 60 * 24 * 365)
		);

		$calc = array(
			's' => array(1, 'second'),
			'm' => array(60, 'minute'),
			'h' => array(60 * 60, 'hour'),
			'd' => array(60 * 60 * 24, 'day'),
			'y' => array(60 * 60 * 24 * 365, 'year')
		);

		if ($measureby == '')
		{
			$usemeasure = 's';

			for ($i = 0; $i < count($calcNum); $i++)
			{
				if ($Clean <= $calcNum[$i][1])
				{
					$usemeasure = $calcNum[$i][0];
					$i = count($calcNum);
				}
			}
		}
		else
		{
			$usemeasure = $measureby;
		}

		$datedifference = floor($Clean / $calc[$usemeasure][0]);

		if ($datedifference == 1)
		{
			return $datedifference . ' ' . $calc[$usemeasure][1];
		}
		else
		{
			return $datedifference . ' ' . $calc[$usemeasure][1] . 's';
		}
	}

	/**
	 * Returns the cached total size of the SQL dump.
	 *
	 * @param   boolean $use_units Should I automatically figure out the unit of measurement
	 *
	 * @return  string
	 */
	public function getTotalSize($use_units = false)
	{
		$size = $this->totalSize;

		if ($use_units)
		{
			$size = $this->sizeformat($size);
		}

		return $size;
	}

	/**
	 * Format a size in bytes into a human readable format
	 *
	 * @param   string $size The size in bytes
	 *
	 * @return  string  The human readable size string
	 */
	private function sizeformat($size)
	{
		if ($size < 0)
		{
			return 0;
		}
		$unit = array('b', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb');
		$i = floor(log($size, 1024));
		if (($i < 0) || ($i > 5))
		{
			$i = 0;
		}

		return @round($size / pow(1024, ($i)), 2) . ' ' . $unit[$i];
	}
}