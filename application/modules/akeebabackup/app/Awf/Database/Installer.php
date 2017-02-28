<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 *
 * This class is adapted from the Joomla! Framework
 */

namespace Awf\Database;


use Awf\Container\Container;
use Awf\Filesystem\File;

class Installer
{
	/** @var  Driver  The database connector object */
	private $db = null;

	/** @var  string  The directory where the XML schema files are stored */
	private $xmlDirectory = null;

    /** @var  string  Force a specific **absolute** file path for the XML schema file */
    private $forcedFile = null;

	/**
	 * Public constructor
	 *
	 * @param   Container  $container  The application container
	 */
	public function __construct(Container $container)
	{
		$this->xmlDirectory = $container->basePath . '/assets/sql/xml';
		$this->db           = $container->db;
	}

	/**
	 * Sets the directory where XML schema files are stored
	 *
	 * @param   string  $xmlDirectory
     *
     * @codeCoverageIgnore
	 */
	public function setXmlDirectory($xmlDirectory)
	{
		$this->xmlDirectory = $xmlDirectory;
	}

	/**
	 * Returns the directory where XML schema files are stored
	 *
	 * @return  string
     *
     * @codeCoverageIgnore
	 */
	public function getXmlDirectory()
	{
		return $this->xmlDirectory;
	}

    /**
     * Returns the absolute path to the forced XML schema file
     *
     * @return  string
     *
     * @codeCoverageIgnore
     */
    public function getForcedFile()
    {
        return $this->forcedFile;
    }

    /**
     * Sets the absolute path to an XML schema file which will be read no matter what. Set to a blank string to let the
     * Installer class auto-detect your schema file based on your database type.
     *
     * @param  string  $forcedFile
     *
     * @codeCoverageIgnore
     */
    public function setForcedFile($forcedFile)
    {
        $this->forcedFile = $forcedFile;
    }

    /**
     * Creates or updates the database schema
     *
     * @return  void
     *
     * @throws  \Exception  When a database query fails and it doesn't have the canfail flag
     */
    public function updateSchema()
    {
        // Get the schema XML file
        $xml = $this->findSchemaXml();

        if (empty($xml))
        {
            return;
        }

        // Make sure there are SQL commands in this file
        if (!$xml->sql)
        {
            return;
        }

        // Walk the sql > action tags to find all tables
        $tables = array();
        /** @var \SimpleXMLElement $actions */
        $actions = $xml->sql->children();

        /** @var \SimpleXMLElement $action */
        foreach ($actions as $action)
        {
            // Get the attributes
            $attributes = $action->attributes();

            // Get the table / view name
            $table = $attributes->table ? $attributes->table : '';

            if (empty($table))
            {
                continue;
            }

            // Am I allowed to let this action fail?
            $canFailAction = $attributes->canfail ? $attributes->canfail : 0;

            // Evaluate conditions
            $shouldExecute = true;

            /** @var \SimpleXMLElement $node */
            foreach ($action->children() as $node)
            {
                if ($node->getName() == 'condition')
                {
                    // Get the operator
                    $operator = $node->attributes()->operator ? (string)$node->attributes()->operator : 'and';
                    $operator = empty($operator) ? 'and' : $operator;

                    $condition = $this->conditionMet($table, $node);

                    switch ($operator)
                    {
                        case 'not':
                            $shouldExecute = $shouldExecute && !$condition;
                            break;

                        case 'or':
                            $shouldExecute = $shouldExecute || $condition;
                            break;

                        case 'nor':
                            $shouldExecute = !$shouldExecute && !$condition;
                            break;

                        case 'xor':
                            $shouldExecute = ($shouldExecute xor $condition);
                            break;

                        case 'maybe':
                            $shouldExecute = $condition ? true : $shouldExecute;
                            break;

                        default:
                            $shouldExecute = $shouldExecute && $condition;
                            break;
                    }
                }

                // DO NOT USE BOOLEAN SHORT CIRCUIT EVALUATION!
                // if (!$shouldExecute) break;
            }

            // Make sure all conditions are met
            if (!$shouldExecute)
            {
                continue;
            }

            // Execute queries
            foreach ($action->children() as $node)
            {
                if ($node->getName() == 'query')
                {
                    $canFail = $node->attributes->canfail ? (string)$node->attributes->canfail : $canFailAction;

                    if (is_string($canFail))
                    {
                        $canFail = strtoupper($canFail);
                    }

                    $canFail = (in_array($canFail, array(true, 1, 'YES', 'TRUE')));

                    $this->db->setQuery((string) $node);

                    try
                    {
                        $this->db->execute();
                    }
                    catch (\Exception $e)
                    {
                        // If we are not allowed to fail, throw back the exception we caught
                        if (!$canFail)
                        {
                            throw $e;
                        }
                    }
                }
            }
        }
    }

