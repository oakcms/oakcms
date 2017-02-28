<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Controller;


use Awf\Text\Text;

class Alice extends ControllerDefault
{
    public function ajax()
    {
    	/** @var \Solo\Model\Alice $model */
        $model = $this->getModel();

        $model->setState('ajax', $this->input->get('ajax', '', 'cmd'));
        $model->setState('log', $this->input->get('log', '', 'cmd'));

        $ret_array = $model->runAnalysis();

        @ob_end_clean();
        header('Content-type: text/plain');
        echo '###' . json_encode($ret_array) . '###';
        flush();

        $this->container->application->close();
    }

    public function domains()
    {
        $return  = array();
        $domains = \AliceUtilScripting::getDomainChain();

        foreach($domains as $domain)
        {
            $return[] = array($domain['domain'], $domain['name']);
        }

        @ob_end_clean();
        header('Content-type: text/plain');
        echo '###'.json_encode($return).'###';
        flush();

        $this->container->application->close();
    }

	/**
	 * Translates language key in English strings
	 */
	public function translate()
	{
		$return  = array();
		$strings = $this->input->getString('keys', '');
		$strings = json_decode($strings);

		// Text always loads all the languages, so we have to convince him very hard to do what we want
		// First of all let's empty the $strings variable
		$property = new \ReflectionProperty('\Awf\Text\Text', 'strings');
		$property->setAccessible(true);
		$property->setValue(array());

		// Then load only the English language
		Text::loadLanguage('en-GB', 'akeebabackup', '.com_akeebabackup.ini', false, $this->container->languagePath);
		Text::loadLanguage('en-GB', 'akeeba', '.com_akeeba.ini', false, $this->container->languagePath);

		foreach ($strings as $string)
		{
			$temp['check'] = Text::_($string->check);

			// If I have an array, it means that I have to use sprintf to translate the error
			if (is_array($string->error))
			{
				$trans[] = Text::_($string->error[0]);
				$args    = array_merge($trans, array_slice($string->error, 1));
				$error   = call_user_func_array('sprintf', $args);
			}
			else
			{
				$error = Text::_($string->error);
			}

			$temp['error'] = $error;

			$return[] = $temp;
		}

		@ob_end_clean();
		header('Content-type: text/plain');
		echo '###' . json_encode($return) . '###';
		flush();

		$this->container->application->close();
	}
}