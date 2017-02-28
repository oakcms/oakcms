<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license GNU GPL version 3 or, at your option, any later version
 * @package akeebaengine
 *
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Abstract class for checks. Every domain that performs checks should extend this one
 */
class AliceCoreDomainAbstract extends AliceAbstractPart
{
	/** @var int Domain priority */
	public     $priority = 20;
	/** @var int Progress percentage */
	protected  $progress = 0;
	/** @var null Handle to Akeeba Backup log to analyze */
	protected  $log      = null;
    /** @var string Name of the current step */
    protected  $stepName = '';
    /** @var string Name of the checks to load */
    protected  $checksName = '';
	/** @var array Stack of check to be performed */
	private    $checks   = array();
	/** @var int   Total number of checks to be performed */
	private    $totalChecks = 0;

	public function __construct($priority, $checksName, $stepName)
	{
        $this->priority   = $priority;
        $this->checksName = $checksName;
        $this->stepName   = $stepName;

		parent::__construct();
		AliceUtilLogger::WriteLog(_AE_LOG_DEBUG, __CLASS__." :: New instance");
	}

	protected function _prepare()
	{
		$this->progress = 0;
		$this->setStep($this->stepName);
		$this->setState('prepared');
	}

	protected function _run()
	{
		// Run in a loop until we run out of time, or breakflag is set
		$registry = AliceFactory::getConfiguration();
		$timer    = AliceFactory::getTimer();

		// Let's check if I already have a stack coming from previous call
		$this->log         = $registry->get('volatile.alice.logToAnalyze');
		$this->checks      = $registry->get('volatile.alice.'.$this->checksName.'.checks', array());
		$this->totalChecks = $registry->get('volatile.alice.'.$this->checksName.'.totalChecks', 0);

		// No incoming stack, let's build it now
		if(!$this->checks)
		{
			$this->checks      = AliceUtilScripting::getChecksStack($this->checksName);
			$this->totalChecks = count($this->checks);
		}

		while( ($timer->getTimeLeft() > 0) && (!$registry->get('volatile.breakflag', false)) && count($this->checks))
		{
			if ($this->getState() == 'postrun')
			{
				AliceUtilLogger::WriteLog(_AE_LOG_DEBUG, __CLASS__." :: Already finished");
				$this->setStep("-");
				$this->setSubstep("");

				break;
			}
			else
			{
				// Did I finished every check?
				if(!$this->checks)
				{
					return;
				}

				$error     = '';
				$solution  = '';
				$className = array_shift($this->checks);

				/** @var AliceCoreDomainChecksAbstract $check */
				$check     = new $className($this->log);

				$this->setSubstep($check->getName());
				$this->progress = ($this->totalChecks - count($this->checks)) / $this->totalChecks;

				// Well, check, do your job!
				try
				{
					$check->check();
				}
				catch(Exception $e)
				{
					// Mhm... log didn't passed the check. Let's save the error and the suggested solution
					$error    = $e->getMessage();
					$solution = $check->getSolution();
				}

                $result = $check->getResult();

				$feedback = $registry->get('volatile.alice.feedback', array());

				$feedback[] = array(
					'check'     => $check->getName(),
					'result'    => $result,
					'error'     => $error,
					'solution'  => $solution,
					'raw'       => array(
						'check' => $check->getCheckLangKey(),
						'error' => $check->getErrLangKey()
					)
				);

				$registry->set('volatile.alice.feedback', $feedback);

				unset($check);
			}
		}

		// Let's save everything
		$registry->set('volatile.alice.requirements.checks', $this->checks);
		$registry->set('volatile.alice.requirements.totalChecks', $this->totalChecks);

		$this->setState('postrun');
	}

	protected function _finalize()
	{
		$this->setState('finished');
	}

	public function getProgress()
	{
		return $this->progress;
	}

    public function getStepName()
    {
        return $this->stepName;
    }
}