    /**
     * Uninstalls the database schema
     *
     * @return  void
     */
    public function removeSchema()
    {
        // Get the schema XML file
        $xml = $this->findSchemaXml();

        if (empty($xml))
        {
            return;
        }

        // Make sure there are SQL commands in this file
        if (!$xml->sql)
        {
            return;
        }

        // Walk the sql > action tags to find all tables
        $tables = array();
        /** @var \SimpleXMLElement $actions */
        $actions = $xml->sql->children();

        /** @var \SimpleXMLElement $action */
        foreach ($actions as $action)
        {
            $attributes = $action->attributes();
            $tables[] = (string)$attributes->table;
        }

        // Simplify the tables list
        $tables = array_unique($tables);

        // Start dropping tables
        foreach ($tables as $table)
        {
            try
            {
                $this->db->dropTable($table);
            }
            catch (\Exception $e)
            {
                // Do not fail if I can't drop the table
            }
        }
    }

    /**
     * Find an suitable schema XML file for this database type and return the SimpleXMLElement holding its information
     *
     * @return  null|\SimpleXMLElement  Null if no suitable schema XML file is found
     */
    protected function findSchemaXml()
    {
        $xml = null;

        // Do we have a forced file?
        if ($this->forcedFile)
        {
            $xml = $this->openAndVerify($this->forcedFile);

            if ($xml !== false)
            {
                return $xml;
            }
        }

        // Get all XML files in the schema directory
        $filesystem = new File(array());
        $xmlFiles   = $filesystem->directoryFiles($this->xmlDirectory, '\.xml$');

        if (empty($xmlFiles))
        {
            return $xml;
        }

        foreach ($xmlFiles as $baseName)
        {
            // Remove any accidental whitespace
            $baseName = trim($baseName);

            // Get the full path to the file
            $fileName = $this->xmlDirectory . '/' . $baseName;

            $xml = $this->openAndVerify($fileName);

            if ($xml !== false)
            {
                return $xml;
            }
        }

        return null;
    }

    /**
     * Opens the schema XML file and return the SimpleXMLElement holding its information. If the file doesn't exist, it
     * is not a schema file or it doesn't match our database driver we return boolean false.
     *
     * @return  false|\SimpleXMLElement  False if it's not a suitable XML schema file
     */
    protected function openAndVerify($fileName)
    {
        $driverType = $this->db->name;

        // Make sure the file exists
        if (!@file_exists($fileName))
        {
            return false;
        }

        // Make sure the file is a valid XML document
        try
        {
            $xml = new \SimpleXMLElement($fileName, LIBXML_NONET, true);
        }
        catch (\Exception $e)
        {
            $xml = null;

            return false;
        }

        // Make sure the file is an XML schema file
        if ($xml->getName() != 'schema')
        {
            $xml = null;

            return false;
        }

        if (!$xml->meta)
        {
            $xml = null;

            return false;
        }

        if (!$xml->meta->drivers)
        {
            $xml = null;

            return false;
        }

        /** @var \SimpleXMLElement $drivers */
        $drivers = $xml->meta->drivers;

        foreach ($drivers->children() as $driverTypeTag)
        {
            $thisDriverType = (string)$driverTypeTag;

            if ($thisDriverType == $driverType)
            {
                return $xml;
            }
        }

        return false;
    }

    /**
     * Checks if a condition is met
     *
     * @param   string            $table  The table we're operating on
     * @param   \SimpleXMLElement  $node   The condition definition node
     *
     * @return  bool
     */
    protected function conditionMet($table, \SimpleXMLElement $node)
    {
        static $allTables = null;

        if (empty($allTables))
        {
            $allTables = $this->db->getTableList();
        }

        // Does the table exist?
        $tableNormal = $this->db->replacePrefix($table);
        $tableExists = in_array($tableNormal, $allTables);

        // Initialise
        $condition = false;

        // Get the condition's attributes
        $attributes = $node->attributes();
        $type = $attributes->type ? $attributes->type : null;
        $value = $attributes->value ? (string) $attributes->value : null;

        switch ($type)
        {
            // Check if a table or column is missing
            case 'missing':
                $fieldName = (string)$value;

                if (empty($fieldName))
                {
                    $condition = !$tableExists;
                }
                else
                {
                    $tableColumns = $this->db->getTableColumns($tableNormal, true);
                    $condition = !array_key_exists($fieldName, $tableColumns);
                }
                break;

            // Check if a column type matches the "coltype" attribute
            case 'type':
                $tableColumns = $this->db->getTableColumns($table, false);
                $condition = false;

                if (array_key_exists($value, $tableColumns))
                {
                    $coltype = $attributes->coltype ? $attributes->coltype : null;

                    if (!empty($coltype))
                    {
                        $coltype = strtolower($coltype);
                        $currentType = strtolower($tableColumns[$value]->Type);

                        $condition = ($coltype == $currentType);
                    }
                }

                break;

            // Check if the result of a query matches our expectation
            case 'equals':
                $query = (string)$node;
                $this->db->setQuery($query);

                try
                {
                    $result = $this->db->loadResult();
                    $condition = ($result == $value);
                }
                catch (\Exception $e)
                {
                    return false;
                }

                break;

            // Always returns true
            case 'true':
                return true;
                break;

            default:
                return false;
                break;
        }

        return $condition;
    }
}