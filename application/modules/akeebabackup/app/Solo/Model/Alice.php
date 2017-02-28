<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model;

use Awf\Mvc\Model;
use Akeeba\Engine\Factory;

class Alice extends Model
{
    public function runAnalysis()
    {
        $ret_array = array();

        $ajaxTask = $this->getState('ajax');
        $log      = $this->getState('log');

        switch($ajaxTask)
        {
            case 'start':
                $tag = 'alice';

                \AliceUtilLogger::WriteLog(true);
                \AliceUtilLogger::WriteLog(_AE_LOG_INFO, 'Starting analysis');

                \AliceCoreKettenrad::reset(array(
                    'maxrun'=> 0
                ));
                \AliceUtilTempvars::reset($tag);

                $kettenrad = \AliceCoreKettenrad::load($tag);

                $options = array('logToAnalyze' => Factory::getLog()->getLogFilename($log));
                $kettenrad->setup($options);
                $kettenrad->tick();

                if(($kettenrad->getState() != 'running'))
                {
                    $kettenrad->tick();
                }

                $ret_array  = $kettenrad->getStatusArray();
                $kettenrad->resetWarnings(); // So as not to have duplicate warnings reports
                \AliceCoreKettenrad::save($tag);

                break;

            case 'step':
                $tag = 'alice';

                $kettenrad = \AliceCoreKettenrad::load($tag);
                $kettenrad->tick();
                $ret_array  = $kettenrad->getStatusArray();
                $kettenrad->resetWarnings(); // So as not to have duplicate warnings reports
                \AliceCoreKettenrad::save($tag);

                if($ret_array['HasRun'] == 1)
                {
                    // Let's get tests result
                    $config   = \AliceFactory::getConfiguration();
                    $feedback = $config->get('volatile.alice.feedback');

                    $ret_array['Results'] = json_encode($feedback);

                    // Clean up
                    \AliceFactory::nuke();
                    \AliceUtilTempvars::reset($tag);
                }
                break;

            default:
                break;
        }

        return $ret_array;
    }
} 