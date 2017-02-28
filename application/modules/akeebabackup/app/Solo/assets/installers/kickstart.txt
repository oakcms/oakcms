<?php
/**
 * Akeeba Kickstart
 *
 * An archive extraction script for ZIP, JPA and JPS archives.
 *
 * @copyright   2008-2017 Nicholas K. Dionysopoulos / Akeeba Ltd.
 * @license     GNU GPL v2 or - at your option - any later version
 */

define('KICKSTART', 1);define('VERSION', '5.3.0');define('KICKSTARTPRO', '0');if (!defined('KSDEBUG') && isset($_SERVER) && isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'], 'local.web') !== false)){define('KSDEBUG', 1);}define('KSWINDOWS', substr(PHP_OS, 0, 3) == 'WIN');if (!defined('KSROOTDIR')){define('KSROOTDIR', dirname(__FILE__));}if (defined('KSDEBUG')){ini_set('error_log', KSROOTDIR . '/kickstart_error_log');if (file_exists(KSROOTDIR . '/kickstart_error_log')){@unlink(KSROOTDIR . '/kickstart_error_log');}error_reporting(E_ALL | E_STRICT);}else{@error_reporting(E_NONE);}if (!isset($_SERVER['REQUEST_URI'])){if (isset($_SERVER['HTTP_REQUEST_URI'])){$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_REQUEST_URI'];}else{if (isset($_SERVER['SCRIPT_NAME'])){$_SERVER['HTTP_REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];}else{$_SERVER['HTTP_REQUEST_URI'] = $_SERVER['PHP_SELF'];}if ($_SERVER['QUERY_STRING']){$_SERVER['HTTP_REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];}$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_REQUEST_URI'];}}$cacertpem = KSROOTDIR . '/cacert.pem';if (is_file($cacertpem)){if (is_readable($cacertpem)){define('AKEEBA_CACERT_PEM', $cacertpem);}}unset($cacertpem);$dh = @opendir(KSROOTDIR);if ($dh === false){return;}while ($filename = readdir($dh)){if (in_array($filename, array('.', '..'))){continue;}if (!is_file($filename)){continue;}if (substr($filename, 0, 10) != 'kickstart.'){continue;}if (substr($filename, -4) != '.php'){continue;}if ($filename == 'kickstart.php'){continue;}if (function_exists('opcache_invalidate')){opcache_invalidate($filename);}if (function_exists('apc_compile_file')){apc_compile_file($filename);}if (function_exists('wincache_refresh_if_changed')){wincache_refresh_if_changed(array($filename));}if (function_exists('xcache_asm')){xcache_asm($filename);}include_once $filename;}

define('_AKEEBA_RESTORATION', 1);defined('DS') or define('DS', DIRECTORY_SEPARATOR);define('AK_STATE_NOFILE', 0); define('AK_STATE_HEADER', 1); define('AK_STATE_DATA', 2); define('AK_STATE_DATAREAD', 3); define('AK_STATE_POSTPROC', 4); define('AK_STATE_DONE', 5); if (!defined('_AKEEBA_IS_WINDOWS')){if (function_exists('php_uname')){define('_AKEEBA_IS_WINDOWS', stristr(php_uname(), 'windows'));}else{define('_AKEEBA_IS_WINDOWS', DIRECTORY_SEPARATOR == '\\');}}if (!defined('KSROOTDIR')){define('KSROOTDIR', dirname(__FILE__));}if (!defined('KSLANGDIR')){define('KSLANGDIR', KSROOTDIR);}if (function_exists('setlocale')){@setlocale(LC_ALL, 'en_US.UTF8');}if (!function_exists('fnmatch')){function fnmatch($pattern, $string){return @preg_match('/^' . strtr(addcslashes($pattern, '/\\.+^$(){}=!<>|'),array('*' => '.*', '?' => '.?')) . '$/i', $string);}}if (!function_exists('akstringlen')){if (function_exists('mb_strlen')){function akstringlen($string){return mb_strlen($string, '8bit');}}else{function akstringlen($string){return strlen($string);}}}if (!function_exists('aksubstr')){if (function_exists('mb_strlen')){function aksubstr($string, $start, $length = null){return mb_substr($string, $start, $length, '8bit');}}else{function aksubstr($string, $start, $length = null){return substr($string, $start, $length);}}}function getQueryParam($key, $default = null){$value = $default;if (array_key_exists($key, $_REQUEST)){$value = $_REQUEST[$key];}if (get_magic_quotes_gpc() && !is_null($value)){$value = stripslashes($value);}return $value;}function debugMsg($msg){if (!defined('KSDEBUG')){return;}$fp = fopen('debug.txt', 'at');fwrite($fp, $msg . "\n");fclose($fp);if (defined('KSDEBUGCLI')){echo $msg . "\n";}}

abstract class AKAbstractObject{protected $_errors_queue_size = 0;protected $_warnings_queue_size = 0;private $_errors = array();private $_warnings = array();public function __construct(){}public function getError($i = null){return $this->getItemFromArray($this->_errors, $i);}private function getItemFromArray($array, $i = null){if ($i === null){$item = end($array);}else if (!array_key_exists($i, $array)){return false;}else{$item = $array[$i];}return $item;}public function getErrors(){return $this->_errors;}public function resetErrors(){$this->_errors = array();}public function getWarning($i = null){return $this->getItemFromArray($this->_warnings, $i);}public function getWarnings(){return $this->_warnings;}public function resetWarnings(){$this->_warnings = array();}public function propagateToObject(&$object){if (!is_object($object)){return;}if (method_exists($object, 'setError')){if (!empty($this->_errors)){foreach ($this->_errors as $error){$object->setError($error);}$this->_errors = array();}}if (method_exists($object, 'setWarning')){if (!empty($this->_warnings)){foreach ($this->_warnings as $warning){$object->setWarning($warning);}$this->_warnings = array();}}}public function propagateFromObject(&$object){if (method_exists($object, 'getErrors')){$errors = $object->getErrors();if (!empty($errors)){foreach ($errors as $error){$this->setError($error);}}if (method_exists($object, 'resetErrors')){$object->resetErrors();}}if (method_exists($object, 'getWarnings')){$warnings = $object->getWarnings();if (!empty($warnings)){foreach ($warnings as $warning){$this->setWarning($warning);}}if (method_exists($object, 'resetWarnings')){$object->resetWarnings();}}}public function setError($error){if ($this->_errors_queue_size > 0){if (count($this->_errors) >= $this->_errors_queue_size){array_shift($this->_errors);}}array_push($this->_errors, $error);}public function setWarning($warning){if ($this->_warnings_queue_size > 0){if (count($this->_warnings) >= $this->_warnings_queue_size){array_shift($this->_warnings);}}array_push($this->_warnings, $warning);}protected function setErrorsQueueSize($newSize = 0){$this->_errors_queue_size = (int) $newSize;}protected function setWarningsQueueSize($newSize = 0){$this->_warnings_queue_size = (int) $newSize;}}

abstract class AKAbstractPart extends AKAbstractObject{protected $isPrepared = false;protected $isRunning = false;protected $isFinished = false;protected $hasRan = false;protected $active_domain = "";protected $active_step = "";protected $active_substep = "";protected $_parametersArray = array();protected $databaseRoot = array();protected $observers = array();private $warnings_pointer = -1;final public function tick(){switch ($this->getState()){case "init":$this->_prepare();break;case "prepared":$this->_run();break;case "running":$this->_run();break;case "postrun":$this->_finalize();break;}$out = $this->_makeReturnTable();return $out;}final public function getState(){if ($this->getError()){return "error";}if (!($this->isPrepared)){return "init";}if (!($this->isFinished) && !($this->isRunning) && !($this->hasRun) && ($this->isPrepared)){return "prepared";}if (!($this->isFinished) && $this->isRunning && !($this->hasRun)){return "running";}if (!($this->isFinished) && !($this->isRunning) && $this->hasRun){return "postrun";}if ($this->isFinished){return "finished";}}abstract protected function _prepare();abstract protected function _run();abstract protected function _finalize();final protected function _makeReturnTable(){$warnings = $this->getWarnings();if ($this->_warnings_queue_size == 0){if (($this->warnings_pointer > 0) && ($this->warnings_pointer < (count($warnings)))){$warnings = array_slice($warnings, $this->warnings_pointer + 1);$this->warnings_pointer += count($warnings);}else{$this->warnings_pointer = count($warnings);}}$out = array('HasRun'   => (!($this->isFinished)),'Domain'   => $this->active_domain,'Step'     => $this->active_step,'Substep'  => $this->active_substep,'Error'    => $this->getError(),'Warnings' => $warnings);return $out;}public function getStatusArray(){return $this->_makeReturnTable();}final public function setup($parametersArray){if ($this->isPrepared){$this->setState('error', "Can't modify configuration after the preparation of " . $this->active_domain);}else{$this->_parametersArray = $parametersArray;if (array_key_exists('root', $parametersArray)){$this->databaseRoot = $parametersArray['root'];}}}protected function setState($state = 'init', $errorMessage = 'Invalid setState argument'){switch ($state){case 'init':$this->isPrepared = false;$this->isRunning  = false;$this->isFinished = false;$this->hasRun     = false;break;case 'prepared':$this->isPrepared = true;$this->isRunning  = false;$this->isFinished = false;$this->hasRun     = false;break;case 'running':$this->isPrepared = true;$this->isRunning  = true;$this->isFinished = false;$this->hasRun     = false;break;case 'postrun':$this->isPrepared = true;$this->isRunning  = false;$this->isFinished = false;$this->hasRun     = true;break;case 'finished':$this->isPrepared = true;$this->isRunning  = false;$this->isFinished = true;$this->hasRun     = false;break;case 'error':default:$this->setError($errorMessage);break;}}final public function getDomain(){return $this->active_domain;}final public function getStep(){return $this->active_step;}final public function getSubstep(){return $this->active_substep;}function attach(AKAbstractPartObserver $obs){$this->observers["$obs"] = $obs;}function detach(AKAbstractPartObserver $obs){delete($this->observers["$obs"]);}protected function setBreakFlag(){AKFactory::set('volatile.breakflag', true);}final protected function setDomain($new_domain){$this->active_domain = $new_domain;}final protected function setStep($new_step){$this->active_step = $new_step;}final protected function setSubstep($new_substep){$this->active_substep = $new_substep;}protected function notify($message){foreach ($this->observers as $obs){$obs->update($this, $message);}}}

abstract class AKAbstractUnarchiver extends AKAbstractPart{public $archiveList = array();public $totalSize = array();public $renameFiles = array();public $renameDirs = array();public $skipFiles = array();protected $filename = null;protected $currentPartNumber = -1;protected $currentPartOffset = 0;protected $flagRestorePermissions = false;protected $postProcEngine = null;protected $addPath = '';protected $removePath = '';protected $chunkSize = 524288;protected $fp = null;protected $runState = null;protected $fileHeader = null;protected $dataReadLength = 0;protected $ignoreDirectories = array();public function __construct(){parent::__construct();}public function __wakeup(){if ($this->currentPartNumber >= 0){$this->fp = @fopen($this->archiveList[$this->currentPartNumber], 'rb');if ((is_resource($this->fp)) && ($this->currentPartOffset > 0)){@fseek($this->fp, $this->currentPartOffset);}}}public function shutdown(){if (is_resource($this->fp)){$this->currentPartOffset = @ftell($this->fp);@fclose($this->fp);}}public function isIgnoredDirectory($shortFilename){if (substr($shortFilename, -1) == '/'){$check = rtrim($shortFilename, '/');}else{$check = dirname($shortFilename);}return in_array($check, $this->ignoreDirectories);}final protected function _prepare(){parent::__construct();if (count($this->_parametersArray) > 0){foreach ($this->_parametersArray as $key => $value){switch ($key){case 'filename':$this->filename = $value;if (!empty($value)){$value = strtolower($value);if (strlen($value) > 6){if ((substr($value, 0, 7) == 'http://')|| (substr($value, 0, 8) == 'https://')|| (substr($value, 0, 6) == 'ftp://')|| (substr($value, 0, 7) == 'ssh2://')|| (substr($value, 0, 6) == 'ssl://')){$this->setState('error', 'Invalid archive location');}}}break;case 'restore_permissions':$this->flagRestorePermissions = $value;break;case 'post_proc':$this->postProcEngine = AKFactory::getpostProc($value);break;case 'add_path':$this->addPath = $value;$this->addPath = str_replace('\\', '/', $this->addPath);$this->addPath = rtrim($this->addPath, '/');if (!empty($this->addPath)){$this->addPath .= '/';}break;case 'remove_path':$this->removePath = $value;$this->removePath = str_replace('\\', '/', $this->removePath);$this->removePath = rtrim($this->removePath, '/');if (!empty($this->removePath)){$this->removePath .= '/';}break;case 'rename_files':$this->renameFiles = $value;break;case 'rename_dirs':$this->renameDirs = $value;break;case 'skip_files':$this->skipFiles = $value;break;case 'ignoredirectories':$this->ignoreDirectories = $value;break;}}}$this->scanArchives();$this->readArchiveHeader();$errMessage = $this->getError();if (!empty($errMessage)){$this->setState('error', $errMessage);}else{$this->runState = AK_STATE_NOFILE;$this->setState('prepared');}}private function scanArchives(){if (defined('KSDEBUG')){@unlink('debug.txt');}debugMsg('Preparing to scan archives');$privateArchiveList = array();$dirname         = dirname($this->filename);$base_extension  = $this->getBaseExtension();$basename        = basename($this->filename, $base_extension);$this->totalSize = 0;$count             = 0;$found             = true;$this->archiveList = array();while ($found){++$count;$extension = substr($base_extension, 0, 2) . sprintf('%02d', $count);$filename  = $dirname . DIRECTORY_SEPARATOR . $basename . $extension;$found     = file_exists($filename);if ($found){debugMsg('- Found archive ' . $filename);$this->archiveList[] = $filename;$filesize = @filesize($filename);$this->totalSize += $filesize;$privateArchiveList[] = array($filename, $filesize);}else{debugMsg('- Found archive ' . $this->filename);$this->archiveList[] = $this->filename;$filename = $this->filename;$filesize = @filesize($filename);$this->totalSize += $filesize;$privateArchiveList[] = array($filename, $filesize);}}debugMsg('Total archive parts: ' . $count);$this->currentPartNumber = -1;$this->currentPartOffset = 0;$this->runState          = AK_STATE_NOFILE;$message                     = new stdClass;$message->type               = 'totalsize';$message->content            = new stdClass;$message->content->totalsize = $this->totalSize;$message->content->filelist  = $privateArchiveList;$this->notify($message);}private function getBaseExtension(){static $baseextension;if (empty($baseextension)){$basename      = basename($this->filename);$lastdot       = strrpos($basename, '.');$baseextension = substr($basename, $lastdot);}return $baseextension;}protected abstract function readArchiveHeader();protected function _run(){if ($this->getState() == 'postrun'){return;}$this->setState('running');$timer = AKFactory::getTimer();$status = true;while ($status && ($timer->getTimeLeft() > 0)){switch ($this->runState){case AK_STATE_NOFILE:debugMsg(__CLASS__ . '::_run() - Reading file header');$status = $this->readFileHeader();if ($status){$message          = new stdClass;$message->type    = 'startfile';$message->content = new stdClass;if (array_key_exists('realfile', get_object_vars($this->fileHeader))){$message->content->realfile = $this->fileHeader->realFile;}else{$message->content->realfile = $this->fileHeader->file;}$message->content->file = $this->fileHeader->file;if (array_key_exists('compressed', get_object_vars($this->fileHeader))){$message->content->compressed = $this->fileHeader->compressed;}else{$message->content->compressed = 0;}$message->content->uncompressed = $this->fileHeader->uncompressed;debugMsg(__CLASS__ . '::_run() - Preparing to extract ' . $message->content->realfile);$this->notify($message);}else{debugMsg(__CLASS__ . '::_run() - Could not read file header');}break;case AK_STATE_HEADER:case AK_STATE_DATA:debugMsg(__CLASS__ . '::_run() - Processing file data');$status = $this->processFileData();break;case AK_STATE_DATAREAD:case AK_STATE_POSTPROC:debugMsg(__CLASS__ . '::_run() - Calling post-processing class');$this->postProcEngine->timestamp = $this->fileHeader->timestamp;$status                          = $this->postProcEngine->process();$this->propagateFromObject($this->postProcEngine);$this->runState = AK_STATE_DONE;break;case AK_STATE_DONE:default:if ($status){debugMsg(__CLASS__ . '::_run() - Finished extracting file');$message          = new stdClass;$message->type    = 'endfile';$message->content = new stdClass;if (array_key_exists('realfile', get_object_vars($this->fileHeader))){$message->content->realfile = $this->fileHeader->realFile;}else{$message->content->realfile = $this->fileHeader->file;}$message->content->file = $this->fileHeader->file;if (array_key_exists('compressed', get_object_vars($this->fileHeader))){$message->content->compressed = $this->fileHeader->compressed;}else{$message->content->compressed = 0;}$message->content->uncompressed = $this->fileHeader->uncompressed;$this->notify($message);}$this->runState = AK_STATE_NOFILE;continue;}}$error = $this->getError();if (!$status && ($this->runState == AK_STATE_NOFILE) && empty($error)){debugMsg(__CLASS__ . '::_run() - Just finished');$this->setState('postrun');}elseif (!empty($error)){debugMsg(__CLASS__ . '::_run() - Halted with an error:');debugMsg($error);$this->setState('error', $error);}}protected abstract function readFileHeader();protected abstract function processFileData();protected function _finalize(){$this->setState('finished');}protected function nextFile(){debugMsg('Current part is ' . $this->currentPartNumber . '; opening the next part');++$this->currentPartNumber;if ($this->currentPartNumber > (count($this->archiveList) - 1)){$this->setState('postrun');return false;}else{if (is_resource($this->fp)){@fclose($this->fp);}debugMsg('Opening file ' . $this->archiveList[$this->currentPartNumber]);$this->fp = @fopen($this->archiveList[$this->currentPartNumber], 'rb');if ($this->fp === false){debugMsg('Could not open file - crash imminent');$this->setError(AKText::sprintf('ERR_COULD_NOT_OPEN_ARCHIVE_PART', $this->archiveList[$this->currentPartNumber]));}fseek($this->fp, 0);$this->currentPartOffset = 0;return true;}}protected function isEOF($local = false){$eof = @feof($this->fp);if (!$eof){$position = @ftell($this->fp);$filesize = @filesize($this->archiveList[$this->currentPartNumber]);if ($filesize <= 0){$eof = false;}elseif ($position >= $filesize){$eof = true;}}if ($local){return $eof;}else{return $eof && ($this->currentPartNumber >= (count($this->archiveList) - 1));}}protected function setCorrectPermissions($path){static $rootDir = null;if (is_null($rootDir)){$rootDir = rtrim(AKFactory::get('kickstart.setup.destdir', ''), '/\\');}$directory = rtrim(dirname($path), '/\\');if ($directory != $rootDir){if (!is_writeable($directory)){$this->postProcEngine->chmod($directory, 0755);}}$this->postProcEngine->chmod($path, 0644);}protected function fread($fp, $length = null){if (is_numeric($length)){if ($length > 0){$data = fread($fp, $length);}else{$data = fread($fp, PHP_INT_MAX);}}else{$data = fread($fp, PHP_INT_MAX);}if ($data === false){$data = '';}$message                  = new stdClass;$message->type            = 'reading';$message->content         = new stdClass;$message->content->length = strlen($data);$this->notify($message);return $data;}protected function removePath($path){if (empty($this->removePath)){return $path;}if (strpos($path, $this->removePath) === 0){$path = substr($path, strlen($this->removePath));$path = ltrim($path, '/\\');}return $path;}}

abstract class AKAbstractPostproc extends AKAbstractObject{public $timestamp = 0;protected $filename = null;protected $perms = 0755;protected $tempFilename = null;abstract public function process();abstract public function processFilename($filename, $perms = 0755);abstract public function createDirRecursive($dirName, $perms);abstract public function chmod($file, $perms);abstract public function unlink($file);abstract public function rmdir($directory);abstract public function rename($from, $to);}

abstract class AKAbstractPartObserver{abstract public function update($object, $message);}

class AKPostprocDirect extends AKAbstractPostproc{public function process(){$restorePerms = AKFactory::get('kickstart.setup.restoreperms', false);if ($restorePerms){@chmod($this->filename, $this->perms);}else{if (@is_file($this->filename)){@chmod($this->filename, 0644);}else{@chmod($this->filename, 0755);}}if ($this->timestamp > 0){@touch($this->filename, $this->timestamp);}return true;}public function processFilename($filename, $perms = 0755){$this->perms    = $perms;$this->filename = $filename;return $filename;}public function createDirRecursive($dirName, $perms){if (AKFactory::get('kickstart.setup.dryrun', '0')){return true;}if (@mkdir($dirName, 0755, true)){@chmod($dirName, 0755);return true;}$root = AKFactory::get('kickstart.setup.destdir');$root = rtrim(str_replace('\\', '/', $root), '/');$dir  = rtrim(str_replace('\\', '/', $dirName), '/');if (strpos($dir, $root) === 0){$dir = ltrim(substr($dir, strlen($root)), '/');$root .= '/';}else{$root = '';}if (empty($dir)){return true;}$dirArray = explode('/', $dir);$path     = '';foreach ($dirArray as $dir){$path .= $dir . '/';$ret = is_dir($root . $path) ? true : @mkdir($root . $path);if (!$ret){if (is_file($root . $path)){@unlink($root . $path);$ret = @mkdir($root . $path);}if (!$ret){$this->setError(AKText::sprintf('COULDNT_CREATE_DIR', $path));return false;}}@chmod($root . $path, $perms);}return true;}public function chmod($file, $perms){if (AKFactory::get('kickstart.setup.dryrun', '0')){return true;}return @chmod($file, $perms);}public function unlink($file){return @unlink($file);}public function rmdir($directory){return @rmdir($directory);}public function rename($from, $to){return @rename($from, $to);}}

class AKPostprocFTP extends AKAbstractPostproc{public $useSSL = false;public $passive = true;public $host = '';public $port = 21;public $user = '';public $pass = '';public $dir = '';private $handle = null;private $tempDir = '';public function __construct(){parent::__construct();$this->useSSL  = AKFactory::get('kickstart.ftp.ssl', false);$this->passive = AKFactory::get('kickstart.ftp.passive', true);$this->host    = AKFactory::get('kickstart.ftp.host', '');$this->port    = AKFactory::get('kickstart.ftp.port', 21);if (trim($this->port) == ''){$this->port = 21;}$this->user    = AKFactory::get('kickstart.ftp.user', '');$this->pass    = AKFactory::get('kickstart.ftp.pass', '');$this->dir     = AKFactory::get('kickstart.ftp.dir', '');$this->tempDir = AKFactory::get('kickstart.ftp.tempdir', '');$connected = $this->connect();if ($connected){if (!empty($this->tempDir)){$tempDir  = rtrim($this->tempDir, '/\\') . '/';$writable = $this->isDirWritable($tempDir);}else{$tempDir  = '';$writable = false;}if (!$writable){$tempDir = KSROOTDIR;if (empty($tempDir)){$tempDir = '.';}$absoluteDirToHere = $tempDir;$tempDir           = rtrim(str_replace('\\', '/', $tempDir), '/');if (!empty($tempDir)){$tempDir .= '/';}$this->tempDir = $tempDir;$writable = $this->isDirWritable($tempDir);}if (!$writable){$tempDir                 = $absoluteDirToHere . '/kicktemp';$trustMeIKnowWhatImDoing = 500 + 10 + 1; $this->createDirRecursive($tempDir, $trustMeIKnowWhatImDoing);$this->fixPermissions($tempDir);$writable = $this->isDirWritable($tempDir);}if (!$writable){$userdir = AKFactory::get('kickstart.ftp.tempdir', '');if (!empty($userdir)){$absolute = false;$absolute = $absolute || (substr($userdir, 0, 1) == '/');$absolute = $absolute || (substr($userdir, 1, 1) == ':');$absolute = $absolute || (substr($userdir, 2, 1) == ':');if (!$absolute){$tempDir = $absoluteDirToHere . $userdir;}else{$tempDir = $userdir;}if (is_dir($tempDir)){$writable = $this->isDirWritable($tempDir);}}}$this->tempDir = $tempDir;if (!$writable){$this->setError(AKText::_('FTP_TEMPDIR_NOT_WRITABLE'));}else{AKFactory::set('kickstart.ftp.tempdir', $tempDir);$this->tempDir = $tempDir;}}}public function connect(){if ($this->useSSL){$this->handle = @ftp_ssl_connect($this->host, $this->port);}else{$this->handle = @ftp_connect($this->host, $this->port);}if ($this->handle === false){$this->setError(AKText::_('WRONG_FTP_HOST'));return false;}if (!@ftp_login($this->handle, $this->user, $this->pass)){$this->setError(AKText::_('WRONG_FTP_USER'));@ftp_close($this->handle);return false;}if (!@ftp_chdir($this->handle, $this->dir)){$this->setError(AKText::_('WRONG_FTP_PATH1'));@ftp_close($this->handle);return false;}if ($this->passive){@ftp_pasv($this->handle, true);}else{@ftp_pasv($this->handle, false);}$testFilename = defined('KSSELFNAME') ? KSSELFNAME : basename(__FILE__);$tempHandle   = fopen('php://temp', 'r+');if (@ftp_fget($this->handle, $tempHandle, $testFilename, FTP_ASCII, 0) === false){$this->setError(AKText::_('WRONG_FTP_PATH2'));@ftp_close($this->handle);fclose($tempHandle);return false;}fclose($tempHandle);return true;}private function isDirWritable($dir){$fp = @fopen($dir . '/kickstart.dat', 'wb');if ($fp === false){return false;}else{@fclose($fp);unlink($dir . '/kickstart.dat');return true;}}public function createDirRecursive($dirName, $perms){$removePath = AKFactory::get('kickstart.setup.destdir', '');if (!empty($removePath)){$removePath = str_replace('\\', '/', $removePath);$dirName    = str_replace('\\', '/', $dirName);$removePath = rtrim($removePath, '/\\') . '/';$dirName    = rtrim($dirName, '/\\') . '/';$left = substr($dirName, 0, strlen($removePath));if ($left == $removePath){$dirName = substr($dirName, strlen($removePath));}}if (empty($dirName)){$dirName = '';} $check = '/' . trim($this->dir, '/') . '/' . trim($dirName, '/');if ($this->is_dir($check)){return true;}$alldirs     = explode('/', $dirName);$previousDir = '/' . trim($this->dir);foreach ($alldirs as $curdir){$check = $previousDir . '/' . $curdir;if (!$this->is_dir($check)){@ftp_delete($this->handle, $check);if (@ftp_mkdir($this->handle, $check) === false){$this->fixPermissions($removePath . $check);if (@ftp_mkdir($this->handle, $check) === false){if (!@mkdir($check)){$this->setError(AKText::sprintf('FTP_CANT_CREATE_DIR', $check));return false;}else{$trustMeIKnowWhatImDoing =500 + 10 + 1; @chmod($check, $trustMeIKnowWhatImDoing);return true;}}}@ftp_chmod($this->handle, $perms, $check);}$previousDir = $check;}return true;}private function is_dir($dir){return @ftp_chdir($this->handle, $dir);}private function fixPermissions($path){if (!defined('KSDEBUG')){$oldErrorReporting = @error_reporting(E_NONE);}$relPath  = str_replace('\\', '/', $path);$basePath = rtrim(str_replace('\\', '/', KSROOTDIR), '/');$basePath = rtrim($basePath, '/');if (!empty($basePath)){$basePath .= '/';}if (substr($relPath, 0, strlen($basePath)) == $basePath){$relPath = substr($relPath, strlen($basePath));}$dirArray  = explode('/', $relPath);$pathBuilt = rtrim($basePath, '/');foreach ($dirArray as $dir){if (empty($dir)){continue;}$oldPath = $pathBuilt;$pathBuilt .= '/' . $dir;if (is_dir($oldPath . $dir)){$trustMeIKnowWhatImDoing = 500 + 10 + 1; @chmod($oldPath . $dir, $trustMeIKnowWhatImDoing);}else{$trustMeIKnowWhatImDoing = 500 + 10 + 1; if (@chmod($oldPath . $dir, $trustMeIKnowWhatImDoing) === false){@unlink($oldPath . $dir);}}}if (!defined('KSDEBUG')){@error_reporting($oldErrorReporting);}}function __wakeup(){$this->connect();}public function process(){if (is_null($this->tempFilename)){return true;}$remotePath = dirname($this->filename);$removePath = AKFactory::get('kickstart.setup.destdir', '');if (!empty($removePath)){$removePath = ltrim($removePath, "/");$remotePath = ltrim($remotePath, "/");$left       = substr($remotePath, 0, strlen($removePath));if ($left == $removePath){$remotePath = substr($remotePath, strlen($removePath));}}$absoluteFSPath  = dirname($this->filename);$relativeFTPPath = trim($remotePath, '/');$absoluteFTPPath = '/' . trim($this->dir, '/') . '/' . trim($remotePath, '/');$onlyFilename    = basename($this->filename);$remoteName = $absoluteFTPPath . '/' . $onlyFilename;$ret = @ftp_chdir($this->handle, $absoluteFTPPath);if ($ret === false){$ret = $this->createDirRecursive($absoluteFSPath, 0755);if ($ret === false){$this->setError(AKText::sprintf('FTP_COULDNT_UPLOAD', $this->filename));return false;}$ret = @ftp_chdir($this->handle, $absoluteFTPPath);if ($ret === false){$this->setError(AKText::sprintf('FTP_COULDNT_UPLOAD', $this->filename));return false;}}$ret = @ftp_put($this->handle, $remoteName, $this->tempFilename, FTP_BINARY);if ($ret === false){$this->fixPermissions($this->filename);$this->unlink($this->filename);$fp = @fopen($this->tempFilename, 'rb');if ($fp !== false){$ret = @ftp_fput($this->handle, $remoteName, $fp, FTP_BINARY);@fclose($fp);}else{$ret = false;}}@unlink($this->tempFilename);if ($ret === false){$this->setError(AKText::sprintf('FTP_COULDNT_UPLOAD', $this->filename));return false;}$restorePerms = AKFactory::get('kickstart.setup.restoreperms', false);if ($restorePerms){@ftp_chmod($this->_handle, $this->perms, $remoteName);}else{@ftp_chmod($this->_handle, 0644, $remoteName);}return true;}public function unlink($file){$removePath = AKFactory::get('kickstart.setup.destdir', '');if (!empty($removePath)){$left = substr($file, 0, strlen($removePath));if ($left == $removePath){$file = substr($file, strlen($removePath));}}$check = '/' . trim($this->dir, '/') . '/' . trim($file, '/');return @ftp_delete($this->handle, $check);}public function processFilename($filename, $perms = 0755){if ($this->getError()){return false;}if (is_null($filename)){$this->filename     = null;$this->tempFilename = null;return null;}$removePath = AKFactory::get('kickstart.setup.destdir', '');if (!empty($removePath)){$left = substr($filename, 0, strlen($removePath));if ($left == $removePath){$filename = substr($filename, strlen($removePath));}}$filename = ltrim($filename, '/');$this->filename     = $filename;$this->tempFilename = tempnam($this->tempDir, 'kickstart-');$this->perms        = $perms;if (empty($this->tempFilename)){$this->tempFilename = $this->tempDir . '/kickstart-' . time() . '.dat';}return $this->tempFilename;}public function close(){@ftp_close($this->handle);}public function chmod($file, $perms){return @ftp_chmod($this->handle, $perms, $file);}public function rmdir($directory){$removePath = AKFactory::get('kickstart.setup.destdir', '');if (!empty($removePath)){$left = substr($directory, 0, strlen($removePath));if ($left == $removePath){$directory = substr($directory, strlen($removePath));}}$check = '/' . trim($this->dir, '/') . '/' . trim($directory, '/');return @ftp_rmdir($this->handle, $check);}public function rename($from, $to){$originalFrom = $from;$originalTo   = $to;$removePath = AKFactory::get('kickstart.setup.destdir', '');if (!empty($removePath)){$left = substr($from, 0, strlen($removePath));if ($left == $removePath){$from = substr($from, strlen($removePath));}}$from = '/' . trim($this->dir, '/') . '/' . trim($from, '/');if (!empty($removePath)){$left = substr($to, 0, strlen($removePath));if ($left == $removePath){$to = substr($to, strlen($removePath));}}$to = '/' . trim($this->dir, '/') . '/' . trim($to, '/');$result = @ftp_rename($this->handle, $from, $to);if ($result !== true){return @rename($from, $to);}else{return true;}}}

class AKPostprocSFTP extends AKAbstractPostproc{public $useSSL = false;public $passive = true;public $host = '';public $port = 21;public $user = '';public $pass = '';public $dir = '';private $handle = null;private $_connection = null;private $_currentdir;private $tempDir = '';public function __construct(){parent::__construct();$this->host = AKFactory::get('kickstart.ftp.host', '');$this->port = AKFactory::get('kickstart.ftp.port', 22);if (trim($this->port) == ''){$this->port = 22;}$this->user    = AKFactory::get('kickstart.ftp.user', '');$this->pass    = AKFactory::get('kickstart.ftp.pass', '');$this->dir     = AKFactory::get('kickstart.ftp.dir', '');$this->tempDir = AKFactory::get('kickstart.ftp.tempdir', '');$connected = $this->connect();if ($connected){if (!empty($this->tempDir)){$tempDir  = rtrim($this->tempDir, '/\\') . '/';$writable = $this->isDirWritable($tempDir);}else{$tempDir  = '';$writable = false;}if (!$writable){$tempDir = KSROOTDIR;if (empty($tempDir)){$tempDir = '.';}$absoluteDirToHere = $tempDir;$tempDir           = rtrim(str_replace('\\', '/', $tempDir), '/');if (!empty($tempDir)){$tempDir .= '/';}$this->tempDir = $tempDir;$writable = $this->isDirWritable($tempDir);}if (!$writable){$tempDir                 = $absoluteDirToHere . '/kicktemp';$trustMeIKnowWhatImDoing = 500 + 10 + 1; $this->createDirRecursive($tempDir, $trustMeIKnowWhatImDoing);$this->fixPermissions($tempDir);$writable = $this->isDirWritable($tempDir);}if (!$writable){$userdir = AKFactory::get('kickstart.ftp.tempdir', '');if (!empty($userdir)){$absolute = false;$absolute = $absolute || (substr($userdir, 0, 1) == '/');$absolute = $absolute || (substr($userdir, 1, 1) == ':');$absolute = $absolute || (substr($userdir, 2, 1) == ':');if (!$absolute){$tempDir = $absoluteDirToHere . $userdir;}else{$tempDir = $userdir;}if (is_dir($tempDir)){$writable = $this->isDirWritable($tempDir);}}}$this->tempDir = $tempDir;if (!$writable){$this->setError(AKText::_('SFTP_TEMPDIR_NOT_WRITABLE'));}else{AKFactory::set('kickstart.ftp.tempdir', $tempDir);$this->tempDir = $tempDir;}}}public function connect(){$this->_connection = false;if (!function_exists('ssh2_connect')){$this->setError(AKText::_('SFTP_NO_SSH2'));return false;}$this->_connection = @ssh2_connect($this->host, $this->port);if (!@ssh2_auth_password($this->_connection, $this->user, $this->pass)){$this->setError(AKText::_('SFTP_WRONG_USER'));$this->_connection = false;return false;}$this->handle = @ssh2_sftp($this->_connection);if (!$this->dir){$this->setError(AKText::_('SFTP_WRONG_STARTING_DIR'));return false;}if (!$this->sftp_chdir('/')){$this->setError(AKText::_('SFTP_WRONG_STARTING_DIR'));unset($this->_connection);unset($this->handle);return false;}$testFilename = defined('KSSELFNAME') ? KSSELFNAME : basename(__FILE__);$basePath     = '/' . trim($this->dir, '/');if (@fopen("ssh2.sftp://{$this->handle}$basePath/$testFilename", 'r+') === false){$this->setError(AKText::_('SFTP_WRONG_STARTING_DIR'));unset($this->_connection);unset($this->handle);return false;}return true;}private function sftp_chdir($dir){$removePath = AKFactory::get('kickstart.setup.destdir', '');if (!empty($removePath)){$removePath = str_replace('\\', '/', $removePath);$dir        = str_replace('\\', '/', $dir);$removePath = rtrim($removePath, '/\\') . '/';$dir        = rtrim($dir, '/\\') . '/';$left = substr($dir, 0, strlen($removePath));if ($left == $removePath){$dir = substr($dir, strlen($removePath));}}if (empty($dir)){$dir = '';}$realdir = substr($this->dir, -1) == '/' ? substr($this->dir, 0, strlen($this->dir) - 1) : $this->dir;$realdir .= '/' . $dir;$realdir = substr($realdir, 0, 1) == '/' ? $realdir : '/' . $realdir;if ($this->_currentdir == $realdir){return true;}$result = @ssh2_sftp_stat($this->handle, $realdir);if ($result === false){return false;}else{$this->_currentdir = $realdir;return true;}}private function isDirWritable($dir){if (@fopen("ssh2.sftp://{$this->handle}$dir/kickstart.dat", 'wb') === false){return false;}else{@ssh2_sftp_unlink($this->handle, $dir . '/kickstart.dat');return true;}}public function createDirRecursive($dirName, $perms){$removePath = AKFactory::get('kickstart.setup.destdir', '');if (!empty($removePath)){$removePath = str_replace('\\', '/', $removePath);$dirName    = str_replace('\\', '/', $dirName);$removePath = rtrim($removePath, '/\\') . '/';$dirName    = rtrim($dirName, '/\\') . '/';$left = substr($dirName, 0, strlen($removePath));if ($left == $removePath){$dirName = substr($dirName, strlen($removePath));}}if (empty($dirName)){$dirName = '';} $check = '/' . trim($this->dir, '/ ') . '/' . trim($dirName, '/');if ($this->is_dir($check)){return true;}$alldirs     = explode('/', $dirName);$previousDir = '/' . trim($this->dir, '/ ');foreach ($alldirs as $curdir){if (!$curdir){continue;}$check = $previousDir . '/' . $curdir;if (!$this->is_dir($check)){@ssh2_sftp_unlink($this->handle, $check);if (@ssh2_sftp_mkdir($this->handle, $check) === false){$this->fixPermissions($check);if (@ssh2_sftp_mkdir($this->handle, $check) === false){if (!@mkdir($check)){$this->setError(AKText::sprintf('FTP_CANT_CREATE_DIR', $check));return false;}else{$trustMeIKnowWhatImDoing =500 + 10 + 1; @chmod($check, $trustMeIKnowWhatImDoing);return true;}}}@ssh2_sftp_chmod($this->handle, $check, $perms);}$previousDir = $check;}return true;}private function is_dir($dir){return $this->sftp_chdir($dir);}private function fixPermissions($path){if (!defined('KSDEBUG')){$oldErrorReporting = @error_reporting(E_NONE);}$relPath  = str_replace('\\', '/', $path);$basePath = rtrim(str_replace('\\', '/', KSROOTDIR), '/');$basePath = rtrim($basePath, '/');if (!empty($basePath)){$basePath .= '/';}if (substr($relPath, 0, strlen($basePath)) == $basePath){$relPath = substr($relPath, strlen($basePath));}$dirArray  = explode('/', $relPath);$pathBuilt = rtrim($basePath, '/');foreach ($dirArray as $dir){if (empty($dir)){continue;}$oldPath = $pathBuilt;$pathBuilt .= '/' . $dir;if (is_dir($oldPath . '/' . $dir)){$trustMeIKnowWhatImDoing = 500 + 10 + 1; @chmod($oldPath . '/' . $dir, $trustMeIKnowWhatImDoing);}else{$trustMeIKnowWhatImDoing = 500 + 10 + 1; if (@chmod($oldPath . '/' . $dir, $trustMeIKnowWhatImDoing) === false){@unlink($oldPath . $dir);}}}if (!defined('KSDEBUG')){@error_reporting($oldErrorReporting);}}function __wakeup(){$this->connect();}public function process(){if (is_null($this->tempFilename)){return true;}$remotePath      = dirname($this->filename);$absoluteFSPath  = dirname($this->filename);$absoluteFTPPath = '/' . trim($this->dir, '/') . '/' . trim($remotePath, '/');$onlyFilename    = basename($this->filename);$remoteName = $absoluteFTPPath . '/' . $onlyFilename;$ret = $this->sftp_chdir($absoluteFTPPath);if ($ret === false){$ret = $this->createDirRecursive($absoluteFSPath, 0755);if ($ret === false){$this->setError(AKText::sprintf('SFTP_COULDNT_UPLOAD', $this->filename));return false;}$ret = $this->sftp_chdir($absoluteFTPPath);if ($ret === false){$this->setError(AKText::sprintf('SFTP_COULDNT_UPLOAD', $this->filename));return false;}}$ret = $this->write($this->tempFilename, $remoteName);if ($ret === -1){$this->setError(AKText::sprintf('SFTP_COULDNT_UPLOAD', $this->filename));return false;}if ($ret === false){$this->fixPermissions($this->filename);$this->unlink($this->filename);$ret = $this->write($this->tempFilename, $remoteName);}@unlink($this->tempFilename);if ($ret === false){$this->setError(AKText::sprintf('SFTP_COULDNT_UPLOAD', $this->filename));return false;}$restorePerms = AKFactory::get('kickstart.setup.restoreperms', false);if ($restorePerms){$this->chmod($remoteName, $this->perms);}else{$this->chmod($remoteName, 0644);}return true;}private function write($local, $remote){$fp      = @fopen("ssh2.sftp://{$this->handle}$remote", 'w');$localfp = @fopen($local, 'rb');if ($fp === false){return -1;}if ($localfp === false){@fclose($fp);return -1;}$res = true;while (!feof($localfp) && ($res !== false)){$buffer = @fread($localfp, 65567);$res    = @fwrite($fp, $buffer);}@fclose($fp);@fclose($localfp);return $res;}public function unlink($file){$check = '/' . trim($this->dir, '/') . '/' . trim($file, '/');return @ssh2_sftp_unlink($this->handle, $check);}public function chmod($file, $perms){return @ssh2_sftp_chmod($this->handle, $file, $perms);}public function processFilename($filename, $perms = 0755){if ($this->getError()){return false;}if (is_null($filename)){$this->filename     = null;$this->tempFilename = null;return null;}$removePath = AKFactory::get('kickstart.setup.destdir', '');if (!empty($removePath)){$left = substr($filename, 0, strlen($removePath));if ($left == $removePath){$filename = substr($filename, strlen($removePath));}}$filename = ltrim($filename, '/');$this->filename     = $filename;$this->tempFilename = tempnam($this->tempDir, 'kickstart-');$this->perms        = $perms;if (empty($this->tempFilename)){$this->tempFilename = $this->tempDir . '/kickstart-' . time() . '.dat';}return $this->tempFilename;}public function close(){unset($this->_connection);unset($this->handle);}public function rmdir($directory){$check = '/' . trim($this->dir, '/') . '/' . trim($directory, '/');return @ssh2_sftp_rmdir($this->handle, $check);}public function rename($from, $to){$from = '/' . trim($this->dir, '/') . '/' . trim($from, '/');$to   = '/' . trim($this->dir, '/') . '/' . trim($to, '/');$result = @ssh2_sftp_rename($this->handle, $from, $to);if ($result !== true){return @rename($from, $to);}else{return true;}}}

class AKPostprocHybrid extends AKAbstractPostproc{public $useFTP = false;public $useSSL = false;public $passive = true;public $host = '';public $port = 21;public $user = '';public $pass = '';public $dir = '';private $handle = null;private $tempDir = '';private $_handle = null;public function __construct(){parent::__construct();$this->useFTP  = true;$this->useSSL  = AKFactory::get('kickstart.ftp.ssl', false);$this->passive = AKFactory::get('kickstart.ftp.passive', true);$this->host    = AKFactory::get('kickstart.ftp.host', '');$this->port    = AKFactory::get('kickstart.ftp.port', 21);$this->user    = AKFactory::get('kickstart.ftp.user', '');$this->pass    = AKFactory::get('kickstart.ftp.pass', '');$this->dir     = AKFactory::get('kickstart.ftp.dir', '');$this->tempDir = AKFactory::get('kickstart.ftp.tempdir', '');if (trim($this->port) == ''){$this->port = 21;}if (empty($this->host) || empty($this->user) || empty($this->pass)){$this->useFTP = false;}$connected = $this->connect();if (!$connected){$this->useFTP = false;}if ($connected){if (!empty($this->tempDir)){$tempDir  = rtrim($this->tempDir, '/\\') . '/';$writable = $this->isDirWritable($tempDir);}else{$tempDir  = '';$writable = false;}if (!$writable){$tempDir = KSROOTDIR;if (empty($tempDir)){$tempDir = '.';}$absoluteDirToHere = $tempDir;$tempDir           = rtrim(str_replace('\\', '/', $tempDir), '/');if (!empty($tempDir)){$tempDir .= '/';}$this->tempDir = $tempDir;$writable = $this->isDirWritable($tempDir);}if (!$writable){$tempDir                 = $absoluteDirToHere . '/kicktemp';$trustMeIKnowWhatImDoing = 500 + 10 + 1; $this->createDirRecursive($tempDir, $trustMeIKnowWhatImDoing);$this->fixPermissions($tempDir);$writable = $this->isDirWritable($tempDir);}if (!$writable){$userdir = AKFactory::get('kickstart.ftp.tempdir', '');if (!empty($userdir)){$absolute = false;$absolute = $absolute || (substr($userdir, 0, 1) == '/');$absolute = $absolute || (substr($userdir, 1, 1) == ':');$absolute = $absolute || (substr($userdir, 2, 1) == ':');if (!$absolute){$tempDir = $absoluteDirToHere . $userdir;}else{$tempDir = $userdir;}if (is_dir($tempDir)){$writable = $this->isDirWritable($tempDir);}}}$this->tempDir = $tempDir;if (!$writable){$this->setError(AKText::_('FTP_TEMPDIR_NOT_WRITABLE'));}else{AKFactory::set('kickstart.ftp.tempdir', $tempDir);$this->tempDir = $tempDir;}}}public function connect(){if (!$this->useFTP){return false;}if ($this->useSSL){$this->handle = @ftp_ssl_connect($this->host, $this->port);}else{$this->handle = @ftp_connect($this->host, $this->port);}if ($this->handle === false){$this->setError(AKText::_('WRONG_FTP_HOST'));return false;}if (!@ftp_login($this->handle, $this->user, $this->pass)){$this->setError(AKText::_('WRONG_FTP_USER'));@ftp_close($this->handle);return false;}if (!@ftp_chdir($this->handle, $this->dir)){$this->setError(AKText::_('WRONG_FTP_PATH1'));@ftp_close($this->handle);return false;}if ($this->passive){@ftp_pasv($this->handle, true);}else{@ftp_pasv($this->handle, false);}$testFilename = defined('KSSELFNAME') ? KSSELFNAME : basename(__FILE__);$tempHandle   = fopen('php://temp', 'r+');if (@ftp_fget($this->handle, $tempHandle, $testFilename, FTP_ASCII, 0) === false){$this->setError(AKText::_('WRONG_FTP_PATH2'));@ftp_close($this->handle);fclose($tempHandle);return false;}fclose($tempHandle);return true;}private function isDirWritable($dir){$fp = @fopen($dir . '/kickstart.dat', 'wb');if ($fp === false){return false;}@fclose($fp);unlink($dir . '/kickstart.dat');return true;}public function createDirRecursive($dirName, $perms){$removePath = AKFactory::get('kickstart.setup.destdir', '');if (!empty($removePath)){$removePath = str_replace('\\', '/', $removePath);$dirName    = str_replace('\\', '/', $dirName);$removePath = rtrim($removePath, '/\\') . '/';$dirName    = rtrim($dirName, '/\\') . '/';$left = substr($dirName, 0, strlen($removePath));if ($left == $removePath){$dirName = substr($dirName, strlen($removePath));}}if (empty($dirName)){$dirName = '';}$check   = '/' . trim($this->dir, '/') . '/' . trim($dirName, '/');$checkFS = $removePath . trim($dirName, '/');if ($this->is_dir($check)){return true;}$alldirs       = explode('/', $dirName);$previousDir   = '/' . trim($this->dir);$previousDirFS = rtrim($removePath, '/\\');foreach ($alldirs as $curdir){$check   = $previousDir . '/' . $curdir;$checkFS = $previousDirFS . '/' . $curdir;if (!is_dir($checkFS) && !$this->is_dir($check)){if (!@unlink($checkFS) && $this->useFTP){@ftp_delete($this->handle, $check);}$createdDir = @mkdir($checkFS, 0755);if (!$createdDir && $this->useFTP){$createdDir = @ftp_mkdir($this->handle, $check);}if ($createdDir === false){$this->fixPermissions($checkFS);$createdDir = @mkdir($checkFS, 0755);if (!$createdDir && $this->useFTP){$createdDir = @ftp_mkdir($this->handle, $check);}if ($createdDir === false){$this->setError(AKText::sprintf('FTP_CANT_CREATE_DIR', $check));return false;}}if (!@chmod($checkFS, $perms) && $this->useFTP){@ftp_chmod($this->handle, $perms, $check);}}$previousDir   = $check;$previousDirFS = $checkFS;}return true;}private function is_dir($dir){if ($this->useFTP){return @ftp_chdir($this->handle, $dir);}return false;}private function fixPermissions($path){if (!defined('KSDEBUG')){$oldErrorReporting = @error_reporting(E_NONE);}$relPath  = str_replace('\\', '/', $path);$basePath = rtrim(str_replace('\\', '/', KSROOTDIR), '/');$basePath = rtrim($basePath, '/');if (!empty($basePath)){$basePath .= '/';}if (substr($relPath, 0, strlen($basePath)) == $basePath){$relPath = substr($relPath, strlen($basePath));}$dirArray  = explode('/', $relPath);$pathBuilt = rtrim($basePath, '/');foreach ($dirArray as $dir){if (empty($dir)){continue;}$oldPath = $pathBuilt;$pathBuilt .= '/' . $dir;if (is_dir($oldPath . $dir)){$trustMeIKnowWhatImDoing = 500 + 10 + 1; @chmod($oldPath . $dir, $trustMeIKnowWhatImDoing);}else{$trustMeIKnowWhatImDoing = 500 + 10 + 1; if (@chmod($oldPath . $dir, $trustMeIKnowWhatImDoing) === false){@unlink($oldPath . $dir);}}}if (!defined('KSDEBUG')){@error_reporting($oldErrorReporting);}}function __wakeup(){if ($this->useFTP){$this->connect();}}function __destruct(){if (!$this->useFTP){@ftp_close($this->handle);}}public function process(){if (is_null($this->tempFilename)){return true;}$remotePath = dirname($this->filename);$removePath = AKFactory::get('kickstart.setup.destdir', '');$root       = rtrim($removePath, '/\\');if (!empty($removePath)){$removePath = ltrim($removePath, "/");$remotePath = ltrim($remotePath, "/");$left       = substr($remotePath, 0, strlen($removePath));if ($left == $removePath){$remotePath = substr($remotePath, strlen($removePath));}}$absoluteFSPath  = dirname($this->filename);$relativeFTPPath = trim($remotePath, '/');$absoluteFTPPath = '/' . trim($this->dir, '/') . '/' . trim($remotePath, '/');$onlyFilename    = basename($this->filename);$remoteName = $absoluteFTPPath . '/' . $onlyFilename;if (!is_dir($root . '/' . $absoluteFSPath)){$ret = $this->createDirRecursive($absoluteFSPath, 0755);if (($ret === false) && ($this->useFTP)){$ret = @ftp_chdir($this->handle, $absoluteFTPPath);}if ($ret === false){$this->setError(AKText::sprintf('FTP_COULDNT_UPLOAD', $this->filename));return false;}}if ($this->useFTP){$ret = @ftp_chdir($this->handle, $absoluteFTPPath);}$ret = @copy($this->tempFilename, $root . '/' . $this->filename);if ($ret === false){$this->fixPermissions($this->filename);$this->unlink($this->filename);$ret = @copy($this->tempFilename, $root . '/' . $this->filename);}if ($this->useFTP && ($ret === false)){$ret = @ftp_put($this->handle, $remoteName, $this->tempFilename, FTP_BINARY);if ($ret === false){$this->fixPermissions($this->filename);$this->unlink($this->filename);$fp = @fopen($this->tempFilename, 'rb');if ($fp !== false){$ret = @ftp_fput($this->handle, $remoteName, $fp, FTP_BINARY);@fclose($fp);}else{$ret = false;}}}@unlink($this->tempFilename);if ($ret === false){$this->setError(AKText::sprintf('FTP_COULDNT_UPLOAD', $this->filename));return false;}$restorePerms = AKFactory::get('kickstart.setup.restoreperms', false);$perms        = $restorePerms ? $this->perms : 0644;$ret = @chmod($root . '/' . $this->filename, $perms);if ($this->useFTP && ($ret === false)){@ftp_chmod($this->_handle, $perms, $remoteName);}return true;}public function unlink($file){$ret = @unlink($file);if (!$ret && $this->useFTP){$removePath = AKFactory::get('kickstart.setup.destdir', '');if (!empty($removePath)){$left = substr($file, 0, strlen($removePath));if ($left == $removePath){$file = substr($file, strlen($removePath));}}$check = '/' . trim($this->dir, '/') . '/' . trim($file, '/');$ret = @ftp_delete($this->handle, $check);}return $ret;}public function processFilename($filename, $perms = 0755){if ($this->getError()){return false;}if (is_null($filename)){$this->filename     = null;$this->tempFilename = null;return null;}$removePath = AKFactory::get('kickstart.setup.destdir', '');if (!empty($removePath)){$left = substr($filename, 0, strlen($removePath));if ($left == $removePath){$filename = substr($filename, strlen($removePath));}}$filename = ltrim($filename, '/');$this->filename     = $filename;$this->tempFilename = tempnam($this->tempDir, 'kickstart-');$this->perms        = $perms;if (empty($this->tempFilename)){$this->tempFilename = $this->tempDir . '/kickstart-' . time() . '.dat';}return $this->tempFilename;}public function close(){if (!$this->useFTP){@ftp_close($this->handle);}}public function chmod($file, $perms){if (AKFactory::get('kickstart.setup.dryrun', '0')){return true;}$ret = @chmod($file, $perms);if (!$ret && $this->useFTP){$removePath = AKFactory::get('kickstart.setup.destdir', '');if (!empty($removePath)){$left = substr($file, 0, strlen($removePath));if ($left == $removePath){$file = substr($file, strlen($removePath));}}$file = ltrim($file, '/');$ret = @ftp_chmod($this->handle, $perms, $file);}return $ret;}public function rmdir($directory){$ret = @rmdir($directory);if (!$ret && $this->useFTP){$removePath = AKFactory::get('kickstart.setup.destdir', '');if (!empty($removePath)){$left = substr($directory, 0, strlen($removePath));if ($left == $removePath){$directory = substr($directory, strlen($removePath));}}$check = '/' . trim($this->dir, '/') . '/' . trim($directory, '/');$ret = @ftp_rmdir($this->handle, $check);}return $ret;}public function rename($from, $to){$ret = @rename($from, $to);if (!$ret && $this->useFTP){$originalFrom = $from;$originalTo   = $to;$removePath = AKFactory::get('kickstart.setup.destdir', '');if (!empty($removePath)){$left = substr($from, 0, strlen($removePath));if ($left == $removePath){$from = substr($from, strlen($removePath));}}$from = '/' . trim($this->dir, '/') . '/' . trim($from, '/');if (!empty($removePath)){$left = substr($to, 0, strlen($removePath));if ($left == $removePath){$to = substr($to, strlen($removePath));}}$to = '/' . trim($this->dir, '/') . '/' . trim($to, '/');$ret = @ftp_rename($this->handle, $from, $to);}return $ret;}}

class AKUnarchiverJPA extends AKAbstractUnarchiver{protected $archiveHeaderData = array();protected function readArchiveHeader(){debugMsg('Preparing to read archive header');$this->archiveHeaderData = new stdClass();debugMsg('Opening the first part');$this->nextFile();if ($this->fp === false){debugMsg('Could not open the first part');return false;}$sig = fread($this->fp, 3);if ($sig != 'JPA'){debugMsg('Invalid archive signature');$this->setError(AKText::_('ERR_NOT_A_JPA_FILE'));return false;}$header_length_array = unpack('v', fread($this->fp, 2));$header_length       = $header_length_array[1];$bin_data    = fread($this->fp, 14);$header_data = unpack('Cmajor/Cminor/Vcount/Vuncsize/Vcsize', $bin_data);$rest_length = $header_length - 19;if ($rest_length > 0){$junk = fread($this->fp, $rest_length);}else{$junk = '';}$temp = array('signature'        => $sig,'length'           => $header_length,'major'            => $header_data['major'],'minor'            => $header_data['minor'],'filecount'        => $header_data['count'],'uncompressedsize' => $header_data['uncsize'],'compressedsize'   => $header_data['csize'],'unknowndata'      => $junk);foreach ($temp as $key => $value){$this->archiveHeaderData->{$key} = $value;}debugMsg('Header data:');debugMsg('Length              : ' . $header_length);debugMsg('Major               : ' . $header_data['major']);debugMsg('Minor               : ' . $header_data['minor']);debugMsg('File count          : ' . $header_data['count']);debugMsg('Uncompressed size   : ' . $header_data['uncsize']);debugMsg('Compressed size	  : ' . $header_data['csize']);$this->currentPartOffset = @ftell($this->fp);$this->dataReadLength = 0;return true;}protected function readFileHeader(){if ($this->isEOF(true)){debugMsg('Archive part EOF; moving to next file');$this->nextFile();}$this->currentPartOffset = ftell($this->fp);debugMsg("Reading file signature; part {$this->currentPartNumber}, offset {$this->currentPartOffset}");$signature = fread($this->fp, 3);$this->fileHeader            = new stdClass();$this->fileHeader->timestamp = 0;if ($signature != 'JPF'){if ($this->isEOF(true)){$this->nextFile();if (!$this->isEOF(false)){debugMsg('Invalid file signature before end of archive encountered');$this->setError(AKText::sprintf('INVALID_FILE_HEADER', $this->currentPartNumber, $this->currentPartOffset));return false;}return false;}else{$screwed = true;if (AKFactory::get('kickstart.setup.ignoreerrors', false)){debugMsg('Invalid file block signature; launching heuristic file block signature scanner');$screwed = !$this->heuristicFileHeaderLocator();if (!$screwed){$signature = 'JPF';}else{debugMsg('Heuristics failed. Brace yourself for the imminent crash.');}}if ($screwed){debugMsg('Invalid file block signature');$this->setError(AKText::sprintf('INVALID_FILE_HEADER', $this->currentPartNumber, $this->currentPartOffset));return false;}}}$isBannedFile = false;$length_array = unpack('vblocksize/vpathsize', fread($this->fp, 4));if ($length_array['pathsize'] > 0){$file = fread($this->fp, $length_array['pathsize']);}else{$file = '';}$isRenamed = false;if (is_array($this->renameFiles) && (count($this->renameFiles) > 0)){if (array_key_exists($file, $this->renameFiles)){$file      = $this->renameFiles[$file];$isRenamed = true;}}$isDirRenamed = false;if (is_array($this->renameDirs) && (count($this->renameDirs) > 0)){if (array_key_exists(dirname($file), $this->renameDirs)){$file         = rtrim($this->renameDirs[dirname($file)], '/') . '/' . basename($file);$isRenamed    = true;$isDirRenamed = true;}}$bin_data    = fread($this->fp, 14);$header_data = unpack('Ctype/Ccompression/Vcompsize/Vuncompsize/Vperms', $bin_data);$restBytes = $length_array['blocksize'] - (21 + $length_array['pathsize']);if ($restBytes > 0){while ($restBytes >= 4){$extra_header_data = fread($this->fp, 4);$extra_header      = unpack('vsignature/vlength', $extra_header_data);$restBytes -= 4;$extra_header['length'] -= 4;switch ($extra_header['signature']){case 256:if ($extra_header['length'] > 0){$bindata = fread($this->fp, $extra_header['length']);$restBytes -= $extra_header['length'];$timestamps                  = unpack('Vmodified', substr($bindata, 0, 4));$filectime                   = $timestamps['modified'];$this->fileHeader->timestamp = $filectime;}break;default:if ($extra_header['length'] > 0){$junk = fread($this->fp, $extra_header['length']);$restBytes -= $extra_header['length'];}break;}}if ($restBytes > 0){$junk = fread($this->fp, $restBytes);}}$compressionType = $header_data['compression'];$this->fileHeader->file         = $file;$this->fileHeader->compressed   = $header_data['compsize'];$this->fileHeader->uncompressed = $header_data['uncompsize'];switch ($header_data['type']){case 0:$this->fileHeader->type = 'dir';break;case 1:$this->fileHeader->type = 'file';break;case 2:$this->fileHeader->type = 'link';break;}switch ($compressionType){case 0:$this->fileHeader->compression = 'none';break;case 1:$this->fileHeader->compression = 'gzip';break;case 2:$this->fileHeader->compression = 'bzip2';break;}$this->fileHeader->permissions = $header_data['perms'];if ((basename($this->fileHeader->file) == ".") || (basename($this->fileHeader->file) == "..")){$isBannedFile = true;}if ((count($this->skipFiles) > 0) && (!$isRenamed)){if (in_array($this->fileHeader->file, $this->skipFiles)){$isBannedFile = true;}}if ($isBannedFile){debugMsg('Skipping file ' . $this->fileHeader->file);$seekleft = $this->fileHeader->compressed;while ($seekleft > 0){$curSize = @filesize($this->archiveList[$this->currentPartNumber]);$curPos  = @ftell($this->fp);$canSeek = $curSize - $curPos;if ($canSeek > $seekleft){$canSeek = $seekleft;}@fseek($this->fp, $canSeek, SEEK_CUR);$seekleft -= $canSeek;if ($seekleft){$this->nextFile();}}$this->currentPartOffset = @ftell($this->fp);$this->runState          = AK_STATE_DONE;return true;}$this->fileHeader->file = $this->removePath($this->fileHeader->file);if (!empty($this->addPath) && !$isDirRenamed){$this->fileHeader->file = $this->addPath . $this->fileHeader->file;}$restorePerms = AKFactory::get('kickstart.setup.restoreperms', false);if ($this->fileHeader->type == 'file'){if ($restorePerms){$this->fileHeader->realFile =$this->postProcEngine->processFilename($this->fileHeader->file, $this->fileHeader->permissions);}else{$this->fileHeader->realFile = $this->postProcEngine->processFilename($this->fileHeader->file);}}elseif ($this->fileHeader->type == 'dir'){$dir = $this->fileHeader->file;if ($restorePerms){$this->postProcEngine->createDirRecursive($this->fileHeader->file, $this->fileHeader->permissions);}else{$this->postProcEngine->createDirRecursive($this->fileHeader->file, 0755);}$this->postProcEngine->processFilename(null);}else{$this->postProcEngine->processFilename(null);}$this->createDirectory();$this->runState = AK_STATE_HEADER;$this->dataReadLength = 0;return true;}protected function heuristicFileHeaderLocator(){$ret     = false;$fullEOF = false;while (!$ret && !$fullEOF){$this->currentPartOffset = @ftell($this->fp);if ($this->isEOF(true)){$this->nextFile();}if ($this->isEOF(false)){$fullEOF = true;continue;}$chunk     = fread($this->fp, 524288);$size_read = mb_strlen($chunk, '8bit');$pos = mb_strpos($chunk, 'JPF', 0, '8bit');if ($pos !== false){$this->currentPartOffset += $pos + 3;@fseek($this->fp, $this->currentPartOffset, SEEK_SET);$ret = true;}else{$this->currentPartOffset = @ftell($this->fp);}}return $ret;}protected function createDirectory(){if (AKFactory::get('kickstart.setup.dryrun', '0')){return true;}if (empty($this->fileHeader->realFile)){$this->fileHeader->realFile = $this->fileHeader->file;}$lastSlash = strrpos($this->fileHeader->realFile, '/');$dirName   = substr($this->fileHeader->realFile, 0, $lastSlash);$perms     = $this->flagRestorePermissions ? $this->fileHeader->permissions : 0755;$ignore    = AKFactory::get('kickstart.setup.ignoreerrors', false) || $this->isIgnoredDirectory($dirName);if (($this->postProcEngine->createDirRecursive($dirName, $perms) == false) && (!$ignore)){$this->setError(AKText::sprintf('COULDNT_CREATE_DIR', $dirName));return false;}else{return true;}}protected function processFileData(){switch ($this->fileHeader->type){case 'dir':return $this->processTypeDir();break;case 'link':return $this->processTypeLink();break;case 'file':switch ($this->fileHeader->compression){case 'none':return $this->processTypeFileUncompressed();break;case 'gzip':case 'bzip2':return $this->processTypeFileCompressedSimple();break;}break;default:debugMsg('Unknown file type ' . $this->fileHeader->type);break;}}private function processTypeDir(){$this->runState = AK_STATE_DATAREAD;return true;}private function processTypeLink(){$readBytes   = 0;$toReadBytes = 0;$leftBytes   = $this->fileHeader->compressed;$data        = '';while ($leftBytes > 0){$toReadBytes     = ($leftBytes > $this->chunkSize) ? $this->chunkSize : $leftBytes;$mydata          = $this->fread($this->fp, $toReadBytes);$reallyReadBytes = akstringlen($mydata);$data .= $mydata;$leftBytes -= $reallyReadBytes;if ($reallyReadBytes < $toReadBytes){if ($this->isEOF(true) && !$this->isEOF(false)){$this->nextFile();}else{debugMsg('End of local file before reading all data with no more parts left. The archive is corrupt or truncated.');$this->setError(AKText::_('ERR_CORRUPT_ARCHIVE'));return false;}}}$filename = isset($this->fileHeader->realFile) ? $this->fileHeader->realFile : $this->fileHeader->file;if (!AKFactory::get('kickstart.setup.dryrun', '0')){if (file_exists($filename)){@unlink($filename);@rmdir($filename);}if (substr($filename, -1) == '/'){$filename = substr($filename, 0, -1);}@symlink($data, $filename);}$this->runState = AK_STATE_DATAREAD;return true; }private function processTypeFileUncompressed(){if (($this->dataReadLength == 0) && !AKFactory::get('kickstart.setup.dryrun', '0')){$this->setCorrectPermissions($this->fileHeader->file);}if (!AKFactory::get('kickstart.setup.dryrun', '0')){$ignore =AKFactory::get('kickstart.setup.ignoreerrors', false) || $this->isIgnoredDirectory($this->fileHeader->file);if ($this->dataReadLength == 0){$outfp = @fopen($this->fileHeader->realFile, 'wb');}else{$outfp = @fopen($this->fileHeader->realFile, 'ab');}if (($outfp === false) && (!$ignore)){debugMsg('Could not write to output file');$this->setError(AKText::sprintf('COULDNT_WRITE_FILE', $this->fileHeader->realFile));return false;}}if ($this->fileHeader->compressed == 0){if (!AKFactory::get('kickstart.setup.dryrun', '0') && is_resource($outfp)){@fclose($outfp);}$this->runState = AK_STATE_DATAREAD;return true;}$timer = AKFactory::getTimer();$toReadBytes = 0;$leftBytes   = $this->fileHeader->compressed - $this->dataReadLength;while (($leftBytes > 0) && ($timer->getTimeLeft() > 0)){$toReadBytes     = ($leftBytes > $this->chunkSize) ? $this->chunkSize : $leftBytes;$data            = $this->fread($this->fp, $toReadBytes);$reallyReadBytes = akstringlen($data);$leftBytes -= $reallyReadBytes;$this->dataReadLength += $reallyReadBytes;if ($reallyReadBytes < $toReadBytes){if ($this->isEOF(true) && !$this->isEOF(false)){$this->nextFile();}else{debugMsg('Not enough data in file. The archive is truncated or corrupt.');$this->setError(AKText::_('ERR_CORRUPT_ARCHIVE'));return false;}}if (!AKFactory::get('kickstart.setup.dryrun', '0')){if (is_resource($outfp)){@fwrite($outfp, $data);}}}if (!AKFactory::get('kickstart.setup.dryrun', '0')){if (is_resource($outfp)){@fclose($outfp);}}if ($leftBytes > 0){$this->runState = AK_STATE_DATA;}else{$this->runState       = AK_STATE_DATAREAD;$this->dataReadLength = 0;}return true;}private function processTypeFileCompressedSimple(){if (!AKFactory::get('kickstart.setup.dryrun', '0')){$this->setCorrectPermissions($this->fileHeader->file);$outfp = @fopen($this->fileHeader->realFile, 'wb');$ignore =AKFactory::get('kickstart.setup.ignoreerrors', false) || $this->isIgnoredDirectory($this->fileHeader->file);if (($outfp === false) && (!$ignore)){debugMsg('Could not write to output file');$this->setError(AKText::sprintf('COULDNT_WRITE_FILE', $this->fileHeader->realFile));return false;}}if ($this->fileHeader->compressed == 0){if (!AKFactory::get('kickstart.setup.dryrun', '0')){if (is_resource($outfp)){@fclose($outfp);}}$this->runState = AK_STATE_DATAREAD;return true;}$zipData = $this->fread($this->fp, $this->fileHeader->compressed);while (akstringlen($zipData) < $this->fileHeader->compressed){if ($this->isEOF(true) && !$this->isEOF(false)){$this->nextFile();$bytes_left = $this->fileHeader->compressed - akstringlen($zipData);$zipData .= $this->fread($this->fp, $bytes_left);}else{debugMsg('End of local file before reading all data with no more parts left. The archive is corrupt or truncated.');$this->setError(AKText::_('ERR_CORRUPT_ARCHIVE'));return false;}}if ($this->fileHeader->compression == 'gzip'){$unzipData = gzinflate($zipData);}elseif ($this->fileHeader->compression == 'bzip2'){$unzipData = bzdecompress($zipData);}unset($zipData);if (!AKFactory::get('kickstart.setup.dryrun', '0') && is_resource($outfp)){@fwrite($outfp, $unzipData, $this->fileHeader->uncompressed);@fclose($outfp);}unset($unzipData);$this->runState = AK_STATE_DATAREAD;return true;}}

class AKUnarchiverZIP extends AKUnarchiverJPA{var $expectDataDescriptor = false;protected function readArchiveHeader(){debugMsg('Preparing to read archive header');$this->archiveHeaderData = new stdClass();debugMsg('Opening the first part');$this->nextFile();if ($this->fp === false){debugMsg('The first part is not readable');return false;}$sigBinary  = fread($this->fp, 4);$headerData = unpack('Vsig', $sigBinary);if ($headerData['sig'] == 0x04034b50){debugMsg('The archive is not multipart');fseek($this->fp, -4, SEEK_CUR);}else{debugMsg('The archive is multipart');}$multiPartSigs = array(0x08074b50,        0x30304b50,        0x04034b50        );if (!in_array($headerData['sig'], $multiPartSigs)){debugMsg('Invalid header signature ' . dechex($headerData['sig']));$this->setError(AKText::_('ERR_CORRUPT_ARCHIVE'));return false;}$this->currentPartOffset = @ftell($this->fp);debugMsg('Current part offset after reading header: ' . $this->currentPartOffset);$this->dataReadLength = 0;return true;}protected function readFileHeader(){if ($this->isEOF(true)){debugMsg('Opening next archive part');$this->nextFile();}$this->currentPartOffset = ftell($this->fp);if ($this->expectDataDescriptor){$junk = @fread($this->fp, 4);$junk = unpack('Vsig', $junk);if ($junk['sig'] == 0x08074b50){$junk = @fread($this->fp, 12);debugMsg('Data descriptor (w/ header) skipped at ' . (ftell($this->fp) - 12));}else{$junk = @fread($this->fp, 8);debugMsg('Data descriptor (w/out header) skipped at ' . (ftell($this->fp) - 8));}if ($this->isEOF(true)){debugMsg('EOF before reading header');$this->nextFile();}}$headerBinary = fread($this->fp, 30);$headerData   =unpack('Vsig/C2ver/vbitflag/vcompmethod/vlastmodtime/vlastmoddate/Vcrc/Vcompsize/Vuncomp/vfnamelen/veflen', $headerBinary);if (!($headerData['sig'] == 0x04034b50)){debugMsg('Not a file signature at ' . (ftell($this->fp) - 4));if ($headerData['sig'] == 0x02014b50){debugMsg('EOCD signature at ' . (ftell($this->fp) - 4));while ($this->nextFile()){};@fseek($this->fp, 0, SEEK_END); return false;}else{debugMsg('Invalid signature ' . dechex($headerData['sig']) . ' at ' . ftell($this->fp));$this->setError(AKText::_('ERR_CORRUPT_ARCHIVE'));return false;}}$this->expectDataDescriptor = ($headerData['bitflag'] & 4) == 4;$this->fileHeader            = new stdClass();$this->fileHeader->timestamp = 0;$lastmodtime = $headerData['lastmodtime'];$lastmoddate = $headerData['lastmoddate'];if ($lastmoddate && $lastmodtime){$v_hour    = ($lastmodtime & 0xF800) >> 11;$v_minute  = ($lastmodtime & 0x07E0) >> 5;$v_seconde = ($lastmodtime & 0x001F) * 2;$v_year  = (($lastmoddate & 0xFE00) >> 9) + 1980;$v_month = ($lastmoddate & 0x01E0) >> 5;$v_day   = $lastmoddate & 0x001F;$this->fileHeader->timestamp = @mktime($v_hour, $v_minute, $v_seconde, $v_month, $v_day, $v_year);}$isBannedFile = false;$this->fileHeader->compressed   = $headerData['compsize'];$this->fileHeader->uncompressed = $headerData['uncomp'];$nameFieldLength                = $headerData['fnamelen'];$extraFieldLength               = $headerData['eflen'];$this->fileHeader->file = fread($this->fp, $nameFieldLength);$isRenamed = false;if (is_array($this->renameFiles) && (count($this->renameFiles) > 0)){if (array_key_exists($this->fileHeader->file, $this->renameFiles)){$this->fileHeader->file = $this->renameFiles[$this->fileHeader->file];$isRenamed              = true;}}$isDirRenamed = false;if (is_array($this->renameDirs) && (count($this->renameDirs) > 0)){if (array_key_exists(dirname($this->fileHeader->file), $this->renameDirs)){$file         =rtrim($this->renameDirs[dirname($this->fileHeader->file)], '/') . '/' . basename($this->fileHeader->file);$isRenamed    = true;$isDirRenamed = true;}}if ($extraFieldLength > 0){$extrafield = fread($this->fp, $extraFieldLength);}debugMsg('*' . ftell($this->fp) . ' IS START OF ' . $this->fileHeader->file . ' (' . $this->fileHeader->compressed . ' bytes)');$this->fileHeader->type = 'file';if (strrpos($this->fileHeader->file, '/') == strlen($this->fileHeader->file) - 1){$this->fileHeader->type = 'dir';}if (($headerData['ver1'] == 10) && ($headerData['ver2'] == 3)){$this->fileHeader->type = 'link';}switch ($headerData['compmethod']){case 0:$this->fileHeader->compression = 'none';break;case 8:$this->fileHeader->compression = 'gzip';break;}if ((basename($this->fileHeader->file) == ".") || (basename($this->fileHeader->file) == "..")){$isBannedFile = true;}if ((count($this->skipFiles) > 0) && (!$isRenamed)){if (in_array($this->fileHeader->file, $this->skipFiles)){$isBannedFile = true;}}if ($isBannedFile){$seekleft = $this->fileHeader->compressed;while ($seekleft > 0){$curSize = @filesize($this->archiveList[$this->currentPartNumber]);$curPos  = @ftell($this->fp);$canSeek = $curSize - $curPos;if ($canSeek > $seekleft){$canSeek = $seekleft;}@fseek($this->fp, $canSeek, SEEK_CUR);$seekleft -= $canSeek;if ($seekleft){$this->nextFile();}}$this->currentPartOffset = @ftell($this->fp);$this->runState          = AK_STATE_DONE;return true;}$this->fileHeader->file = $this->removePath($this->fileHeader->file);if (!empty($this->addPath) && !$isDirRenamed){$this->fileHeader->file = $this->addPath . $this->fileHeader->file;}if ($this->fileHeader->type == 'file'){$this->fileHeader->realFile = $this->postProcEngine->processFilename($this->fileHeader->file);}elseif ($this->fileHeader->type == 'dir'){$this->fileHeader->timestamp = 0;$dir = $this->fileHeader->file;$this->postProcEngine->createDirRecursive($this->fileHeader->file, 0755);$this->postProcEngine->processFilename(null);}else{$this->fileHeader->timestamp = 0;$this->postProcEngine->processFilename(null);}$this->createDirectory();$this->runState = AK_STATE_HEADER;return true;}}

class AKUnarchiverJPS extends AKUnarchiverJPA{protected $archiveHeaderData = array();protected $password = '';private static $pbkdf2Algorithm = 'sha1';private static $pbkdf2Iterations = 1000;private static $pbkdf2UseStaticSalt = 0;private static $pbkdf2StaticSalt = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";public function __construct(){parent::__construct();$this->password = AKFactory::get('kickstart.jps.password', '');}protected function readArchiveHeader(){$this->archiveHeaderData = new stdClass();$this->nextFile();if ($this->fp === false){return false;}$sig = fread($this->fp, 3);if ($sig != 'JPS'){$this->setError(AKText::_('ERR_NOT_A_JPS_FILE'));return false;}$bin_data    = fread($this->fp, 5);$header_data = unpack('Cmajor/Cminor/cspanned/vextra', $bin_data);$versionHumanReadable = $header_data['major'] . '.' . $header_data['minor'];$isV2Archive = version_compare($versionHumanReadable, '2.0', 'ge');$rest_length = $header_data['extra'];if ($isV2Archive && $rest_length){if (!$this->readKeyExpansionExtraHeader()){return false;}}elseif ($rest_length > 0){$junk = fread($this->fp, $rest_length);}$temp = array('signature' => $sig,'major'     => $header_data['major'],'minor'     => $header_data['minor'],'spanned'   => $header_data['spanned']);foreach ($temp as $key => $value){$this->archiveHeaderData->{$key} = $value;}$this->currentPartOffset = @ftell($this->fp);$this->dataReadLength = 0;return true;}protected function readFileHeader(){if ($this->isEOF(true)){$this->nextFile();}$this->currentPartOffset = ftell($this->fp);$signature = fread($this->fp, 3);if ($signature == 'JPE'){$this->setState('postrun');return true;}$this->fileHeader            = new stdClass();$this->fileHeader->timestamp = 0;if ($signature != 'JPF'){if ($this->isEOF(true)){$this->nextFile();if (!$this->isEOF(false)){$this->setError(AKText::sprintf('INVALID_FILE_HEADER', $this->currentPartNumber, $this->currentPartOffset));return false;}return false;}else{fseek($this->fp, -6, SEEK_CUR);$signature = fread($this->fp, 3);if ($signature == 'JPE'){return false;}$this->setError(AKText::sprintf('INVALID_FILE_HEADER', $this->currentPartNumber, $this->currentPartOffset));return false;}}$isBannedFile = false;AKEncryptionAES::setPbkdf2Algorithm(self::$pbkdf2Algorithm);AKEncryptionAES::setPbkdf2Iterations(self::$pbkdf2Iterations);AKEncryptionAES::setPbkdf2UseStaticSalt(self::$pbkdf2UseStaticSalt);AKEncryptionAES::setPbkdf2StaticSalt(self::$pbkdf2StaticSalt);$edbhData = fread($this->fp, 4);$edbh     = unpack('vencsize/vdecsize', $edbhData);$bin_data = fread($this->fp, $edbh['encsize']);$bin_data = AKEncryptionAES::AESDecryptCBC($bin_data, $this->password);$bin_data = substr($bin_data, 0, $edbh['decsize']);$length_array = unpack('vpathsize', substr($bin_data, 0, 2));$file = substr($bin_data, 2, $length_array['pathsize']);$isRenamed = false;if (is_array($this->renameFiles) && (count($this->renameFiles) > 0)){if (array_key_exists($file, $this->renameFiles)){$file      = $this->renameFiles[$file];$isRenamed = true;}}$isDirRenamed = false;if (is_array($this->renameDirs) && (count($this->renameDirs) > 0)){if (array_key_exists(dirname($file), $this->renameDirs)){$file         = rtrim($this->renameDirs[dirname($file)], '/') . '/' . basename($file);$isRenamed    = true;$isDirRenamed = true;}}$bin_data    = substr($bin_data, 2 + $length_array['pathsize']);$header_data = unpack('Ctype/Ccompression/Vuncompsize/Vperms/Vfilectime', $bin_data);$this->fileHeader->timestamp = $header_data['filectime'];$compressionType             = $header_data['compression'];$this->fileHeader->file         = $file;$this->fileHeader->uncompressed = $header_data['uncompsize'];switch ($header_data['type']){case 0:$this->fileHeader->type = 'dir';break;case 1:$this->fileHeader->type = 'file';break;case 2:$this->fileHeader->type = 'link';break;}switch ($compressionType){case 0:$this->fileHeader->compression = 'none';break;case 1:$this->fileHeader->compression = 'gzip';break;case 2:$this->fileHeader->compression = 'bzip2';break;}$this->fileHeader->permissions = $header_data['perms'];if ((basename($this->fileHeader->file) == ".") || (basename($this->fileHeader->file) == "..")){$isBannedFile = true;}if ((count($this->skipFiles) > 0) && (!$isRenamed)){if (in_array($this->fileHeader->file, $this->skipFiles)){$isBannedFile = true;}}if ($isBannedFile){$done = false;while (!$done){$binMiniHead = fread($this->fp, 8);if (in_array(substr($binMiniHead, 0, 3), array('JPF', 'JPE'))){@fseek($this->fp, -8, SEEK_CUR); $done = true; continue; }else{$miniHead = unpack('Vencsize/Vdecsize', $binMiniHead);@fseek($this->fp, $miniHead['encsize'], SEEK_CUR);}}$this->currentPartOffset = @ftell($this->fp);$this->runState          = AK_STATE_DONE;return true;}$this->fileHeader->file = $this->removePath($this->fileHeader->file);if (!empty($this->addPath) && !$isDirRenamed){$this->fileHeader->file = $this->addPath . $this->fileHeader->file;}$restorePerms = AKFactory::get('kickstart.setup.restoreperms', false);if ($this->fileHeader->type == 'file'){if ($restorePerms){$this->fileHeader->realFile =$this->postProcEngine->processFilename($this->fileHeader->file, $this->fileHeader->permissions);}else{$this->fileHeader->realFile = $this->postProcEngine->processFilename($this->fileHeader->file);}}elseif ($this->fileHeader->type == 'dir'){$dir                        = $this->fileHeader->file;$this->fileHeader->realFile = $dir;if ($restorePerms){$this->postProcEngine->createDirRecursive($this->fileHeader->file, $this->fileHeader->permissions);}else{$this->postProcEngine->createDirRecursive($this->fileHeader->file, 0755);}$this->postProcEngine->processFilename(null);}else{$this->postProcEngine->processFilename(null);}$this->createDirectory();$this->runState = AK_STATE_HEADER;$this->dataReadLength = 0;return true;}protected function createDirectory(){if (AKFactory::get('kickstart.setup.dryrun', '0')){return true;}$lastSlash = strrpos($this->fileHeader->realFile, '/');$dirName   = substr($this->fileHeader->realFile, 0, $lastSlash);$perms     = $this->flagRestorePermissions ? $retArray['permissions'] : 0755;$ignore    = AKFactory::get('kickstart.setup.ignoreerrors', false) || $this->isIgnoredDirectory($dirName);if (($this->postProcEngine->createDirRecursive($dirName, $perms) == false) && (!$ignore)){$this->setError(AKText::sprintf('COULDNT_CREATE_DIR', $dirName));return false;}else{return true;}}protected function processFileData(){switch ($this->fileHeader->type){case 'dir':return $this->processTypeDir();break;case 'link':return $this->processTypeLink();break;case 'file':switch ($this->fileHeader->compression){case 'none':return $this->processTypeFileUncompressed();break;case 'gzip':case 'bzip2':return $this->processTypeFileCompressedSimple();break;}break;}}private function processTypeDir(){$this->runState = AK_STATE_DATAREAD;return true;}private function processTypeLink(){if ($this->fileHeader->uncompressed == 0){$this->runState = AK_STATE_DATAREAD;return true;}$binMiniHeader   = fread($this->fp, 8);$reallyReadBytes = akstringlen($binMiniHeader);if ($reallyReadBytes < 8){if ($this->isEOF(true) && !$this->isEOF(false)){$this->nextFile();$binMiniHeader   = fread($this->fp, 8);$reallyReadBytes = akstringlen($binMiniHeader);if ($reallyReadBytes < 8){$this->setError(AKText::_('ERR_CORRUPT_ARCHIVE'));return false;}}else{$this->setError(AKText::_('ERR_CORRUPT_ARCHIVE'));return false;}}$miniHeader      = unpack('Vencsize/Vdecsize', $binMiniHeader);$toReadBytes     = $miniHeader['encsize'];$data            = $this->fread($this->fp, $toReadBytes);$reallyReadBytes = akstringlen($data);if ($reallyReadBytes < $toReadBytes){if ($this->isEOF(true) && !$this->isEOF(false)){$this->nextFile();$toReadBytes -= $reallyReadBytes;$restData        = $this->fread($this->fp, $toReadBytes);$reallyReadBytes = akstringlen($data);if ($reallyReadBytes < $toReadBytes){$this->setError(AKText::_('ERR_CORRUPT_ARCHIVE'));return false;}$data .= $restData;}else{$this->setError(AKText::_('ERR_CORRUPT_ARCHIVE'));return false;}}$data = AKEncryptionAES::AESDecryptCBC($data, $this->password);$data_length = akstringlen($data);if ($data_length < $miniHeader['decsize']){$this->setError(AKText::_('ERR_INVALID_JPS_PASSWORD'));return false;}$data = substr($data, 0, $miniHeader['decsize']);if (!AKFactory::get('kickstart.setup.dryrun', '0')){if (file_exists($this->fileHeader->file)){@unlink($this->fileHeader->file);@rmdir($this->fileHeader->file);}if (substr($this->fileHeader->file, -1) == '/'){$this->fileHeader->file = substr($this->fileHeader->file, 0, -1);}@symlink($data, $this->fileHeader->file);}$this->runState = AK_STATE_DATAREAD;return true; }private function processTypeFileUncompressed(){if (($this->dataReadLength == 0) && !AKFactory::get('kickstart.setup.dryrun', '0')){$this->setCorrectPermissions($this->fileHeader->file);}if (!AKFactory::get('kickstart.setup.dryrun', '0')){$ignore =AKFactory::get('kickstart.setup.ignoreerrors', false) || $this->isIgnoredDirectory($this->fileHeader->file);if ($this->dataReadLength == 0){$outfp = @fopen($this->fileHeader->realFile, 'wb');}else{$outfp = @fopen($this->fileHeader->realFile, 'ab');}if (($outfp === false) && (!$ignore)){$this->setError(AKText::sprintf('COULDNT_WRITE_FILE', $this->fileHeader->realFile));return false;}}if ($this->fileHeader->uncompressed == 0){if (!AKFactory::get('kickstart.setup.dryrun', '0') && is_resource($outfp)){@fclose($outfp);}$this->runState = AK_STATE_DATAREAD;return true;}else{$this->setError('An uncompressed file was detected; this is not supported by this archive extraction utility');return false;}return true;}private function processTypeFileCompressedSimple(){$timer = AKFactory::getTimer();if (($this->dataReadLength == 0) && !AKFactory::get('kickstart.setup.dryrun', '0')){$this->setCorrectPermissions($this->fileHeader->file);}if (!AKFactory::get('kickstart.setup.dryrun', '0')){$outfp = @fopen($this->fileHeader->realFile, 'wb');$ignore =AKFactory::get('kickstart.setup.ignoreerrors', false) || $this->isIgnoredDirectory($this->fileHeader->file);if (($outfp === false) && (!$ignore)){$this->setError(AKText::sprintf('COULDNT_WRITE_FILE', $this->fileHeader->realFile));return false;}}if ($this->fileHeader->uncompressed == 0){if (!AKFactory::get('kickstart.setup.dryrun', '0')){if (is_resource($outfp)){@fclose($outfp);}}$this->runState = AK_STATE_DATAREAD;return true;}$leftBytes = $this->fileHeader->uncompressed - $this->dataReadLength;while (($leftBytes > 0) && ($timer->getTimeLeft() > 0)){$binMiniHeader   = fread($this->fp, 8);$reallyReadBytes = akstringlen($binMiniHeader);if ($reallyReadBytes < 8){if ($this->isEOF(true) && !$this->isEOF(false)){$this->nextFile();$binMiniHeader   = fread($this->fp, 8);$reallyReadBytes = akstringlen($binMiniHeader);if ($reallyReadBytes < 8){$this->setError(AKText::_('ERR_CORRUPT_ARCHIVE'));return false;}}else{$this->setError(AKText::_('ERR_CORRUPT_ARCHIVE'));return false;}}$miniHeader      = unpack('Vencsize/Vdecsize', $binMiniHeader);$toReadBytes     = $miniHeader['encsize'];$data            = $this->fread($this->fp, $toReadBytes);$reallyReadBytes = akstringlen($data);if ($reallyReadBytes < $toReadBytes){if ($this->isEOF(true) && !$this->isEOF(false)){$this->nextFile();$toReadBytes -= $reallyReadBytes;$restData        = $this->fread($this->fp, $toReadBytes);$reallyReadBytes = akstringlen($restData);if ($reallyReadBytes < $toReadBytes){$this->setError(AKText::_('ERR_CORRUPT_ARCHIVE'));return false;}if (akstringlen($data) == 0){$data = $restData;}else{$data .= $restData;}}else{$this->setError(AKText::_('ERR_CORRUPT_ARCHIVE'));return false;}}$data = AKEncryptionAES::AESDecryptCBC($data, $this->password);$data_length = akstringlen($data);if ($data_length < $miniHeader['decsize']){$this->setError(AKText::_('ERR_INVALID_JPS_PASSWORD'));return false;}$data = substr($data, 0, $miniHeader['decsize']);$data    = gzinflate($data);$unc_len = akstringlen($data);if (!AKFactory::get('kickstart.setup.dryrun', '0')){if (is_resource($outfp)){@fwrite($outfp, $data, akstringlen($data));}}$this->dataReadLength += $unc_len;$leftBytes = $this->fileHeader->uncompressed - $this->dataReadLength;}if (!AKFactory::get('kickstart.setup.dryrun', '0')){if (is_resource($outfp)){@fclose($outfp);}}if ($leftBytes > 0){$this->runState = AK_STATE_DATA;}else{$this->runState       = AK_STATE_DATAREAD;$this->dataReadLength = 0;}return true;}private function readKeyExpansionExtraHeader(){$signature = fread($this->fp, 4);if ($signature != "JH\x00\x01"){$this->setError(AKText::_('ERR_NOT_A_JPS_FILE'));return false;}$bin_data    = fread($this->fp, 8);$header_data = unpack('vlength/Calgo/Viterations/CuseStaticSalt', $bin_data);if ($header_data['length'] != 76){$this->setError(AKText::_('ERR_NOT_A_JPS_FILE'));return false;}switch ($header_data['algo']){case 0:$algorithm = 'sha1';break;case 1:$algorithm = 'sha256';break;case 2:$algorithm = 'sha512';break;default:$this->setError(AKText::_('ERR_NOT_A_JPS_FILE'));return false;break;}self::$pbkdf2Algorithm     = $algorithm;self::$pbkdf2Iterations    = $header_data['iterations'];self::$pbkdf2UseStaticSalt = $header_data['useStaticSalt'];self::$pbkdf2StaticSalt    = fread($this->fp, 64);return true;}}

class AKCoreTimer extends AKAbstractObject{private $max_exec_time = null;private $start_time = null;public function __construct(){parent::__construct();$this->start_time = $this->microtime_float();$config_max_exec_time = AKFactory::get('kickstart.tuning.max_exec_time', 14);$bias                 = AKFactory::get('kickstart.tuning.run_time_bias', 75) / 100;if (@function_exists('ini_get')){$php_max_exec_time = @ini_get("maximum_execution_time");if ((!is_numeric($php_max_exec_time)) || ($php_max_exec_time == 0)){$php_max_exec_time = 14;}}else{$php_max_exec_time = 14;}$php_max_exec_time--;$php_max_exec_time    = $php_max_exec_time * $bias;$config_max_exec_time = $config_max_exec_time * $bias;if ($config_max_exec_time > $php_max_exec_time){$this->max_exec_time = $php_max_exec_time;}else{$this->max_exec_time = $config_max_exec_time;}}private function microtime_float(){list($usec, $sec) = explode(" ", microtime());return ((float) $usec + (float) $sec);}public function __wakeup(){$this->start_time = $this->microtime_float();}public function getTimeLeft(){return $this->max_exec_time - $this->getRunningTime();}public function getRunningTime(){return $this->microtime_float() - $this->start_time;}public function enforce_min_exec_time(){if (@function_exists('ini_get')){$php_max_exec = @ini_get("maximum_execution_time");}else{$php_max_exec = 10;}if (($php_max_exec == "") || ($php_max_exec == 0)){$php_max_exec = 10;}$php_max_exec = max($php_max_exec * 1000 - 1000, 0);$minexectime = AKFactory::get('kickstart.tuning.min_exec_time', 0);if (!is_numeric($minexectime)){$minexectime = 0;}if ($minexectime > $php_max_exec){$minexectime = $php_max_exec;}$elapsed_time = $this->getRunningTime() * 1000;if (($minexectime > $elapsed_time) && ($elapsed_time > 0)){$sleep_msec = $minexectime - $elapsed_time;if (function_exists('usleep')){usleep(1000 * $sleep_msec);}elseif (function_exists('time_nanosleep')){$sleep_sec  = floor($sleep_msec / 1000);$sleep_nsec = 1000000 * ($sleep_msec - ($sleep_sec * 1000));time_nanosleep($sleep_sec, $sleep_nsec);}elseif (function_exists('time_sleep_until')){$until_timestamp = time() + $sleep_msec / 1000;time_sleep_until($until_timestamp);}elseif (function_exists('sleep')){$sleep_sec = ceil($sleep_msec / 1000);sleep($sleep_sec);}}elseif ($elapsed_time > 0){}}public function resetTime(){$this->start_time = $this->microtime_float();}}

class AKUtilsLister extends AKAbstractObject{public function &getFiles($folder, $pattern = '*'){$arr   = array();$false = false;if (!is_dir($folder)){return $false;}$handle = @opendir($folder);if ($handle === false){$this->setWarning('Unreadable directory ' . $folder);return $false;}while (($file = @readdir($handle)) !== false){if (!fnmatch($pattern, $file)){continue;}if (($file != '.') && ($file != '..')){$ds    =($folder == '') || ($folder == '/') || (@substr($folder, -1) == '/') || (@substr($folder, -1) == DIRECTORY_SEPARATOR) ?'' : DIRECTORY_SEPARATOR;$dir   = $folder . $ds . $file;$isDir = is_dir($dir);if (!$isDir){$arr[] = $dir;}}}@closedir($handle);return $arr;}public function &getFolders($folder, $pattern = '*'){$arr   = array();$false = false;if (!is_dir($folder)){return $false;}$handle = @opendir($folder);if ($handle === false){$this->setWarning('Unreadable directory ' . $folder);return $false;}while (($file = @readdir($handle)) !== false){if (!fnmatch($pattern, $file)){continue;}if (($file != '.') && ($file != '..')){$ds    =($folder == '') || ($folder == '/') || (@substr($folder, -1) == '/') || (@substr($folder, -1) == DIRECTORY_SEPARATOR) ?'' : DIRECTORY_SEPARATOR;$dir   = $folder . $ds . $file;$isDir = is_dir($dir);if ($isDir){$arr[] = $dir;}}}@closedir($handle);return $arr;}}

class AKText extends AKAbstractObject{private $default_translation = array('AUTOMODEON'                      => 'Auto-mode enabled','ERR_NOT_A_JPA_FILE'              => 'The file is not a JPA archive','ERR_CORRUPT_ARCHIVE'             => 'The archive file is corrupt, truncated or archive parts are missing','ERR_INVALID_LOGIN'               => 'Invalid login','COULDNT_CREATE_DIR'              => 'Could not create %s folder','COULDNT_WRITE_FILE'              => 'Could not open %s for writing.','WRONG_FTP_HOST'                  => 'Wrong FTP host or port','WRONG_FTP_USER'                  => 'Wrong FTP username or password','WRONG_FTP_PATH1'                 => 'Wrong FTP initial directory - the directory doesn\'t exist','FTP_CANT_CREATE_DIR'             => 'Could not create directory %s','FTP_TEMPDIR_NOT_WRITABLE'        => 'Could not find or create a writable temporary directory','SFTP_TEMPDIR_NOT_WRITABLE'       => 'Could not find or create a writable temporary directory','FTP_COULDNT_UPLOAD'              => 'Could not upload %s','THINGS_HEADER'                   => 'Things you should know about Akeeba Kickstart','THINGS_01'                       => 'Kickstart is not an installer. It is an archive extraction tool. The actual installer was put inside the archive file at backup time.','THINGS_02'                       => 'Kickstart is not the only way to extract the backup archive. You can use Akeeba eXtract Wizard and upload the extracted files using FTP instead.','THINGS_03'                       => 'Kickstart is bound by your server\'s configuration. As such, it may not work at all.','THINGS_04'                       => 'You should download and upload your archive files using FTP in Binary transfer mode. Any other method could lead to a corrupt backup archive and restoration failure.','THINGS_05'                       => 'Post-restoration site load errors are usually caused by .htaccess or php.ini directives. You should understand that blank pages, 404 and 500 errors can usually be worked around by editing the aforementioned files. It is not our job to mess with your configuration files, because this could be dangerous for your site.','THINGS_06'                       => 'Kickstart overwrites files without a warning. If you are not sure that you are OK with that do not continue.','THINGS_07'                       => 'Trying to restore to the temporary URL of a cPanel host (e.g. http://1.2.3.4/~username) will lead to restoration failure and your site will appear to be not working. This is normal and it\'s just how your server and CMS software work.','THINGS_08'                       => 'You are supposed to read the documentation before using this software. Most issues can be avoided, or easily worked around, by understanding how this software works.','THINGS_09'                       => 'This text does not imply that there is a problem detected. It is standard text displayed every time you launch Kickstart.','CLOSE_LIGHTBOX'                  => 'Click here or press ESC to close this message','SELECT_ARCHIVE'                  => 'Select a backup archive','ARCHIVE_FILE'                    => 'Archive file:','SELECT_EXTRACTION'               => 'Select an extraction method','WRITE_TO_FILES'                  => 'Write to files:','WRITE_HYBRID'                    => 'Hybrid (use FTP only if needed)','WRITE_DIRECTLY'                  => 'Directly','WRITE_FTP'                       => 'Use FTP for all files','WRITE_SFTP'                      => 'Use SFTP for all files','FTP_HOST'                        => '(S)FTP host name:','FTP_PORT'                        => '(S)FTP port:','FTP_FTPS'                        => 'Use FTP over SSL (FTPS)','FTP_PASSIVE'                     => 'Use FTP Passive Mode','FTP_USER'                        => '(S)FTP user name:','FTP_PASS'                        => '(S)FTP password:','FTP_DIR'                         => '(S)FTP directory:','FTP_TEMPDIR'                     => 'Temporary directory:','FTP_CONNECTION_OK'               => 'FTP Connection Established','SFTP_CONNECTION_OK'              => 'SFTP Connection Established','FTP_CONNECTION_FAILURE'          => 'The FTP Connection Failed','SFTP_CONNECTION_FAILURE'         => 'The SFTP Connection Failed','FTP_TEMPDIR_WRITABLE'            => 'The temporary directory is writable.','FTP_TEMPDIR_UNWRITABLE'          => 'The temporary directory is not writable. Please check the permissions.','FTPBROWSER_ERROR_HOSTNAME'       => "Invalid FTP host or port",'FTPBROWSER_ERROR_USERPASS'       => "Invalid FTP username or password",'FTPBROWSER_ERROR_NOACCESS'       => "Directory doesn't exist or you don't have enough permissions to access it",'FTPBROWSER_ERROR_UNSUPPORTED'    => "Sorry, your FTP server doesn't support our FTP directory browser.",'FTPBROWSER_LBL_GOPARENT'         => "&lt;up one level&gt;",'FTPBROWSER_LBL_INSTRUCTIONS'     => 'Click on a directory to navigate into it. Click on OK to select that directory, Cancel to abort the procedure.','FTPBROWSER_LBL_ERROR'            => 'An error occurred','SFTP_NO_SSH2'                    => 'Your web server does not have the SSH2 PHP module, therefore can not connect to SFTP servers.','SFTP_NO_FTP_SUPPORT'             => 'Your SSH server does not allow SFTP connections','SFTP_WRONG_USER'                 => 'Wrong SFTP username or password','SFTP_WRONG_STARTING_DIR'         => 'You must supply a valid absolute path','SFTPBROWSER_ERROR_NOACCESS'      => "Directory doesn't exist or you don't have enough permissions to access it",'SFTP_COULDNT_UPLOAD'             => 'Could not upload %s','SFTP_CANT_CREATE_DIR'            => 'Could not create directory %s','UI-ROOT'                         => '&lt;root&gt;','CONFIG_UI_FTPBROWSER_TITLE'      => 'FTP Directory Browser','FTP_BROWSE'                      => 'Browse','BTN_CHECK'                       => 'Check','BTN_RESET'                       => 'Reset','BTN_TESTFTPCON'                  => 'Test FTP connection','BTN_TESTSFTPCON'                 => 'Test SFTP connection','BTN_GOTOSTART'                   => 'Start over','FINE_TUNE'                       => 'Fine tune','BTN_SHOW_FINE_TUNE'              => 'Show advanced options (for experts)','MIN_EXEC_TIME'                   => 'Minimum execution time:','MAX_EXEC_TIME'                   => 'Maximum execution time:','SECONDS_PER_STEP'                => 'seconds per step','EXTRACT_FILES'                   => 'Extract files','BTN_START'                       => 'Start','EXTRACTING'                      => 'Extracting','DO_NOT_CLOSE_EXTRACT'            => 'Do not close this window while the extraction is in progress','RESTACLEANUP'                    => 'Restoration and Clean Up','BTN_RUNINSTALLER'                => 'Run the Installer','BTN_CLEANUP'                     => 'Clean Up','BTN_SITEFE'                      => 'Visit your site\'s front-end','BTN_SITEBE'                      => 'Visit your site\'s back-end','WARNINGS'                        => 'Extraction Warnings','ERROR_OCCURED'                   => 'An error occured','STEALTH_MODE'                    => 'Stealth mode','STEALTH_URL'                     => 'HTML file to show to web visitors','ERR_NOT_A_JPS_FILE'              => 'The file is not a JPA archive','ERR_INVALID_JPS_PASSWORD'        => 'The password you gave is wrong or the archive is corrupt','JPS_PASSWORD'                    => 'Archive Password (for JPS files)','INVALID_FILE_HEADER'             => 'Invalid header in archive file, part %s, offset %s','NEEDSOMEHELPKS'                  => 'Want some help to use this tool? Read this first:','QUICKSTART'                      => 'Quick Start Guide','CANTGETITTOWORK'                 => 'Can\'t get it to work? Click me!','NOARCHIVESCLICKHERE'             => 'No archives detected. Click here for troubleshooting instructions.','POSTRESTORATIONTROUBLESHOOTING'  => 'Something not working after the restoration? Click here for troubleshooting instructions.','UPDATE_HEADER'                   => 'An updated version of Akeeba Kickstart (<span id="update-version">unknown</span>) is available!','UPDATE_NOTICE'                   => 'You are advised to always use the latest version of Akeeba Kickstart available. Older versions may be subject to bugs and will not be supported.','UPDATE_DLNOW'                    => 'Download now','UPDATE_MOREINFO'                 => 'More information','IGNORE_MOST_ERRORS'              => 'Ignore most errors','WRONG_FTP_PATH2'                 => 'Wrong FTP initial directory - the directory doesn\'t correspond to your site\'s web root','ARCHIVE_DIRECTORY'               => 'Archive directory:','RELOAD_ARCHIVES'                 => 'Reload','CONFIG_UI_SFTPBROWSER_TITLE'     => 'SFTP Directory Browser','ERR_COULD_NOT_OPEN_ARCHIVE_PART' => 'Could not open archive part file %s for reading. Check that the file exists, is readable by the web server and is not in a directory made out of reach by chroot, open_basedir restrictions or any other restriction put in place by your host.','RENAME_FILES'                    => 'Rename server configuration files','RESTORE_PERMISSIONS'             => 'Restore file permissions',);private $strings;private $language;public function __construct(){$this->strings = $this->default_translation;$this->loadTranslation('en-GB');$this->getBrowserLanguage();if (!is_null($this->language)){$this->loadTranslation();}}private function loadTranslation($lang = null){if (defined('KSLANGDIR')){$dirname = KSLANGDIR;}else{$dirname = KSROOTDIR;}$basename = basename(__FILE__, '.php') . '.ini';if (empty($lang)){$lang = $this->language;}$translationFilename = $dirname . DIRECTORY_SEPARATOR . $lang . '.' . $basename;if (!@file_exists($translationFilename) && ($basename != 'kickstart.ini')){$basename            = 'kickstart.ini';$translationFilename = $dirname . DIRECTORY_SEPARATOR . $lang . '.' . $basename;}if (!@file_exists($translationFilename)){return;}$temp = self::parse_ini_file($translationFilename, false);if (!is_array($this->strings)){$this->strings = array();}if (empty($temp)){$this->strings = array_merge($this->default_translation, $this->strings);}else{$this->strings = array_merge($this->strings, $temp);}}public static function parse_ini_file($file, $process_sections = false, $raw_data = false){$process_sections = ($process_sections !== true) ? false : true;if (!$raw_data){$ini = @file($file);}else{$ini = $file;}if (count($ini) == 0){return array();}$sections = array();$values   = array();$result   = array();$globals  = array();$i        = 0;if (!empty($ini)){foreach ($ini as $line){$line = trim($line);$line = str_replace("\t", " ", $line);if (!preg_match('/^[a-zA-Z0-9[]/', $line)){continue;}if ($line{0} == '['){$tmp        = explode(']', $line);$sections[] = trim(substr($tmp[0], 1));$i++;continue;}list($key, $value) = explode('=', $line, 2);$key   = trim($key);$value = trim($value);if (strstr($value, ";")){$tmp = explode(';', $value);if (count($tmp) == 2){if ((($value{0} != '"') && ($value{0} != "'")) ||preg_match('/^".*"\s*;/', $value) || preg_match('/^".*;[^"]*$/', $value) ||preg_match("/^'.*'\s*;/", $value) || preg_match("/^'.*;[^']*$/", $value)){$value = $tmp[0];}}else{if ($value{0} == '"'){$value = preg_replace('/^"(.*)".*/', '$1', $value);}elseif ($value{0} == "'"){$value = preg_replace("/^'(.*)'.*/", '$1', $value);}else{$value = $tmp[0];}}}$value = trim($value);$value = trim($value, "'\"");if ($i == 0){if (substr($line, -1, 2) == '[]'){$globals[$key][] = $value;}else{$globals[$key] = $value;}}else{if (substr($line, -1, 2) == '[]'){$values[$i - 1][$key][] = $value;}else{$values[$i - 1][$key] = $value;}}}}for ($j = 0; $j < $i; $j++){if ($process_sections === true){$result[$sections[$j]] = $values[$j];}else{$result[] = $values[$j];}}return $result + $globals;}public function getBrowserLanguage(){$user_languages = array();if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])){$languages = strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"]);$languages = str_replace(' ', '', $languages);$languages = explode(",", $languages);foreach ($languages as $language_list){$temp_array = array();$temp_array[0] = substr($language_list, 0, strcspn($language_list, ';'));$temp_array[1] = substr($language_list, 0, 2);if ((strlen($temp_array[0]) == 5) && ((substr($temp_array[0], 2, 1) == '-') || (substr($temp_array[0], 2, 1) == '_'))){$langLocation  = strtoupper(substr($temp_array[0], 3, 2));$temp_array[0] = $temp_array[1] . '-' . $langLocation;}$user_languages[] = $temp_array;}}else{$user_languages[0] = array('', ''); }$this->language = null;$basename       = basename(__FILE__, '.php') . '.ini';if (class_exists('AKUtilsLister')){$fs       = new AKUtilsLister();$iniFiles = $fs->getFiles(KSROOTDIR, '*.' . $basename);if (empty($iniFiles) && ($basename != 'kickstart.ini')){$basename = 'kickstart.ini';$iniFiles = $fs->getFiles(KSROOTDIR, '*.' . $basename);}}else{$iniFiles = null;}if (is_array($iniFiles)){foreach ($user_languages as $languageStruct){if (is_null($this->language)){$iniFiles = $fs->getFiles(KSROOTDIR, $languageStruct[1] . '-??.' . $basename);if (count($iniFiles) > 0){$filename       = $iniFiles[0];$filename       = substr($filename, strlen(KSROOTDIR) + 1);$this->language = substr($filename, 0, 5);}else{$this->language = null;}}}}if (is_null($this->language)){foreach ($user_languages as $languageStruct){if (@file_exists($languageStruct[0] . '.' . $basename) && is_null($this->language)){$this->language = $languageStruct[0];}else{}}}else{foreach ($user_languages as $languageStruct){if (substr($this->language, 0, strlen($languageStruct[1])) == $languageStruct[1]){if (file_exists($languageStruct[0] . '.' . $basename)){$this->language = $languageStruct[0];}}}}}public static function sprintf($key){$text = self::getInstance();$args = func_get_args();if (count($args) > 0){$args[0] = $text->_($args[0]);return @call_user_func_array('sprintf', $args);}return '';}public static function &getInstance(){static $instance;if (!is_object($instance)){$instance = new AKText();}return $instance;}public static function _($string){$text = self::getInstance();$key = strtoupper($string);$key = substr($key, 0, 1) == '_' ? substr($key, 1) : $key;if (isset ($text->strings[$key])){$string = $text->strings[$key];}else{if (defined($string)){$string = constant($string);}}return $string;}public function dumpLanguage(){$out = '';foreach ($this->strings as $key => $value){$out .= "$key=$value\n";}return $out;}public function asJavascript(){$out = '';foreach ($this->strings as $key => $value){$key   = addcslashes($key, '\\\'"');$value = addcslashes($value, '\\\'"');if (!empty($out)){$out .= ",\n";}$out .= "'$key':\t'$value'";}return $out;}public function resetTranslation(){$this->strings = $this->default_translation;}public function addDefaultLanguageStrings($stringList = array()){if (!is_array($stringList)){return;}if (empty($stringList)){return;}$this->strings = array_merge($stringList, $this->strings);}}

class AKFactory{private $objectlist = array();private $varlist = array();private function __construct(){}public static function serialize(){$engine = self::getUnarchiver();$engine->shutdown();$serialized = serialize(self::getInstance());if (function_exists('base64_encode') && function_exists('base64_decode')){$serialized = base64_encode($serialized);}return $serialized;}public static function &getUnarchiver($configOverride = null){static $class_name;if (!empty($configOverride)){if ($configOverride['reset']){$class_name = null;}}if (empty($class_name)){$filetype = self::get('kickstart.setup.filetype', null);if (empty($filetype)){$filename      = self::get('kickstart.setup.sourcefile', null);$basename      = basename($filename);$baseextension = strtoupper(substr($basename, -3));switch ($baseextension){case 'JPA':$filetype = 'JPA';break;case 'JPS':$filetype = 'JPS';break;case 'ZIP':$filetype = 'ZIP';break;default:die('Invalid archive type or extension in file ' . $filename);break;}}$class_name = 'AKUnarchiver' . ucfirst($filetype);}$destdir = self::get('kickstart.setup.destdir', null);if (empty($destdir)){$destdir = KSROOTDIR;}$object = self::getClassInstance($class_name);if ($object->getState() == 'init'){$sourcePath = self::get('kickstart.setup.sourcepath', '');$sourceFile = self::get('kickstart.setup.sourcefile', '');if (!empty($sourcePath)){$sourceFile = rtrim($sourcePath, '/\\') . '/' . $sourceFile;}$config = array('filename'            => $sourceFile,'restore_permissions' => self::get('kickstart.setup.restoreperms', 0),'post_proc'           => self::get('kickstart.procengine', 'direct'),'add_path'            => self::get('kickstart.setup.targetpath', $destdir),'remove_path'         => self::get('kickstart.setup.removepath', ''),'rename_files'        => self::get('kickstart.setup.renamefiles', array('.htaccess' => 'htaccess.bak', 'php.ini' => 'php.ini.bak', 'web.config' => 'web.config.bak','.user.ini' => '.user.ini.bak')),'skip_files'          => self::get('kickstart.setup.skipfiles', array(basename(__FILE__), 'kickstart.php', 'abiautomation.ini', 'htaccess.bak', 'php.ini.bak','cacert.pem')),'ignoredirectories'   => self::get('kickstart.setup.ignoredirectories', array('tmp', 'log', 'logs')),);if (!defined('KICKSTART')){$moreSkippedFiles     = array('administrator/components/com_akeeba/restoration.php','administrator/components/com_joomlaupdate/restoration.php','wp-content/plugins/akeebabackupwp/app/restoration.php','wp-content/plugins/akeebabackupcorewp/app/restoration.php','wp-content/plugins/akeebabackup/app/restoration.php','wp-content/plugins/akeebabackupwpcore/app/restoration.php','app/restoration.php',);$config['skip_files'] = array_merge($config['skip_files'], $moreSkippedFiles);}if (!empty($configOverride)){$config = array_merge($config, $configOverride);}$object->setup($config);}return $object;}public static function get($key, $default = null){$self = self::getInstance();if (array_key_exists($key, $self->varlist)){return $self->varlist[$key];}else{return $default;}}protected static function &getInstance($serialized_data = null){static $myInstance;if (!is_object($myInstance) || !is_null($serialized_data)){if (!is_null($serialized_data)){$myInstance = unserialize($serialized_data);}else{$myInstance = new self();}}return $myInstance;}protected static function &getClassInstance($class_name){$self = self::getInstance();if (!isset($self->objectlist[$class_name])){$self->objectlist[$class_name] = new $class_name;}return $self->objectlist[$class_name];}public static function unserialize($serialized_data){if (function_exists('base64_encode') && function_exists('base64_decode')){$serialized_data = base64_decode($serialized_data);}self::getInstance($serialized_data);}public static function nuke(){$self = self::getInstance();foreach ($self->objectlist as $key => $object){$self->objectlist[$key] = null;}$self->objectlist = array();}public static function set($key, $value){$self                = self::getInstance();$self->varlist[$key] = $value;}public static function &getPostProc($proc_engine = null){static $class_name;if (empty($class_name)){if (empty($proc_engine)){$proc_engine = self::get('kickstart.procengine', 'direct');}$class_name = 'AKPostproc' . ucfirst($proc_engine);}return self::getClassInstance($class_name);}public static function &getTimer(){return self::getClassInstance('AKCoreTimer');}}

interface AKEncryptionAESAdapterInterface{public function decrypt($plainText, $key);public function getBlockSize();public function isSupported();}

abstract class AKEncryptionAESAdapterAbstract{public function resizeKey($key, $size){if (empty($key)){return null;}$keyLength = strlen($key);if (function_exists('mb_strlen')){$keyLength = mb_strlen($key, 'ASCII');}if ($keyLength == $size){return $key;}if ($keyLength > $size){if (function_exists('mb_substr')){return mb_substr($key, 0, $size, 'ASCII');}return substr($key, 0, $size);}return $key . str_repeat("\0", ($size - $keyLength));}protected function getZeroPadding($string, $blockSize){$stringSize = strlen($string);if (function_exists('mb_strlen')){$stringSize = mb_strlen($string, 'ASCII');}if ($stringSize == $blockSize){return '';}if ($stringSize < $blockSize){return str_repeat("\0", $blockSize - $stringSize);}$paddingBytes = $stringSize % $blockSize;return str_repeat("\0", $blockSize - $paddingBytes);}}

class Mcrypt extends AKEncryptionAESAdapterAbstract implements AKEncryptionAESAdapterInterface{protected $cipherType = MCRYPT_RIJNDAEL_128;protected $cipherMode = MCRYPT_MODE_CBC;public function decrypt($cipherText, $key){$iv_size    = $this->getBlockSize();$key        = $this->resizeKey($key, $iv_size);$iv         = substr($cipherText, 0, $iv_size);$cipherText = substr($cipherText, $iv_size);$plainText  = mcrypt_decrypt($this->cipherType, $key, $cipherText, $this->cipherMode, $iv);return $plainText;}public function isSupported(){if (!function_exists('mcrypt_get_key_size')){return false;}if (!function_exists('mcrypt_get_iv_size')){return false;}if (!function_exists('mcrypt_create_iv')){return false;}if (!function_exists('mcrypt_encrypt')){return false;}if (!function_exists('mcrypt_decrypt')){return false;}if (!function_exists('mcrypt_list_algorithms')){return false;}if (!function_exists('hash')){return false;}if (!function_exists('hash_algos')){return false;}$algorightms = mcrypt_list_algorithms();if (!in_array('rijndael-128', $algorightms)){return false;}if (!in_array('rijndael-192', $algorightms)){return false;}if (!in_array('rijndael-256', $algorightms)){return false;}$algorightms = hash_algos();if (!in_array('sha256', $algorightms)){return false;}return true;}public function getBlockSize(){return mcrypt_get_iv_size($this->cipherType, $this->cipherMode);}}

class OpenSSL extends AKEncryptionAESAdapterAbstract implements AKEncryptionAESAdapterInterface{protected $openSSLOptions = 0;protected $method = 'aes-128-cbc';public function __construct(){$this->openSSLOptions = OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING;}public function decrypt($cipherText, $key){$iv_size    = $this->getBlockSize();$key        = $this->resizeKey($key, $iv_size);$iv         = substr($cipherText, 0, $iv_size);$cipherText = substr($cipherText, $iv_size);$plainText  = openssl_decrypt($cipherText, $this->method, $key, $this->openSSLOptions, $iv);return $plainText;}public function isSupported(){if (!function_exists('openssl_get_cipher_methods')){return false;}if (!function_exists('openssl_random_pseudo_bytes')){return false;}if (!function_exists('openssl_cipher_iv_length')){return false;}if (!function_exists('openssl_encrypt')){return false;}if (!function_exists('openssl_decrypt')){return false;}if (!function_exists('hash')){return false;}if (!function_exists('hash_algos')){return false;}$algorightms = openssl_get_cipher_methods();if (!in_array('aes-128-cbc', $algorightms)){return false;}$algorightms = hash_algos();if (!in_array('sha256', $algorightms)){return false;}return true;}public function getBlockSize(){return openssl_cipher_iv_length($this->method);}}

class AKEncryptionAES{protected static $Sbox =array(0x63, 0x7c, 0x77, 0x7b, 0xf2, 0x6b, 0x6f, 0xc5, 0x30, 0x01, 0x67, 0x2b, 0xfe, 0xd7, 0xab, 0x76,0xca, 0x82, 0xc9, 0x7d, 0xfa, 0x59, 0x47, 0xf0, 0xad, 0xd4, 0xa2, 0xaf, 0x9c, 0xa4, 0x72, 0xc0,0xb7, 0xfd, 0x93, 0x26, 0x36, 0x3f, 0xf7, 0xcc, 0x34, 0xa5, 0xe5, 0xf1, 0x71, 0xd8, 0x31, 0x15,0x04, 0xc7, 0x23, 0xc3, 0x18, 0x96, 0x05, 0x9a, 0x07, 0x12, 0x80, 0xe2, 0xeb, 0x27, 0xb2, 0x75,0x09, 0x83, 0x2c, 0x1a, 0x1b, 0x6e, 0x5a, 0xa0, 0x52, 0x3b, 0xd6, 0xb3, 0x29, 0xe3, 0x2f, 0x84,0x53, 0xd1, 0x00, 0xed, 0x20, 0xfc, 0xb1, 0x5b, 0x6a, 0xcb, 0xbe, 0x39, 0x4a, 0x4c, 0x58, 0xcf,0xd0, 0xef, 0xaa, 0xfb, 0x43, 0x4d, 0x33, 0x85, 0x45, 0xf9, 0x02, 0x7f, 0x50, 0x3c, 0x9f, 0xa8,0x51, 0xa3, 0x40, 0x8f, 0x92, 0x9d, 0x38, 0xf5, 0xbc, 0xb6, 0xda, 0x21, 0x10, 0xff, 0xf3, 0xd2,0xcd, 0x0c, 0x13, 0xec, 0x5f, 0x97, 0x44, 0x17, 0xc4, 0xa7, 0x7e, 0x3d, 0x64, 0x5d, 0x19, 0x73,0x60, 0x81, 0x4f, 0xdc, 0x22, 0x2a, 0x90, 0x88, 0x46, 0xee, 0xb8, 0x14, 0xde, 0x5e, 0x0b, 0xdb,0xe0, 0x32, 0x3a, 0x0a, 0x49, 0x06, 0x24, 0x5c, 0xc2, 0xd3, 0xac, 0x62, 0x91, 0x95, 0xe4, 0x79,0xe7, 0xc8, 0x37, 0x6d, 0x8d, 0xd5, 0x4e, 0xa9, 0x6c, 0x56, 0xf4, 0xea, 0x65, 0x7a, 0xae, 0x08,0xba, 0x78, 0x25, 0x2e, 0x1c, 0xa6, 0xb4, 0xc6, 0xe8, 0xdd, 0x74, 0x1f, 0x4b, 0xbd, 0x8b, 0x8a,0x70, 0x3e, 0xb5, 0x66, 0x48, 0x03, 0xf6, 0x0e, 0x61, 0x35, 0x57, 0xb9, 0x86, 0xc1, 0x1d, 0x9e,0xe1, 0xf8, 0x98, 0x11, 0x69, 0xd9, 0x8e, 0x94, 0x9b, 0x1e, 0x87, 0xe9, 0xce, 0x55, 0x28, 0xdf,0x8c, 0xa1, 0x89, 0x0d, 0xbf, 0xe6, 0x42, 0x68, 0x41, 0x99, 0x2d, 0x0f, 0xb0, 0x54, 0xbb, 0x16);protected static $Rcon = array(array(0x00, 0x00, 0x00, 0x00),array(0x01, 0x00, 0x00, 0x00),array(0x02, 0x00, 0x00, 0x00),array(0x04, 0x00, 0x00, 0x00),array(0x08, 0x00, 0x00, 0x00),array(0x10, 0x00, 0x00, 0x00),array(0x20, 0x00, 0x00, 0x00),array(0x40, 0x00, 0x00, 0x00),array(0x80, 0x00, 0x00, 0x00),array(0x1b, 0x00, 0x00, 0x00),array(0x36, 0x00, 0x00, 0x00));protected static $passwords = array();private static $pbkdf2Algorithm = 'sha1';private static $pbkdf2Iterations = 1000;private static $pbkdf2UseStaticSalt = 0;private static $pbkdf2StaticSalt = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";public static function AESEncryptCtr($plaintext, $password, $nBits){$blockSize = 16;  if (!($nBits == 128 || $nBits == 192 || $nBits == 256)){return '';}  $nBytes  = $nBits / 8;  $pwBytes = array();for ($i = 0; $i < $nBytes; $i++){$pwBytes[$i] = ord(substr($password, $i, 1)) & 0xff;}$key = self::Cipher($pwBytes, self::KeyExpansion($pwBytes));$key = array_merge($key, array_slice($key, 0, $nBytes - 16));  $counterBlock = array();$nonce        = floor(microtime(true) * 1000);   $nonceSec     = floor($nonce / 1000);$nonceMs      = $nonce % 1000;for ($i = 0; $i < 4; $i++){$counterBlock[$i] = self::urs($nonceSec, $i * 8) & 0xff;}for ($i = 0; $i < 4; $i++){$counterBlock[$i + 4] = $nonceMs & 0xff;}$ctrTxt = '';for ($i = 0; $i < 8; $i++){$ctrTxt .= chr($counterBlock[$i]);}$keySchedule = self::KeyExpansion($key);$blockCount = ceil(strlen($plaintext) / $blockSize);$ciphertxt  = array();  for ($b = 0; $b < $blockCount; $b++){for ($c = 0; $c < 4; $c++){$counterBlock[15 - $c] = self::urs($b, $c * 8) & 0xff;}for ($c = 0; $c < 4; $c++){$counterBlock[15 - $c - 4] = self::urs($b / 0x100000000, $c * 8);}$cipherCntr = self::Cipher($counterBlock, $keySchedule);  $blockLength = $b < $blockCount - 1 ? $blockSize : (strlen($plaintext) - 1) % $blockSize + 1;$cipherByte  = array();for ($i = 0; $i < $blockLength; $i++){  $cipherByte[$i] = $cipherCntr[$i] ^ ord(substr($plaintext, $b * $blockSize + $i, 1));$cipherByte[$i] = chr($cipherByte[$i]);}$ciphertxt[$b] = implode('', $cipherByte);  }$ciphertext = $ctrTxt . implode('', $ciphertxt);$ciphertext = base64_encode($ciphertext);return $ciphertext;}protected static function Cipher($input, $w){    $Nb = 4;                 $Nr = count($w) / $Nb - 1; $state = array();  for ($i = 0; $i < 4 * $Nb; $i++){$state[$i % 4][floor($i / 4)] = $input[$i];}$state = self::AddRoundKey($state, $w, 0, $Nb);for ($round = 1; $round < $Nr; $round++){  $state = self::SubBytes($state, $Nb);$state = self::ShiftRows($state, $Nb);$state = self::MixColumns($state);$state = self::AddRoundKey($state, $w, $round, $Nb);}$state = self::SubBytes($state, $Nb);$state = self::ShiftRows($state, $Nb);$state = self::AddRoundKey($state, $w, $Nr, $Nb);$output = array(4 * $Nb);  for ($i = 0; $i < 4 * $Nb; $i++){$output[$i] = $state[$i % 4][floor($i / 4)];}return $output;}protected static function AddRoundKey($state, $w, $rnd, $Nb){  for ($r = 0; $r < 4; $r++){for ($c = 0; $c < $Nb; $c++){$state[$r][$c] ^= $w[$rnd * 4 + $c][$r];}}return $state;}protected static function SubBytes($s, $Nb){    for ($r = 0; $r < 4; $r++){for ($c = 0; $c < $Nb; $c++){$s[$r][$c] = self::$Sbox[$s[$r][$c]];}}return $s;}protected static function ShiftRows($s, $Nb){    $t = array(4);for ($r = 1; $r < 4; $r++){for ($c = 0; $c < 4; $c++){$t[$c] = $s[$r][($c + $r) % $Nb];}  for ($c = 0; $c < 4; $c++){$s[$r][$c] = $t[$c];}         }          return $s;  }protected static function MixColumns($s){for ($c = 0; $c < 4; $c++){$a = array(4);  $b = array(4);  for ($i = 0; $i < 4; $i++){$a[$i] = $s[$i][$c];$b[$i] = $s[$i][$c] & 0x80 ? $s[$i][$c] << 1 ^ 0x011b : $s[$i][$c] << 1;}$s[0][$c] = $b[0] ^ $a[1] ^ $b[1] ^ $a[2] ^ $a[3]; $s[1][$c] = $a[0] ^ $b[1] ^ $a[2] ^ $b[2] ^ $a[3]; $s[2][$c] = $a[0] ^ $a[1] ^ $b[2] ^ $a[3] ^ $b[3]; $s[3][$c] = $a[0] ^ $b[0] ^ $a[1] ^ $a[2] ^ $b[3]; }return $s;}protected static function KeyExpansion($key){$Nb = 4;$Nk = (int) (count($key) / 4);$Nr = $Nk + 6;$w    = array();$temp = array();for ($i = 0; $i < $Nk; $i++){$r     = array($key[4 * $i], $key[4 * $i + 1], $key[4 * $i + 2], $key[4 * $i + 3]);$w[$i] = $r;}for ($i = $Nk; $i < ($Nb * ($Nr + 1)); $i++){$w[$i] = array();for ($t = 0; $t < 4; $t++){$temp[$t] = $w[$i - 1][$t];}if ($i % $Nk == 0){$temp = self::SubWord(self::RotWord($temp));for ($t = 0; $t < 4; $t++){$rConIndex = (int) ($i / $Nk);$temp[$t] ^= self::$Rcon[$rConIndex][$t];}}else if ($Nk > 6 && $i % $Nk == 4){$temp = self::SubWord($temp);}for ($t = 0; $t < 4; $t++){$w[$i][$t] = $w[$i - $Nk][$t] ^ $temp[$t];}}return $w;}protected static function SubWord($w){    for ($i = 0; $i < 4; $i++){$w[$i] = self::$Sbox[$w[$i]];}return $w;}protected static function RotWord($w){    $tmp = $w[0];for ($i = 0; $i < 3; $i++){$w[$i] = $w[$i + 1];}$w[3] = $tmp;return $w;}protected static function urs($a, $b){$a &= 0xffffffff;$b &= 0x1f;  if ($a & 0x80000000 && $b > 0){   $a = ($a >> 1) & 0x7fffffff;   $a = $a >> ($b - 1);           }else{                       $a = ($a >> $b);               }return $a;}public static function AESDecryptCtr($ciphertext, $password, $nBits){$blockSize = 16;  if (!($nBits == 128 || $nBits == 192 || $nBits == 256)){return '';}$ciphertext = base64_decode($ciphertext);$nBytes  = $nBits / 8;  $pwBytes = array();for ($i = 0; $i < $nBytes; $i++){$pwBytes[$i] = ord(substr($password, $i, 1)) & 0xff;}$key = self::Cipher($pwBytes, self::KeyExpansion($pwBytes));$key = array_merge($key, array_slice($key, 0, $nBytes - 16));  $counterBlock = array();$ctrTxt       = substr($ciphertext, 0, 8);for ($i = 0; $i < 8; $i++){$counterBlock[$i] = ord(substr($ctrTxt, $i, 1));}$keySchedule = self::KeyExpansion($key);$nBlocks = ceil((strlen($ciphertext) - 8) / $blockSize);$ct      = array();for ($b = 0; $b < $nBlocks; $b++){$ct[$b] = substr($ciphertext, 8 + $b * $blockSize, 16);}$ciphertext = $ct;  $plaintxt = array();for ($b = 0; $b < $nBlocks; $b++){for ($c = 0; $c < 4; $c++){$counterBlock[15 - $c] = self::urs($b, $c * 8) & 0xff;}for ($c = 0; $c < 4; $c++){$counterBlock[15 - $c - 4] = self::urs(($b + 1) / 0x100000000 - 1, $c * 8) & 0xff;}$cipherCntr = self::Cipher($counterBlock, $keySchedule);  $plaintxtByte = array();for ($i = 0; $i < strlen($ciphertext[$b]); $i++){$plaintxtByte[$i] = $cipherCntr[$i] ^ ord(substr($ciphertext[$b], $i, 1));$plaintxtByte[$i] = chr($plaintxtByte[$i]);}$plaintxt[$b] = implode('', $plaintxtByte);}$plaintext = implode('', $plaintxt);return $plaintext;}public static function AESDecryptCBC($ciphertext, $password){$adapter = self::getAdapter();if (!$adapter->isSupported()){return false;}$data_size = unpack('V', substr($ciphertext, -4));$salt             = substr($ciphertext, -92, 68);$rightStringLimit = -4;$params        = self::getKeyDerivationParameters();$keySizeBytes  = $params['keySize'];$algorithm     = $params['algorithm'];$iterations    = $params['iterations'];$useStaticSalt = $params['useStaticSalt'];if (substr($salt, 0, 4) == 'JPST'){$salt             = substr($salt, 4);$rightStringLimit -= 68;$key          = self::pbkdf2($password, $salt, $algorithm, $iterations, $keySizeBytes);}elseif ($useStaticSalt){$key = self::getStaticSaltExpandedKey($password);}else{$key = self::expandKey($password);}$iv               = substr($ciphertext, -24, 20);if (substr($iv, 0, 4) == 'JPIV'){$iv               = substr($iv, 4);$rightStringLimit -= 20;}else{$iv = self::createTheWrongIV($password);}$plaintext = $adapter->decrypt($iv . substr($ciphertext, 0, $rightStringLimit), $key);if (strlen($plaintext) > $data_size){$plaintext = substr($plaintext, 0, $data_size);}return $plaintext;}public static function createTheWrongIV($password){static $ivs = array();$key = md5($password);if (!isset($ivs[$key])){$nBytes  = 16;  $pwBytes = array();for ($i = 0; $i < $nBytes; $i++){$pwBytes[$i] = ord(substr($password, $i, 1)) & 0xff;}$iv    = self::Cipher($pwBytes, self::KeyExpansion($pwBytes));$newIV = '';foreach ($iv as $int){$newIV .= chr($int);}$ivs[$key] = $newIV;}return $ivs[$key];}public static function expandKey($password){$nBits     = 128;$lookupKey = md5($password . '-' . $nBits);if (array_key_exists($lookupKey, self::$passwords)){$key = self::$passwords[$lookupKey];return $key;}$nBytes  = $nBits / 8; $pwBytes = array();for ($i = 0; $i < $nBytes; $i++){$pwBytes[$i] = ord(substr($password, $i, 1)) & 0xff;}$key    = self::Cipher($pwBytes, self::KeyExpansion($pwBytes));$key    = array_merge($key, array_slice($key, 0, $nBytes - 16)); $newKey = '';foreach ($key as $int){$newKey .= chr($int);}$key = $newKey;self::$passwords[$lookupKey] = $key;return $key;}public static function getAdapter(){static $adapter = null;if (is_object($adapter) && ($adapter instanceof AKEncryptionAESAdapterInterface)){return $adapter;}$adapter = new OpenSSL();if (!$adapter->isSupported()){$adapter = new Mcrypt();}return $adapter;}public static function getPbkdf2Algorithm(){return self::$pbkdf2Algorithm;}public static function setPbkdf2Algorithm($pbkdf2Algorithm){self::$pbkdf2Algorithm = $pbkdf2Algorithm;}public static function getPbkdf2Iterations(){return self::$pbkdf2Iterations;}public static function setPbkdf2Iterations($pbkdf2Iterations){self::$pbkdf2Iterations = $pbkdf2Iterations;}public static function getPbkdf2UseStaticSalt(){return self::$pbkdf2UseStaticSalt;}public static function setPbkdf2UseStaticSalt($pbkdf2UseStaticSalt){self::$pbkdf2UseStaticSalt = $pbkdf2UseStaticSalt;}public static function getPbkdf2StaticSalt(){return self::$pbkdf2StaticSalt;}public static function setPbkdf2StaticSalt($pbkdf2StaticSalt){self::$pbkdf2StaticSalt = $pbkdf2StaticSalt;}public static function getKeyDerivationParameters(){return array('keySize'       => 16,'algorithm'     => self::$pbkdf2Algorithm,'iterations'    => self::$pbkdf2Iterations,'useStaticSalt' => self::$pbkdf2UseStaticSalt,'staticSalt'    => self::$pbkdf2StaticSalt,);}public static function pbkdf2($password, $salt, $algorithm = 'sha1', $count = 1000, $key_length = 16){if (function_exists("hash_pbkdf2")){return hash_pbkdf2($algorithm, $password, $salt, $count, $key_length, true);}$hash_length = akstringlen(hash($algorithm, "", true));$block_count = ceil($key_length / $hash_length);$output = "";for ($i = 1; $i <= $block_count; $i++){$last = $salt . pack("N", $i);$xorResult = hash_hmac($algorithm, $last, $password, true);$last      = $xorResult;for ($j = 1; $j < $count; $j++){$last = hash_hmac($algorithm, $last, $password, true);$xorResult ^= $last;}$output .= $xorResult;}return aksubstr($output, 0, $key_length);}private static function getStaticSaltExpandedKey($password){$params        = self::getKeyDerivationParameters();$keySizeBytes  = $params['keySize'];$algorithm     = $params['algorithm'];$iterations    = $params['iterations'];$staticSalt    = $params['staticSalt'];$lookupKey = "PBKDF2-$algorithm-$iterations-" . md5($password . $staticSalt);if (!array_key_exists($lookupKey, self::$passwords)){self::$passwords[$lookupKey] = self::pbkdf2($password, $staticSalt, $algorithm, $iterations, $keySizeBytes);}return self::$passwords[$lookupKey];}}

function masterSetup(){$ini_data = null;if (!defined('KICKSTART')){$setupFile = 'restoration.php';if (!file_exists($setupFile)){AKFactory::set('kickstart.enabled', false);return false;}require_once $setupFile;$ini_data = $restoration_setup;if (empty($ini_data)){AKFactory::set('kickstart.enabled', false);return false;}AKFactory::set('kickstart.enabled', true);}else{global $restoration_setup;if (!empty($restoration_setup) && !is_array($restoration_setup)){$ini_data = AKText::parse_ini_file($restoration_setup, false, true);}elseif (is_array($restoration_setup)){$ini_data = $restoration_setup;}}if (!empty($ini_data)){foreach ($ini_data as $key => $value){AKFactory::set($key, $value);}AKFactory::set('kickstart.enabled', true);}$ini_data = null;$json = getQueryParam('json', null);if (!empty($_REQUEST)){foreach ($_REQUEST as $key => $value){unset($_REQUEST[$key]);}}if (!empty($_POST)){foreach ($_POST as $key => $value){unset($_POST[$key]);}}if (!empty($_GET)){foreach ($_GET as $key => $value){unset($_GET[$key]);}}$password = AKFactory::get('kickstart.security.password', null);if (!empty($json)){if (!empty($password)){$json = AKEncryptionAES::AESDecryptCtr($json, $password, 128);if (empty($json)){die('###{"status":false,"message":"Invalid login"}###');}}$raw = json_decode($json, true);if (!empty($password) && (empty($raw))){die('###{"status":false,"message":"Invalid login"}###');}if (!empty($raw)){foreach ($raw as $key => $value){$_REQUEST[$key] = $value;}}}elseif (!empty($password)){die('###{"status":false,"message":"Invalid login"}###');}$serialized = getQueryParam('factory', null);if (!is_null($serialized)){AKFactory::unserialize($serialized);AKFactory::set('kickstart.enabled', true);return true;}if (defined('KICKSTART')){$configuration = getQueryParam('configuration');if (!is_null($configuration)){$ini_data = json_decode($configuration, true);}else{$ini_data = array('kickstart.enabled' => true);}if (!empty($ini_data)){foreach ($ini_data as $key => $value){AKFactory::set($key, $value);}AKFactory::set('kickstart.enabled', true);return true;}}}

if (!defined('KICKSTART')){class RestorationObserver extends AKAbstractPartObserver{public $compressedTotal = 0;public $uncompressedTotal = 0;public $filesProcessed = 0;public function update($object, $message){if (!is_object($message)){return;}if (!array_key_exists('type', get_object_vars($message))){return;}if ($message->type == 'startfile'){$this->filesProcessed++;$this->compressedTotal += $message->content->compressed;$this->uncompressedTotal += $message->content->uncompressed;}}public function __toString(){return __CLASS__;}}masterSetup();$retArray = array('status'  => true,'message' => null);$enabled = AKFactory::get('kickstart.enabled', false);if ($enabled){$task = getQueryParam('task');switch ($task){case 'ping':$timer = AKFactory::getTimer();$timer->enforce_min_exec_time();break;case 'startRestore':AKFactory::nuke(); case 'stepRestore':$engine   = AKFactory::getUnarchiver(); $observer = new RestorationObserver(); $engine->attach($observer); $engine->tick();$ret = $engine->getStatusArray();if ($ret['Error'] != ''){$retArray['status']  = false;$retArray['done']    = true;$retArray['message'] = $ret['Error'];}elseif (!$ret['HasRun']){$retArray['files']    = $observer->filesProcessed;$retArray['bytesIn']  = $observer->compressedTotal;$retArray['bytesOut'] = $observer->uncompressedTotal;$retArray['status']   = true;$retArray['done']     = true;}else{$retArray['files']    = $observer->filesProcessed;$retArray['bytesIn']  = $observer->compressedTotal;$retArray['bytesOut'] = $observer->uncompressedTotal;$retArray['status']   = true;$retArray['done']     = false;$retArray['factory']  = AKFactory::serialize();}break;case 'finalizeRestore':$root = AKFactory::get('kickstart.setup.destdir');recursive_remove_directory($root . '/installation');$postproc = AKFactory::getPostProc();if (file_exists($root . '/htaccess.bak')){if (file_exists($root . '/.htaccess')){$postproc->unlink($root . '/.htaccess');}$postproc->rename($root . '/htaccess.bak', $root . '/.htaccess');}if (file_exists($root . '/web.config.bak')){if (file_exists($root . '/web.config')){$postproc->unlink($root . '/web.config');}$postproc->rename($root . '/web.config.bak', $root . '/web.config');}$basepath = KSROOTDIR;$basepath = rtrim(str_replace('\\', '/', $basepath), '/');if (!empty($basepath)){$basepath .= '/';}$postproc->unlink($basepath . 'restoration.php');$filename = dirname(__FILE__) . '/restore_finalisation.php';if (file_exists($filename)){if (function_exists('opcache_invalidate')){opcache_invalidate($filename);}if (function_exists('apc_compile_file')){apc_compile_file($filename);}if (function_exists('wincache_refresh_if_changed')){wincache_refresh_if_changed(array($filename));}if (function_exists('xcache_asm')){xcache_asm($filename);}include_once $filename;}if (function_exists('finalizeRestore')){finalizeRestore($root, $basepath);}break;default:$enabled = false;break;}}if (!$enabled){$retArray['status']  = false;$retArray['message'] = AKText::_('ERR_INVALID_LOGIN');}$json = json_encode($retArray);$password = AKFactory::get('kickstart.security.password', null);if (!empty($password)){$json = AKEncryptionAES::AESEncryptCtr($json, $password, 128);}echo "###$json###";}function recursive_remove_directory($directory){if (substr($directory, -1) == '/'){$directory = substr($directory, 0, -1);}if (!file_exists($directory) || !is_dir($directory)){return false;}elseif (!is_readable($directory)){return false;}else{$handle   = opendir($directory);$postproc = AKFactory::getPostProc();while (false !== ($item = readdir($handle))){if ($item != '.' && $item != '..'){$path = $directory . '/' . $item;if (is_dir($path)){recursive_remove_directory($path);}else{$postproc->unlink($path);}}}closedir($handle);if (!$postproc->rmdir($directory)){return false;}return true;}}

class AKKickstartUtils{public static function getBestArchivePath(){$basePath      = self::getPath();$basePathSlash = (empty($basePath) ? '.' : rtrim($basePath, '/\\')) . '/';$paths = array($basePath,$basePath . '/kicktemp',$basePathSlash . 'administrator/components/com_akeeba/backup',$basePathSlash . 'backups',$basePathSlash . 'wp-content/plugins/akeebabackupwp/app/backups',);foreach ($paths as $path){$archives = self::findArchives($path);if (!empty($archives)){return $path;}}return $basePath;}public static function getPath(){$path = KSROOTDIR;$path = rtrim(str_replace('\\', '/', $path), '/');if (!empty($path)){$path .= '/';}return $path;}public static function findArchives($path){$ret = array();if (empty($path)){$path = self::getPath();}if (empty($path)){$path = '.';}$dh = @opendir($path);if ($dh === false){return $ret;}while (false !== $file = @readdir($dh)){$dotpos = strrpos($file, '.');if ($dotpos === false){continue;}if ($dotpos == strlen($file)){continue;}$extension = strtolower(substr($file, $dotpos + 1));if (in_array($extension, array('jpa', 'zip', 'jps'))){$ret[] = $file;}}closedir($dh);if (!empty($ret)){return $ret;}$d = dir($path);while (false != ($file = $d->read())){$dotpos = strrpos($file, '.');if ($dotpos === false){continue;}if ($dotpos == strlen($file)){continue;}$extension = strtolower(substr($file, $dotpos + 1));if (in_array($extension, array('jpa', 'zip', 'jps'))){$ret[] = $file;}}return $ret;}public static function getTemporaryPath(){$path = self::getPath();$candidateDirs = array($path,$path . '/kicktemp',);if (function_exists('sys_get_temp_dir')){$candidateDirs[] = sys_get_temp_dir();}foreach ($candidateDirs as $dir){if (is_dir($dir) && is_writable($dir)){return $dir;}}return $path;}public static function getArchivesAsOptions($path = null){$ret = '';$archives = self::findArchives($path);if (empty($archives)){return $ret;}foreach ($archives as $file){$ret .= '<option value="' . $file . '">' . $file . '</option>' . "\n";}return $ret;}}

class ExtractionObserver extends AKAbstractPartObserver{public $compressedTotal = 0;public $uncompressedTotal = 0;public $filesProcessed = 0;public $totalSize = null;public $fileList = null;public $lastFile = '';public function update($object, $message){if (!is_object($message)){return;}if (!array_key_exists('type', get_object_vars($message))){return;}if ($message->type == 'startfile'){$this->lastFile = $message->content->file;$this->filesProcessed++;$this->compressedTotal += $message->content->compressed;$this->uncompressedTotal += $message->content->uncompressed;}elseif ($message->type == 'totalsize'){$this->totalSize = $message->content->totalsize;$this->fileList  = $message->content->filelist;}}public function __toString(){return __CLASS__;}}

function callExtraFeature($method = null, array $params = array()){static $extraFeatureObjects = null;if (!is_array($extraFeatureObjects)){$extraFeatureObjects = array();$allClasses          = get_declared_classes();foreach ($allClasses as $class){if (substr($class, 0, 9) == 'AKFeature'){$extraFeatureObjects[] = new $class;}}}if (is_null($method)){return;}if (empty($extraFeatureObjects)){return;}$result = null;foreach ($extraFeatureObjects as $o){if (!method_exists($o, $method)){continue;}$result = call_user_func(array($o, $method), $params);}return $result;}

function TranslateWinPath($p_path){$is_unc = false;if (KSWINDOWS){$is_unc = (substr($p_path, 0, 2) == '\\\\') || (substr($p_path, 0, 2) == '//');if ((strpos($p_path, '\\') > 0) || (substr($p_path, 0, 1) == '\\')){$p_path = strtr($p_path, '\\', '/');}}$p_path = str_replace('///', '/', $p_path);$p_path = str_replace('//', '/', $p_path);if ($is_unc){$p_path = '//' . ltrim($p_path, '/');}return $p_path;}function getListing($directory, $host, $port, $username, $password, $passive, $ssl){$directory = resolvePath($directory);$dir       = $directory;$parsed_dir = trim($dir, '/');$parts      = empty($parsed_dir) ? array() : explode('/', $parsed_dir);if (!empty($parts)){$copy_of_parts = $parts;array_pop($copy_of_parts);if (!empty($copy_of_parts)){$parent_directory = '/' . implode('/', $copy_of_parts);}else{$parent_directory = '/';}}else{$parent_directory = '';}if ($ssl){$con = @ftp_ssl_connect($host, $port);}else{$con = @ftp_connect($host, $port);}if ($con === false){return array('error' => 'FTPBROWSER_ERROR_HOSTNAME');}$result = @ftp_login($con, $username, $password);if ($result === false){return array('error' => 'FTPBROWSER_ERROR_USERPASS');}@ftp_pasv($con, $passive);if (!empty($dir)){$result = @ftp_chdir($con, $dir);if ($result === false){return array('error' => 'FTPBROWSER_ERROR_NOACCESS');}}else{$directory = @ftp_pwd($con);$parsed_dir       = trim($directory, '/');$parts            = empty($parsed_dir) ? array() : explode('/', $parsed_dir);$parent_directory = $this->directory;}$list = @ftp_rawlist($con, '.');ftp_close($con);if ($list === false){return array('error' => 'FTPBROWSER_ERROR_UNSUPPORTED');}$folders = parse_rawlist($list);return array('error'       => '','list'        => $folders,'breadcrumbs' => $parts,'directory'   => $directory,'parent'      => $parent_directory);}function parse_rawlist($list){$folders = array();foreach ($list as $v){$info  = array();$vinfo = preg_split("/[\s]+/", $v, 9);if ($vinfo[0] !== "total"){$perms = $vinfo[0];if (substr($perms, 0, 1) == 'd'){$folders[] = $vinfo[8];}}}asort($folders);return $folders;}function getSftpListing($directory, $host, $port, $username, $password){$directory = resolvePath($directory);$dir       = $directory;$parsed_dir = trim($dir, '/');$parts      = empty($parsed_dir) ? array() : explode('/', $parsed_dir);if (!empty($parts)){$copy_of_parts = $parts;array_pop($copy_of_parts);if (!empty($copy_of_parts)){$parent_directory = '/' . implode('/', $copy_of_parts);}else{$parent_directory = '/';}}else{$parent_directory = '';}$connection = null;$sftphandle = null;if (!function_exists('ssh2_connect')){return array('error' => AKText::_('SFTP_NO_SSH2'));}$connection = ssh2_connect($host, $port);if ($connection === false){return array('error' => AKText::_('SFTP_WRONG_USER'));}if (!ssh2_auth_password($connection, $username, $password)){return array('error' => AKText::_('SFTP_WRONG_USER'));}$sftphandle = ssh2_sftp($connection);if ($sftphandle === false){return array('error' => AKText::_('SFTP_NO_FTP_SUPPORT'));}$list = array();$dir  = ltrim($dir, '/');if (empty($dir)){$dir       = ssh2_sftp_realpath($sftphandle, ".");$directory = $dir;$parsed_dir = trim($dir, '/');$parts      = empty($parsed_dir) ? array() : explode('/', $parsed_dir);if (!empty($parts)){$copy_of_parts = $parts;array_pop($copy_of_parts);if (!empty($copy_of_parts)){$parent_directory = '/' . implode('/', $copy_of_parts);}else{$parent_directory = '/';}}else{$parent_directory = '';}}$handle = opendir("ssh2.sftp://$sftphandle/$dir");if (!is_resource($handle)){return array('error' => AKText::_('SFTPBROWSER_ERROR_NOACCESS'));}while (($entry = readdir($handle)) !== false){if (!is_dir("ssh2.sftp://$sftphandle/$dir/$entry")){continue;}$list[] = $entry;}closedir($handle);if (!empty($list)){asort($list);}return array('error'       => '','list'        => $list,'breadcrumbs' => $parts,'directory'   => $directory,'parent'      => $parent_directory);}function resolvePath($filename){$filename = str_replace('//', '/', $filename);$parts    = explode('/', $filename);$out      = array();foreach ($parts as $part){if ($part == '.'){continue;}if ($part == '..'){array_pop($out);continue;}$out[] = $part;}return implode('/', $out);}function createStealthURL(){$filename = AKFactory::get('kickstart.stealth.url', '');if (empty($filename)){return;}$filename = basename($filename);if ((strtolower(substr($filename, -5)) != '.html') && (strtolower(substr($filename, -4)) != '.htm')){return;}$filename_quoted = str_replace('.', '\\.', $filename);$rewrite_base    = trim(dirname(AKFactory::get('kickstart.stealth.url', '')), '/');$userIP = $_SERVER['REMOTE_ADDR'];$userIP = str_replace('.', '\.', $userIP);$stealthHtaccess = <<<ENDHTACCESS
RewriteEngine On
RewriteBase /$rewrite_base
RewriteCond %{REMOTE_ADDR}		!$userIP
RewriteCond %{REQUEST_URI}		!$filename_quoted
RewriteCond %{REQUEST_URI}		!(\.png|\.jpg|\.gif|\.jpeg|\.bmp|\.swf|\.css|\.js)$
RewriteRule (.*)				$filename	[R=307,L]

ENDHTACCESS;
$postproc = AKFactory::getpostProc();$postproc->unlink('.htaccess');$tempfile = $postproc->processFilename('.htaccess');@file_put_contents($tempfile, $stealthHtaccess);$postproc->process();}

function echoCSS(){echo <<<CSS

html {
    background: #f0f0f0;
    font-size: 62.5%;
}

body {
	font-size: 12pt;
    font-family: Calibri, "Helvetica Neue", Helvetica, Arial, sans-serif;
	text-rendering: optimizeLegibility;
	background: transparent;
	color:#555;
	width:100%;
	max-width:980px;
	margin: 0 auto;
}

#page-container {
	position:relative;
	margin:5% 0;
	background: #f9f9f9;
	border: medium solid #ddd;
}

#header {
	color: #555;
	background: #eaeaea;
	background-clip: padding-box;
	margin-bottom: 0.7em;
	border-bottom: 2px solid #ddd;
	padding:.25em;
    font-size: 24pt;
	line-height: 1.2;
	text-align: center;
}

#footer {
	font-size: 9pt;
	color: #999;
	text-align: center;
	border-top: 1px solid #ddd;
	padding: 1em 1em;
	background: #eaeaea;
	clear: both;
}

#footer a {
	color: #88a;
	text-decoration: none;
}

#error, .error {
	x-display: none;
	border: solid #cc0000;
	border-width: 4px 0;
	background: rgb(255,255,136);
	color: #990000;
	padding:1em 2em;
	margin-bottom: 1.15em;
	text-align:center;
}

#error h3, .error h3 {
	margin: 0;
	padding: 0;
	font-size: 12pt;
}

.clr {
	clear: both;
}

.circle {
	display: block;
	float: left;
	border-radius: 2em;
	border: 2px solid #e5e5e5;
	font-weight: bold;
	font-size: 14pt;
	line-height:1.5em;
	color: #fff;
	height: 1.5em;
	width: 1.5em;
    margin: 0.75em;
    text-align: center;
    background: rgb(35,83,138);
}

.area-container {
	margin: 1em 2em;
}

#page2a .area-container {
	margin: 1em 0;
}

#runInstaller,
#runCleanup,
#gotoSite,
#gotoAdministrator,
#gotoPostRestorationRroubleshooting {
    margin: 0 2em 1.3em;
}

h2 {
	font-size: 18pt;
	font-weight: normal;
    line-height: 1.3;
	border: solid #ddd;
	border-left:none;
	border-right:none;
	padding: 0.5em 0;
    background: #eaeaea;
}

#preextraction h2 {
	margin-top:0;
	border-top:0;
	text-align:center;
}

input,
select,
textarea {
    font-size : 100%;
    margin : 0;
    vertical-align : baseline;
    *vertical-align: middle;
}

button,
input {
    line-height : normal;
	font-weight:normal;
    *overflow: visible;
}

input,
select,
textarea {
	background:#fff;
	color:#777;
	font-size: 12pt;
	border:1px solid #d5d5d5;
    border-radius: .25em;
    box-sizing: border-box;
	width:50%;
	padding:0 0 0 .5em;
}

input[type="checkbox"] {
	width:auto;
}

.field {
	height:1.5em;
}

label {
	display:inline-block;
	width:30%;
	font-size: 95%;
    font-weight: normal;
    cursor : pointer;
	color: #333;
	margin:.5em 0;
}

input:focus, input:hover {
	background-color: #f6f6ff;
}

.button {
	display: inline-block;
	margin: 1em .25em;
	padding: 1em 2em;
	background: #2cb12c;
	color:#fff;
	border: 1px solid #ccc;
	cursor: pointer;
	border-radius: .25em;
  	transition: 0.3s linear all;
}

#checkFTPTempDir.button,
#resetFTPTempDir.button,
#testFTP.button,
#browseFTP,
#reloadArchives,
#notWorking.button {
	padding: .5em 1em;
}

.button:hover {
	background: #259625;
}

.button:active {
	background: #3c3;
	border: 1px solid #eaeaea;
	border-radius: .25em;
}

#notWorking.button, .bluebutton {
	text-decoration: none;
	background: #66a8ff;
}
#notWorking.button:hover, .bluebutton:hover {
	background: #4096ee;
}
#notWorking.button:active, .bluebutton:active {
	background: #7abcff;
}

.loprofile {
	padding: 0.5em 1em;
	font-size: 80%;
}

.black_overlay{
	display: none;
	position: absolute;
	top: 0%;
	left: 0%;
	width: 100%;
	height: 100%;
	background-color: black;
	z-index:1001;
	-moz-opacity: 0.8;
	opacity:.80;
	filter: alpha(opacity=80);
}

.white_content {
	display: none;
	position: absolute;
	padding: 0 0 1em;
	background: #fff;
	border: 1px solid #ddd;
	border: 1px solid rgba(0,0,0,.3);
	z-index:1002;
	overflow: hidden;
}
.white_content a{
	margin-left:4em;
}
ol {
	margin:0 2em;
	padding:0 2em 1em;
}
li {
	margin : 0 0 .5em;
}

#genericerror {
	background-color: #f0f000 !important;
	border: 4px solid #fcc !important;
}

#genericerrorInner {
	font-size: 110%;
	color: #33000;
}

#warn-not-close, .warn-not-close {
	padding: 0.2em 0.5em;
	text-align: center;
	background: #fcfc00;
	font-size: smaller;
	font-weight: bold;
}

#progressbar, .progressbar {
	display: block;
	width: 80%;
	height: 32px;
	border: 1px solid #ccc;
	margin: 1em 10% 0.2em;
	border-radius: .25em;
}

#progressbar-inner, .progressbar-inner {
	display: block;
	width: 100%;
	height: 100%;
	background: #4096ee;
}

#currentFile {
	font-family: Consolas, "Courier New", Courier, monospace;
	font-size: 9pt;
	height: 10pt;
	overflow: hidden;
	text-overflow: ellipsis;
	background: #ccc;
	margin: 0 10% 1em;
	padding:.125em;
}

#extractionComplete {
}

#warningsContainer {
	border-bottom: 2px solid brown;
	border-left: 2px solid brown;
	border-right: 2px solid brown;
	padding: 5px 0;
	background: #ffffcc;
	border-bottom-right-radius: 5px;
	border-bottom-left-radius: 5px;
}

#warningsHeader h2 {
	color: black;
	border-top: 2px solid brown;
	border-left: 2px solid brown;
	border-right: 2px solid brown;
	border-bottom: thin solid brown;
	border-top-right-radius: 5px;
	border-top-left-radius: 5px;
	background: yellow;
	font-size: large;
	padding: 2px 5px;
	margin: 0px;
}

#warnings {
	height: 200px;
	overflow-y: scroll;
}

#warnings div {
	background: #eeeeee;
	font-size: small;
	padding: 2px 4px;
	border-bottom: thin solid #333333;
}

#automode {
	display: inline-block;
	padding: 6pt 12pt;
	background-color: #cc0000;
	border: thick solid yellow;
	color: white;
	font-weight: bold;
	font-size: 125%;
	position: absolute;
	float: right;
	top: 1em;
	right: 1em;
}

.helpme,
#warn-not-close {
	background: rgb(255,255,136);
	padding: 0.75em 0.5em;
	border: solid #febf01;
	border-width: 1px 0;
	text-align: center;
}

#update-notification {
	margin: 1em;
	padding: 0.5em;
	background-color: #FF9;
	color: #F33;
	text-align: center;
	border-radius: 20px;
	border: medium solid red;
}

.update-notify {
	font-size: 20pt;
	font-weight: bold;
}

.update-links {
	color: #333;
	font-size: 14pt;
}

#update-dlnow {
	text-decoration: none;
	color: #333;
	border: thin solid #333;
	padding: 0.5em;
	border-radius: 5px;
	background-color: #f0f0f0;
}

#update-dlnow:hover {
	background-color: #fff;
}

#update-whatsnew {
	font-size: 11pt;
	color: blue;
	text-decoration: underline;
}

.update-whyupdate {
	color: #333;
	font-size: 9pt;
}

/* FTP Browser */
.breadcrumb {background-color: #F5F5F5; border-radius: 4px; list-style: none outside none; margin: 0 0 18px; padding: 8px 15px;}
.breadcrumb > li {display: inline-block; text-shadow: 0 1px 0 #FFFFFF;}
#ak_crumbs span {padding: 1px 3px;}
#ak_crumbs a {cursor: pointer;}
#ftpBrowserFolderList a{cursor:pointer}

/* Bootstrap porting */
.table {margin-bottom: 18px;width: 100%;}
.table th, .table td {border-top: 1px solid #DDDDDD; line-height: 18px; padding: 8px; text-align: left; vertical-align: top;}
.table-striped tbody > tr:nth-child(2n+1) > td, .table-striped tbody > tr:nth-child(2n+1) > th { background-color: #F9F9F9;}

/* Layout helpers
----------------------------------*/
.ui-helper-hidden { display: none; }
.ui-helper-hidden-accessible { border: 0; clip: rect(0 0 0 0); height: 1px; margin: -1px; overflow: hidden; padding: 0; position: absolute; width: 1px; }
.ui-helper-reset { margin: 0; padding: 0; border: 0; outline: 0; line-height: 1.3; text-decoration: none; font-size: 100%; list-style: none; }
.ui-helper-clearfix:before, .ui-helper-clearfix:after { content: ""; display: table; }
.ui-helper-clearfix:after { clear: both; }
.ui-helper-clearfix { zoom: 1; }
.ui-helper-zfix { width: 100%; height: 100%; top: 0; left: 0; position: absolute; opacity: 0; filter:Alpha(Opacity=0); }


/* Interaction Cues
----------------------------------*/
.ui-state-disabled { cursor: default !important; }

/* Icons
----------------------------------*/

/* states and images */
.ui-icon { display: block; text-indent: -99999px; overflow: hidden; background-repeat: no-repeat; }

/* Misc visuals
----------------------------------*/

/* Overlays */
.ui-widget-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
.ui-resizable { position: relative;}
.ui-resizable-handle { position: absolute;font-size: 0.1px; display: block; }
.ui-resizable-disabled .ui-resizable-handle, .ui-resizable-autohide .ui-resizable-handle { display: none; }
.ui-resizable-n { cursor: n-resize; height: 7px; width: 100%; top: -5px; left: 0; }
.ui-resizable-s { cursor: s-resize; height: 7px; width: 100%; bottom: -5px; left: 0; }
.ui-resizable-e { cursor: e-resize; width: 7px; right: -5px; top: 0; height: 100%; }
.ui-resizable-w { cursor: w-resize; width: 7px; left: -5px; top: 0; height: 100%; }
.ui-resizable-se { cursor: se-resize; width: 12px; height: 12px; right: 1px; bottom: 1px; }
.ui-resizable-sw { cursor: sw-resize; width: 9px; height: 9px; left: -5px; bottom: -5px; }
.ui-resizable-nw { cursor: nw-resize; width: 9px; height: 9px; left: -5px; top: -5px; }
.ui-resizable-ne { cursor: ne-resize; width: 9px; height: 9px; right: -5px; top: -5px;}
.ui-button { display: inline-block; position: relative; padding: 0; margin-right: .1em; cursor: pointer; text-align: center; zoom: 1; overflow: visible; } /* the overflow property removes extra width in IE */
.ui-button, .ui-button:link, .ui-button:visited, .ui-button:hover, .ui-button:active { text-decoration: none; }
.ui-button-icon-only { width: 2.2em; } /* to make room for the icon, a width needs to be set here */
button.ui-button-icon-only { width: 2.4em; } /* button elements seem to need a little more width */
.ui-button-icons-only { width: 3.4em; }
button.ui-button-icons-only { width: 3.7em; }

/*button text element */
.ui-button .ui-button-text { display: block; line-height: 1.4;  }
.ui-button-text-only .ui-button-text { padding: 0; }
.ui-button-icon-only .ui-button-text, .ui-button-icons-only .ui-button-text { padding: .4em; text-indent: -9999999px; }
.ui-button-text-icon-primary .ui-button-text, .ui-button-text-icons .ui-button-text { padding: .4em 1em .4em 2.1em; }
.ui-button-text-icon-secondary .ui-button-text, .ui-button-text-icons .ui-button-text { padding: .4em 2.1em .4em 1em; }
.ui-button-text-icons .ui-button-text { padding-left: 2.1em; padding-right: 2.1em; }
/* no icon support for input elements, provide padding by default */
input.ui-button { padding: .4em 1em; }

/*button icon element(s) */
.ui-button-icon-only .ui-icon, .ui-button-text-icon-primary .ui-icon, .ui-button-text-icon-secondary .ui-icon, .ui-button-text-icons .ui-icon, .ui-button-icons-only .ui-icon { position: absolute; top: 50%; margin-top: -8px; }
.ui-button-icon-only .ui-icon { left: 50%; margin-left: -8px; }
.ui-button-text-icon-primary .ui-button-icon-primary, .ui-button-text-icons .ui-button-icon-primary, .ui-button-icons-only .ui-button-icon-primary { left: .5em; }
.ui-button-text-icon-secondary .ui-button-icon-secondary, .ui-button-text-icons .ui-button-icon-secondary, .ui-button-icons-only .ui-button-icon-secondary { right: .5em; }
.ui-button-text-icons .ui-button-icon-secondary, .ui-button-icons-only .ui-button-icon-secondary { right: .5em; }

/*button sets*/
.ui-buttonset { margin-right: 7px; }
.ui-buttonset .ui-button { margin-left: 0; margin-right: -.3em; }

/* workarounds */
button.ui-button::-moz-focus-inner { border: 0; padding: 0; } /* reset extra padding in Firefox */
.ui-dialog { position: absolute; top: 0; left: 0; padding: .2em; width: 300px; overflow: hidden; }
.ui-dialog .ui-dialog-titlebar { padding: .4em 1em; position: relative;  }
.ui-dialog .ui-dialog-title { float: left; margin: .1em 16px .1em 0; }
.ui-dialog .ui-dialog-titlebar-close { position: absolute; right: .3em; top: 50%; width: 19px; margin: -10px 0 0 0; padding: 1px; height: 18px; display:none}
.ui-dialog .ui-dialog-titlebar-close span { display: none; margin: 1px; }
.ui-dialog .ui-dialog-titlebar-close:hover, .ui-dialog .ui-dialog-titlebar-close:focus { padding: 0; }
.ui-dialog .ui-dialog-content { position: relative; border: 0; padding: .5em 1em; background: none; overflow: auto; zoom: 1; }
.ui-dialog .ui-dialog-buttonpane { text-align: left; border-width: 1px 0 0 0; background-image: none; margin: .5em 0 0 0; padding: .3em 1em .5em .4em; }
.ui-dialog .ui-dialog-buttonpane .ui-dialog-buttonset { float: right; }
.ui-dialog .ui-dialog-buttonpane button { margin: .5em .4em .5em 0; cursor: pointer; }
.ui-dialog .ui-resizable-se { width: 14px; height: 14px; right: 3px; bottom: 3px; }
.ui-draggable .ui-dialog-titlebar { cursor: move; }

/* Component containers
----------------------------------*/
.ui-widget-content { border: 1px solid #a6c9e2; background: #fcfdfd; color: #222222; }
.ui-widget-content a { color: #222222; }
.ui-widget-header { border: 1px solid #4297d7; background: #5c9ccc ; color: #ffffff; font-weight: bold; }
.ui-widget-header a { color: #ffffff; }

/* Interaction states
----------------------------------*/
.ui-state-default a, .ui-state-default a:link, .ui-state-default a:visited {text-decoration: none; }
.ui-state-hover, .ui-widget-content .ui-state-hover, .ui-widget-header .ui-state-hover{
    background: #4096ee;
	background: -moz-linear-gradient(top, #4096ee 0%, #60abf8 56%, #7abcff 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#4096ee), color-stop(56%,#60abf8), color-stop(100%,#7abcff));
	background: -webkit-linear-gradient(top, #4096ee 0%,#60abf8 56%,#7abcff 100%);
	background: -o-linear-gradient(top, #4096ee 0%,#60abf8 56%,#7abcff 100%);
	background: -ms-linear-gradient(top, #4096ee 0%,#60abf8 56%,#7abcff 100%);
	background: linear-gradient(top, #4096ee 0%,#60abf8 56%,#7abcff 100%);
}
.ui-state-hover a, .ui-state-hover a:hover, .ui-state-hover a:link, .ui-state-hover a:visited { color: #1d5987; text-decoration: none; }
.ui-state-active a, .ui-state-active a:link, .ui-state-active a:visited { color: #e17009; text-decoration: none; }

/* Interaction Cues
----------------------------------*/
.ui-state-highlight, .ui-widget-content .ui-state-highlight, .ui-widget-header .ui-state-highlight  {border: 1px solid #fad42e; background: #fbec88 ; color: #363636; }
.ui-state-highlight a, .ui-widget-content .ui-state-highlight a,.ui-widget-header .ui-state-highlight a { color: #363636; }
.ui-state-error, .ui-widget-content .ui-state-error, .ui-widget-header .ui-state-error {border: 1px solid #cd0a0a; background: #fef1ec ; color: #cd0a0a; }
.ui-state-error a, .ui-widget-content .ui-state-error a, .ui-widget-header .ui-state-error a { color: #cd0a0a; }
.ui-state-error-text, .ui-widget-content .ui-state-error-text, .ui-widget-header .ui-state-error-text { color: #cd0a0a; }
.ui-priority-primary, .ui-widget-content .ui-priority-primary, .ui-widget-header .ui-priority-primary { font-weight: bold; }
.ui-priority-secondary, .ui-widget-content .ui-priority-secondary,  .ui-widget-header .ui-priority-secondary { opacity: .7; filter:Alpha(Opacity=70); font-weight: normal; }
.ui-state-disabled, .ui-widget-content .ui-state-disabled, .ui-widget-header .ui-state-disabled { opacity: .35; filter:Alpha(Opacity=35); background-image: none; }
.ui-state-disabled .ui-icon { filter:Alpha(Opacity=35); } /* For IE8 - See #6059 */

/* Icons
----------------------------------*/

/* states and images */
.ui-icon { display:none}

/* Misc visuals
----------------------------------*/

/* Corner radius */
.ui-corner-all, .ui-corner-top, .ui-corner-left, .ui-corner-tl { -moz-border-radius-topleft: 5px; -webkit-border-top-left-radius: 5px; -khtml-border-top-left-radius: 5px; border-top-left-radius: 5px; }
.ui-corner-all, .ui-corner-top, .ui-corner-right, .ui-corner-tr { -moz-border-radius-topright: 5px; -webkit-border-top-right-radius: 5px; -khtml-border-top-right-radius: 5px; border-top-right-radius: 5px; }
.ui-corner-all, .ui-corner-bottom, .ui-corner-left, .ui-corner-bl { -moz-border-radius-bottomleft: 5px; -webkit-border-bottom-left-radius: 5px; -khtml-border-bottom-left-radius: 5px; border-bottom-left-radius: 5px; }
.ui-corner-all, .ui-corner-bottom, .ui-corner-right, .ui-corner-br { -moz-border-radius-bottomright: 5px; -webkit-border-bottom-right-radius: 5px; -khtml-border-bottom-right-radius: 5px; border-bottom-right-radius: 5px; }

/* Overlays */
.ui-widget-overlay { background: #000000 ; opacity: .8;filter:Alpha(Opacity=80); }
.ui-widget-shadow { margin: -8px 0 0 -8px; padding: 8px; background: #000000 ; opacity: .8;filter:Alpha(Opacity=80); -moz-border-radius: 8px; -khtml-border-radius: 8px; -webkit-border-radius: 8px; border-radius: 8px; }


.ui-button {
    font-family: Calibri,"Helvetica Neue",Helvetica,Arial,sans-serif;
    font-size: 1.4rem;
	display: inline-block;
	padding: .5em 1em;
	margin: 1em .25em;
	color:#fff;
    text-decoration: none;
	background: #7abcff;
	border: solid #ddd;
	cursor: pointer;
	border-radius: .25em;
  	transition: 0.3s linear all;
}

CSS;
callExtraFeature('onExtraHeadCSS');}

function echoHeadJavascript(){?>
	<script type="text/javascript" language="javascript">
		var akeeba_debug = <?php echo defined('KSDEBUG') ? 'true' : 'false' ?>;
		var sftp_path    = '<?php echo TranslateWinPath(defined('KSROOTDIR') ? KSROOTDIR : dirname(__FILE__)); ?>/';
		var isJoomla     = true;

		/**
		 * Returns the version of Internet Explorer or a -1
		 * (indicating the use of another browser).
		 *
		 * @return   integer  MSIE version or -1
		 */
		function getInternetExplorerVersion()
		{
			var rv = -1; // Return value assumes failure.
			if (navigator.appName == 'Microsoft Internet Explorer')
			{
				var ua = navigator.userAgent;
				var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
				if (re.exec(ua) != null)
				{
					rv = parseFloat(RegExp.$1);
				}
			}
			return rv;
		}

		$(document).ready(function ()
		{
			// Hide 2nd Page
			$('#page2').css('display', 'none');

			// Translate the GUI
			translateGUI();

			// Hook interaction handlers
			$(document).keyup(closeLightbox);
			$('#kickstart\\.procengine').change(onChangeProcengine);
			$('#kickstart\\.setup\\.sourcepath').change(onArchiveListReload);
			$('#reloadArchives').click(onArchiveListReload);
			$('#checkFTPTempDir').click(oncheckFTPTempDirClick);
			$('#resetFTPTempDir').click(onresetFTPTempDir);
			$('#browseFTP').click(onbrowseFTP);
			$('#testFTP').click(onTestFTPClick);
			$('#gobutton').click(onStartExtraction);
			$('#runInstaller').click(onRunInstallerClick);
			$('#runCleanup').click(onRunCleanupClick);
			$('#gotoSite').click(function (event)
			{
				window.open('index.php', 'finalstepsite');
				window.close();
			});
			$('#gotoAdministrator').click(function (event)
			{
				window.open('administrator/index.php', 'finalstepadmin');
				window.close();
			});
			$('#gotoStart').click(onGotoStartClick);
			$('#showFineTune').click(function ()
			{
				$('#fine-tune-holder').show();
				$(this).hide();
			});

			// Reset the progress bar
			setProgressBar(0);

			// Show warning
			var msieVersion = getInternetExplorerVersion();
			if ((msieVersion != -1) && (msieVersion <= 8.99))
			{
				$('#ie7Warning').css('display', 'block');
			}
			if (!akeeba_debug)
			{
				$('#preextraction').css('display', 'block');
				$('#fade').css('display', 'block');
			}

			// Trigger change, so we avoid problems if the user refreshes the page
			$('#kickstart\\.procengine').change();
		});

		var translation = {
			<?php echoTranslationStrings(); ?>
		}

		var akeeba_ajax_url                  = '<?php echo defined('KSSELFNAME') ? KSSELFNAME : basename(__FILE__); ?>';
		var akeeba_error_callback            = onGenericError;
		var akeeba_restoration_stat_inbytes  = 0;
		var akeeba_restoration_stat_outbytes = 0;
		var akeeba_restoration_stat_files    = 0;
		var akeeba_restoration_stat_total    = 0;
		var akeeba_factory                   = null;

		var akeeba_ftpbrowser_host      = null;
		var akeeba_ftpbrowser_port      = 21;
		var akeeba_ftpbrowser_username  = null;
		var akeeba_ftpbrowser_password  = null;
		var akeeba_ftpbrowser_passive   = 1;
		var akeeba_ftpbrowser_ssl       = 0;
		var akeeba_ftpbrowser_directory = '';

		var akeeba_sftpbrowser_host      = null;
		var akeeba_sftpbrowser_port      = 21;
		var akeeba_sftpbrowser_username  = null;
		var akeeba_sftpbrowser_password  = null;
		var akeeba_sftpbrowser_pubkey    = null;
		var akeeba_sftpbrowser_privkey   = null;
		var akeeba_sftpbrowser_directory = '';

		function translateGUI()
		{
			$('*').each(function (i, e)
			{
				transKey = $(e).text();
				if (array_key_exists(transKey, translation))
				{
					$(e).text(translation[transKey]);
				}
			});
		}

		function trans(key)
		{
			if (array_key_exists(key, translation))
			{
				return translation[key];
			}
			else
			{
				return key;
			}
		}

		function array_key_exists(key, search)
		{
			if (!search || (search.constructor !== Array && search.constructor !== Object))
			{
				return false;
			}
			return key in search;
		}

		function empty(mixed_var)
		{
			var key;

			if (mixed_var === "" ||
				mixed_var === 0 ||
				mixed_var === "0" ||
				mixed_var === null ||
				mixed_var === false ||
				typeof mixed_var === 'undefined'
			)
			{
				return true;
			}

			if (typeof mixed_var == 'object')
			{
				for (key in mixed_var)
				{
					return false;
				}
				return true;
			}

			return false;
		}

		function is_array(mixed_var)
		{
			var key         = '';
			var getFuncName = function (fn)
			{
				var name = (/\W*function\s+([\w\$]+)\s*\(/).exec(fn);
				if (!name)
				{
					return '(Anonymous)';
				}
				return name[1];
			};

			if (!mixed_var)
			{
				return false;
			}

			// BEGIN REDUNDANT
			this.php_js     = this.php_js || {};
			this.php_js.ini = this.php_js.ini || {};
			// END REDUNDANT

			if (typeof mixed_var === 'object')
			{

				if (this.php_js.ini['phpjs.objectsAsArrays'] &&  // Strict checking for being a JavaScript array (only check this way if call ini_set('phpjs.objectsAsArrays', 0) to disallow objects as arrays)
					(
					(this.php_js.ini['phpjs.objectsAsArrays'].local_value.toLowerCase &&
					this.php_js.ini['phpjs.objectsAsArrays'].local_value.toLowerCase() === 'off') ||
					parseInt(this.php_js.ini['phpjs.objectsAsArrays'].local_value, 10) === 0)
				)
				{
					return mixed_var.hasOwnProperty('length') && // Not non-enumerable because of being on parent class
						!mixed_var.propertyIsEnumerable('length') && // Since is own property, if not enumerable, it must be a built-in function
						getFuncName(mixed_var.constructor) !== 'String'; // exclude String()
				}

				if (mixed_var.hasOwnProperty)
				{
					for (key in mixed_var)
					{
						// Checks whether the object has the specified property
						// if not, we figure it's not an object in the sense of a php-associative-array.
						if (false === mixed_var.hasOwnProperty(key))
						{
							return false;
						}
					}
				}

				// Read discussion at: http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_is_array/
				return true;
			}

			return false;
		}

		function resolvePath(filename)
		{
			filename  = filename.replace('\/\/g', '\/');
			var parts = filename.split('/');
			var out   = [];

			$.each(parts, function (i, part)
			{
				if (part == '.') return;
				if (part == '..')
				{
					out.pop();
					return;
				}
				out.push(part);
			});

			return out.join('/');
		}

		/**
		 * Performs an AJAX request and returns the parsed JSON output.
		 * The global akeeba_ajax_url is used as the AJAX proxy URL.
		 * If there is no errorCallback, the global akeeba_error_callback is used.
		 * @param data An object with the query data, e.g. a serialized form
		 * @param successCallback A function accepting a single object parameter, called on success
		 * @param errorCallback A function accepting a single string parameter, called on failure
		 */
		function doAjax(data, successCallback, errorCallback)
		{
			var structure =
			    {
				    type:    "POST",
				    url:     akeeba_ajax_url,
				    cache:   false,
				    data:    data,
				    timeout: 600000,
				    success: function (msg)
				             {
					             // Initialize
					             var junk    = null;
					             var message = "";

					             // Get rid of junk before the data
					             var valid_pos = msg.indexOf('###');
					             if (valid_pos == -1)
					             {
						             // Valid data not found in the response
						             msg = 'Invalid AJAX data received:<br/>' + msg;
						             if (errorCallback == null)
						             {
							             if (akeeba_error_callback != null)
							             {
								             akeeba_error_callback(msg);
							             }
						             }
						             else
						             {
							             errorCallback(msg);
						             }
						             return;
					             }
					             else if (valid_pos != 0)
					             {
						             // Data is prefixed with junk
						             junk    = msg.substr(0, valid_pos);
						             message = msg.substr(valid_pos);
					             }
					             else
					             {
						             message = msg;
					             }
					             message = message.substr(3); // Remove triple hash in the beginning

					             // Get of rid of junk after the data
					             var valid_pos = message.lastIndexOf('###');
					             message       = message.substr(0, valid_pos); // Remove triple hash in the end

					             try
					             {
						             var data = eval('(' + message + ')');
					             }
					             catch (err)
					             {
						             var msg = err.message + "\n<br/>\n<pre>\n" + message + "\n</pre>";
						             if (errorCallback == null)
						             {
							             if (akeeba_error_callback != null)
							             {
								             akeeba_error_callback(msg);
							             }
						             }
						             else
						             {
							             errorCallback(msg);
						             }
						             return;
					             }

					             // Call the callback function
					             successCallback(data);
				             },
				    error:   function (Request, textStatus, errorThrown)
				             {
					             var message = '<strong>AJAX Loading Error</strong><br/>HTTP Status: ' + Request.status + ' (' + Request.statusText + ')<br/>';
					             message     = message + 'Internal status: ' + textStatus + '<br/>';
					             message     = message + 'XHR ReadyState: ' + Request.readyState + '<br/>';
					             message     = message + 'Raw server response:<br/>' + Request.responseText;
					             if (errorCallback == null)
					             {
						             if (akeeba_error_callback != null)
						             {
							             akeeba_error_callback(message);
						             }
					             }
					             else
					             {
						             errorCallback(message);
					             }
				             }
			    };
			$.ajax(structure);
		}

		function onChangeProcengine(event)
		{
			if ($('#kickstart\\.procengine').val() == 'direct')
			{
				$('#ftp-options').hide('fast');
			}
			else
			{
				$('#ftp-options').show('fast');
			}

			if ($('#kickstart\\.procengine').val() == 'sftp')
			{
				$('#ftp-ssl-passive').hide('fast');

				if ($('#kickstart\\.ftp\\.dir').val() == '')
				{
					$('#kickstart\\.ftp\\.dir').val(sftp_path);
				}

				$('#testFTP').html(trans('BTN_TESTSFTPCON'))
			}
			else
			{
				$('#ftp-ssl-passive').show('fast');
				$('#testFTP').html(trans('BTN_TESTFTPCON'))
			}
		}

		function closeLightbox(event)
		{
			var closeMe = false;

			if ((event == null) || (event == undefined))
			{
				closeMe = true;
			}
			else if (event.keyCode == '27')
			{
				closeMe = true;
			}

			if (closeMe)
			{
				document.getElementById('preextraction').style.display = 'none';
				document.getElementById('genericerror').style.display  = 'none';
				document.getElementById('fade').style.display          = 'none';
				$(document).unbind('keyup', closeLightbox);
			}
		}

		function onGenericError(msg)
		{
			$('#genericerrorInner').html(msg);
			$('#genericerror').css('display', 'block');
			$('#fade').css('display', 'block');
			$(document).keyup(closeLightbox);
		}

		function setProgressBar(percent)
		{
			var newValue = 0;

			if (percent <= 1)
			{
				newValue = 100 * percent;
			}
			else
			{
				newValue = percent;
			}

			$('#progressbar-inner').css('width', percent + '%');
		}

		function oncheckFTPTempDirClick(event)
		{
			var data = {
				'task': 'checkTempdir',
				'json': JSON.stringify({
					'kickstart.ftp.tempdir': $('#kickstart\\.ftp\\.tempdir').val()
				})
			};

			doAjax(data, function (ret)
			{
				var key = ret.status ? 'FTP_TEMPDIR_WRITABLE' : 'FTP_TEMPDIR_UNWRITABLE';
				alert(trans(key));
			});
		}

		function onTestFTPClick(event)
		{
			var type = 'ftp';

			if ($('#kickstart\\.procengine').val() == 'sftp')
			{
				type = 'sftp';
			}

			var data = {
				'task': 'checkFTP',
				'json': JSON.stringify({
					'type':                  type,
					'kickstart.ftp.host':    $('#kickstart\\.ftp\\.host').val(),
					'kickstart.ftp.port':    $('#kickstart\\.ftp\\.port').val(),
					'kickstart.ftp.ssl':     $('#kickstart\\.ftp\\.ssl').is(':checked'),
					'kickstart.ftp.passive': $('#kickstart\\.ftp\\.passive').is(':checked'),
					'kickstart.ftp.user':    $('#kickstart\\.ftp\\.user').val(),
					'kickstart.ftp.pass':    $('#kickstart\\.ftp\\.pass').val(),
					'kickstart.ftp.dir':     $('#kickstart\\.ftp\\.dir').val(),
					'kickstart.ftp.tempdir': $('#kickstart\\.ftp\\.tempdir').val()
				})
			};
			doAjax(data, function (ret)
			{
				if (type == 'ftp')
				{
					var key = ret.status ? 'FTP_CONNECTION_OK' : 'FTP_CONNECTION_FAILURE';
				}
				else
				{
					var key = ret.status ? 'SFTP_CONNECTION_OK' : 'SFTP_CONNECTION_FAILURE';
				}


				alert(trans(key) + "\n\n" + (ret.status ? '' : ret.message));
			});
		}

		function onbrowseFTP()
		{
			if ($('#kickstart\\.procengine').val() != 'sftp')
			{
				akeeba_ftpbrowser_host      = $('#kickstart\\.ftp\\.host').val();
				akeeba_ftpbrowser_port      = $('#kickstart\\.ftp\\.port').val();
				akeeba_ftpbrowser_username  = $('#kickstart\\.ftp\\.user').val();
				akeeba_ftpbrowser_password  = $('#kickstart\\.ftp\\.pass').val();
				akeeba_ftpbrowser_passive   = $('#kickstart\\.ftp\\.passive').is(':checked');
				akeeba_ftpbrowser_ssl       = $('#kickstart\\.ftp\\.ssl').is(':checked');
				akeeba_ftpbrowser_directory = $('#kickstart\\.ftp\\.dir').val();

				var akeeba_onbrowseFTP_callback = function (path)
				{
					var charlist = ('/').replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
					var re       = new RegExp('^[' + charlist + ']+', 'g');
					path         = '/' + (path + '').replace(re, '');
					$('#kickstart\\.ftp\\.dir').val(path);
				};

				akeeba_ftpbrowser_hook(akeeba_onbrowseFTP_callback);
			}
			else
			{
				akeeba_sftpbrowser_host      = $('#kickstart\\.ftp\\.host').val();
				akeeba_sftpbrowser_port      = $('#kickstart\\.ftp\\.port').val();
				akeeba_sftpbrowser_username  = $('#kickstart\\.ftp\\.user').val();
				akeeba_sftpbrowser_password  = $('#kickstart\\.ftp\\.pass').val();
				akeeba_sftpbrowser_directory = $('#kickstart\\.ftp\\.dir').val();

				var akeeba_postprocsftp_callback = function (path)
				{
					var charlist = ('/').replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
					var re       = new RegExp('^[' + charlist + ']+', 'g');
					path         = '/' + (path + '').replace(re, '');
					$('#kickstart\\.ftp\\.dir').val(path);
				};

				akeeba_sftpbrowser_hook(akeeba_postprocsftp_callback);
			}
		}

		akeeba_ftpbrowser_hook = function (callback)
		{
			var ftp_dialog_element = $("#ftpdialog");
			var ftp_callback       = function ()
			{
				callback(akeeba_ftpbrowser_directory);
				ftp_dialog_element.dialog("close");
			};

			ftp_dialog_element.css('display', 'block');
			ftp_dialog_element.removeClass('ui-state-error');
			ftp_dialog_element.dialog({
				autoOpen:  false,
				title:     trans('CONFIG_UI_FTPBROWSER_TITLE'),
				draggable: false,
				height:    500,
				width:     500,
				modal:     true,
				resizable: false,
				buttons:   {
					"OK":     ftp_callback,
					"Cancel": function ()
					          {
						          ftp_dialog_element.dialog("close");
					          }
				}
			});

			$('#ftpBrowserErrorContainer').css('display', 'none');
			$('#ftpBrowserFolderList').html('');
			$('#ak_crumbs').html('');

			ftp_dialog_element.dialog('open');

			if (empty(akeeba_ftpbrowser_directory)) akeeba_ftpbrowser_directory = '';

			var data = {
				'task': 'ftpbrowse',
				'json': JSON.stringify({
					'host':      akeeba_ftpbrowser_host,
					'port':      akeeba_ftpbrowser_port,
					'username':  akeeba_ftpbrowser_username,
					'password':  akeeba_ftpbrowser_password,
					'passive':   (akeeba_ftpbrowser_passive ? 1 : 0),
					'ssl':       (akeeba_ftpbrowser_ssl ? 1 : 0),
					'directory': akeeba_ftpbrowser_directory
				})
			};

			// Do AJAX call and Render results
			doAjax(
				data,
				function (data)
				{
					if (data.error != false)
					{
						// An error occured
						$('#ftpBrowserError').html(trans(data.error));
						$('#ftpBrowserErrorContainer').css('display', 'block');
						$('#ftpBrowserFolderList').css('display', 'none');
						$('#ak_crumbs').css('display', 'none');
					}
					else
					{
						// Create the interface
						$('#ftpBrowserErrorContainer').css('display', 'none');

						// Display the crumbs
						if (!empty(data.breadcrumbs))
						{
							$('#ak_crumbs').css('display', 'block');
							$('#ak_crumbs').html('');
							var relativePath = '/';

							akeeba_ftpbrowser_addcrumb(trans('UI-ROOT'), '/', callback);

							$.each(data.breadcrumbs, function (i, crumb)
							{
								relativePath += '/' + crumb;

								akeeba_ftpbrowser_addcrumb(crumb, relativePath, callback);
							});
						}
						else
						{
							$('#ak_crumbs').hide();
						}

						// Display the list of directories
						if (!empty(data.list))
						{
							$('#ftpBrowserFolderList').show();

							$.each(data.list, function (i, item)
							{
								akeeba_ftpbrowser_create_link(data.directory + '/' + item, item, $('#ftpBrowserFolderList'), callback);
							});
						}
						else
						{
							$('#ftpBrowserFolderList').css('display', 'none');
						}
					}
				},
				function (message)
				{
					$('#ftpBrowserError').html(message);
					$('#ftpBrowserErrorContainer').css('display', 'block');
					$('#ftpBrowserFolderList').css('display', 'none');
					$('#ak_crumbs').css('display', 'none');
				}
			);
		};

		/**
		 * Creates a directory link for the FTP browser UI
		 */
		function akeeba_ftpbrowser_create_link(path, label, container, callback)
		{
			var row  = $(document.createElement('tr'));
			var cell = $(document.createElement('td')).appendTo(row);

			var myElement = $(document.createElement('a'))
				.text(label)
				.click(function ()
				{
					akeeba_ftpbrowser_directory = resolvePath(path);
					akeeba_ftpbrowser_hook(callback);
				})
				.appendTo(cell);
			row.appendTo($(container));
		}

		/**
		 * Adds a breadcrumb to the FTP browser
		 */
		function akeeba_ftpbrowser_addcrumb(crumb, relativePath, callback, last)
		{
			if (empty(last)) last = false;
			var li = $(document.createElement('li'));

			$(document.createElement('a'))
				.html(crumb)
				.click(function (e)
				{
					akeeba_ftpbrowser_directory = relativePath;
					akeeba_ftpbrowser_hook(callback);

					if (e.preventDefault)
					{
						e.preventDefault();
					}
					else
					{
						e.returnValue = false;
					}
				})
				.appendTo(li);

			if (!last)
			{
				$(document.createElement('span'))
					.text('/')
					.addClass('divider')
					.appendTo(li);
			}

			li.appendTo('#ak_crumbs');
		}

		// FTP browser function
		akeeba_sftpbrowser_hook = function (callback)
		{
			var sftp_dialog_element = $("#ftpdialog");
			var sftp_callback       = function ()
			{
				callback(akeeba_sftpbrowser_directory);
				sftp_dialog_element.dialog("close");
			};

			sftp_dialog_element.css('display', 'block');
			sftp_dialog_element.removeClass('ui-state-error');
			sftp_dialog_element.dialog({
				autoOpen:  false,
				'title':   trans('CONFIG_UI_SFTPBROWSER_TITLE'),
				draggable: false,
				height:    500,
				width:     500,
				modal:     true,
				resizable: false,
				buttons:   {
					"OK":     sftp_callback,
					"Cancel": function ()
					          {
						          sftp_dialog_element.dialog("close");
					          }
				}
			});

			$('#ftpBrowserErrorContainer').css('display', 'none');
			$('#ftpBrowserFolderList').html('');
			$('#ak_crumbs').html('');

			sftp_dialog_element.dialog('open');

			if (empty(akeeba_sftpbrowser_directory)) akeeba_sftpbrowser_directory = '';

			var data = {
				'task': 'sftpbrowse',
				'json': JSON.stringify({
					'host':      akeeba_sftpbrowser_host,
					'port':      akeeba_sftpbrowser_port,
					'username':  akeeba_sftpbrowser_username,
					'password':  akeeba_sftpbrowser_password,
					'directory': akeeba_sftpbrowser_directory
				})
			};

			doAjax(
				data,
				function (data)
				{
					if (data.error != false)
					{
						// An error occured
						$('#ftpBrowserError').html(data.error);
						$('#ftpBrowserErrorContainer').css('display', 'block');
						$('#ftpBrowserFolderList').css('display', 'none');
						$('#ak_crumbs').css('display', 'none');
					}
					else
					{
						// Create the interface
						$('#ftpBrowserErrorContainer').css('display', 'none');

						// Display the crumbs
						if (!empty(data.breadcrumbs))
						{
							$('#ak_crumbs').css('display', 'block');
							$('#ak_crumbs').html('');
							var relativePath = '/';

							akeeba_sftpbrowser_addcrumb(trans('UI-ROOT'), '/', callback);

							$.each(data.breadcrumbs, function (i, crumb)
							{
								relativePath += '/' + crumb;

								akeeba_sftpbrowser_addcrumb(crumb, relativePath, callback);
							});
						}
						else
						{
							$('#ftpBrowserCrumbs').css('display', 'none');
						}

						// Display the list of directories
						if (!empty(data.list))
						{
							$('#ftpBrowserFolderList').css('display', 'block');

							$.each(data.list, function (i, item)
							{
								akeeba_sftpbrowser_create_link(data.directory + '/' + item, item, $('#ftpBrowserFolderList'), callback);
							});
						}
						else
						{
							$('#ftpBrowserFolderList').css('display', 'none');
						}
					}
				},
				function (message)
				{
					$('#ftpBrowserError').html(message);
					$('#ftpBrowserErrorContainer').css('display', 'block');
					$('#ftpBrowserFolderList').css('display', 'none');
					$('#ftpBrowserCrumbs').css('display', 'none');
				}
			);
		};

		/**
		 * Creates a directory link for the SFTP browser UI
		 */
		function akeeba_sftpbrowser_create_link(path, label, container, callback)
		{
			var row  = $(document.createElement('tr'));
			var cell = $(document.createElement('td')).appendTo(row);

			var myElement = $(document.createElement('a'))
				.text(label)
				.click(function ()
				{
					akeeba_sftpbrowser_directory = resolvePath(path);
					akeeba_sftpbrowser_hook(callback);
				})
				.appendTo(cell);
			row.appendTo($(container));
		}

		/**
		 * Adds a breadcrumb to the SFTP browser
		 */
		function akeeba_sftpbrowser_addcrumb(crumb, relativePath, callback, last)
		{
			if (empty(last)) last = false;
			var li = $(document.createElement('li'));

			$(document.createElement('a'))
				.html(crumb)
				.click(function (e)
				{
					akeeba_sftpbrowser_directory = relativePath;
					akeeba_sftpbrowser_hook(callback);

					if (e.preventDefault)
					{
						e.preventDefault();
					}
					else
					{
						e.returnValue = false;
					}
				})
				.appendTo(li);

			if (!last)
			{
				$(document.createElement('span'))
					.text('/')
					.addClass('divider')
					.appendTo(li);
			}

			li.appendTo('#ak_crumbs');
		}

		function onStartExtraction()
		{
			$('#page1').hide('fast');
			$('#page2').show('fast');

			$('#currentFile').text('');

			akeeba_error_callback = errorHandler;

			var data = {
				'task': 'startExtracting',
				'json': JSON.stringify({
					'kickstart.setup.sourcepath':     $('#kickstart\\.setup\\.sourcepath').val(),
					'kickstart.setup.sourcefile':     $('#kickstart\\.setup\\.sourcefile').val(),
					'kickstart.jps.password':         $('#kickstart\\.jps\\.password').val(),
					'kickstart.tuning.min_exec_time': $('#kickstart\\.tuning\\.min_exec_time').val(),
					'kickstart.tuning.max_exec_time': $('#kickstart\\.tuning\\.max_exec_time').val(),
					'kickstart.stealth.enable':       $('#kickstart\\.stealth\\.enable').is(':checked'),
					'kickstart.stealth.url':          $('#kickstart\\.stealth\\.url').val(),
					'kickstart.tuning.run_time_bias': 75,
					'kickstart.setup.restoreperms':   $('#kickstart\\.restorepermissions\\.enable').is(':checked'),
					'kickstart.setup.dryrun':         0,
					'kickstart.setup.ignoreerrors':   $('#kickstart\\.setup\\.ignoreerrors').is(':checked'),
					'kickstart.enabled':              1,
					'kickstart.security.password':    '',
					'kickstart.setup.renameback':     $('#kickstart\\.setup\\.renameback').is(':checked'),
					'kickstart.procengine':           $('#kickstart\\.procengine').val(),
					'kickstart.ftp.host':             $('#kickstart\\.ftp\\.host').val(),
					'kickstart.ftp.port':             $('#kickstart\\.ftp\\.port').val(),
					'kickstart.ftp.ssl':              $('#kickstart\\.ftp\\.ssl').is(':checked'),
					'kickstart.ftp.passive':          $('#kickstart\\.ftp\\.passive').is(':checked'),
					'kickstart.ftp.user':             $('#kickstart\\.ftp\\.user').val(),
					'kickstart.ftp.pass':             $('#kickstart\\.ftp\\.pass').val(),
					'kickstart.ftp.dir':              $('#kickstart\\.ftp\\.dir').val(),
					'kickstart.ftp.tempdir':          $('#kickstart\\.ftp\\.tempdir').val()
				})
			};
			doAjax(data, function (ret)
			{
				processRestorationStep(ret);
			});
		}

		function processRestorationStep(data)
		{
			// Look for errors
			if (!data.status)
			{
				errorHandler(data.message);
				return;
			}

			// Propagate warnings to the GUI
			if (!empty(data.Warnings))
			{
				$.each(data.Warnings, function (i, item)
				{
					$('#warnings').append(
						$(document.createElement('div'))
							.html(item)
					);
					$('#warningsBox').show('fast');
				});
			}

			// Parse total size, if exists
			if (array_key_exists('totalsize', data))
			{
				if (is_array(data.filelist))
				{
					akeeba_restoration_stat_total = 0;
					$.each(data.filelist, function (i, item)
					{
						akeeba_restoration_stat_total += item[1];
					});
				}
				akeeba_restoration_stat_outbytes = 0;
				akeeba_restoration_stat_inbytes  = 0;
				akeeba_restoration_stat_files    = 0;
			}

			// Update GUI
			akeeba_restoration_stat_inbytes += data.bytesIn;
			akeeba_restoration_stat_outbytes += data.bytesOut;
			akeeba_restoration_stat_files += data.files;
			var percentage = 0;
			if (akeeba_restoration_stat_total > 0)
			{
				percentage = 100 * akeeba_restoration_stat_inbytes / akeeba_restoration_stat_total;
				if (percentage < 0)
				{
					percentage = 0;
				}
				else if (percentage > 100)
				{
					percentage = 100;
				}
			}
			if (data.done) percentage = 100;
			setProgressBar(percentage);
			$('#currentFile').text(data.lastfile);

			if (!empty(data.factory)) akeeba_factory = data.factory;

			post = {
				'task': 'continueExtracting',
				'json': JSON.stringify({factory: akeeba_factory})
			};

			if (!data.done)
			{
				doAjax(post, function (ret)
				{
					processRestorationStep(ret);
				});
			}
			else
			{
				$('#page2a').hide('fast');
				$('#extractionComplete').show('fast');

				$('#runInstaller').css('display', 'inline-block');
			}
		}

		function onGotoStartClick(event)
		{
			$('#page2').hide('fast');
			$('#error').hide('fast');
			$('#page1').show('fast');
		}

		function onRunInstallerClick(event)
		{
			var windowReference = window.open('installation/index.php', 'installer');
			if (!windowReference.opener)
			{
				windowReference.opener = this.window;
			}
			$('#runCleanup').css('display', 'inline-block');
			$('#runInstaller').hide('fast');
		}

		function onRunCleanupClick(event)
		{
			post = {
				'task': 'isJoomla',
				// Passing the factory preserves the renamed files array
				'json': JSON.stringify({factory: akeeba_factory})
			};

			doAjax(post, function (ret)
			{
				isJoomla = ret;
				onRealRunCleanupClick();
			});
		}

		function onRealRunCleanupClick()
		{
			post = {
				'task': 'cleanUp',
				// Passing the factory preserves the renamed files array
				'json': JSON.stringify({factory: akeeba_factory})
			};

			doAjax(post, function (ret)
			{
				$('#runCleanup').hide('fast');
				$('#gotoSite').css('display', 'inline-block');
				if (isJoomla)
				{
					$('#gotoAdministrator').css('display', 'inline-block');
				}
				else
				{
					$('#gotoAdministrator').css('display', 'none');
				}
				$('#gotoPostRestorationRroubleshooting').css('display', 'block');
			});

		}

		function errorHandler(msg)
		{
			$('#errorMessage').html(msg);
			$('#error').show('fast');
		}

		function onresetFTPTempDir(event)
		{
			$('#kickstart\\.ftp\\.tempdir').val('<?php echo addcslashes(AKKickstartUtils::getPath(), '\\\'"') ?>');
		}

		function onArchiveListReload()
		{
			post = {
				'task': 'listArchives',
				'json': JSON.stringify({path: $('#kickstart\\.setup\\.sourcepath').val()})
			}

			doAjax(post, function (ret)
			{
				$('#sourcefileContainer').html(ret);
			});
		}

		/**
		 * Akeeba Kickstart Update Check
		 */

		var akeeba_update  = {version: '0'};
		var akeeba_version = '5.3.0';

		function version_compare(v1, v2, operator)
		{
			// BEGIN REDUNDANT
			this.php_js     = this.php_js || {};
			this.php_js.ENV = this.php_js.ENV || {};
			// END REDUNDANT
			// Important: compare must be initialized at 0.
			var i           = 0,
			    x           = 0,
			    compare     = 0,
			    // vm maps textual PHP versions to negatives so they're less than 0.
			    // PHP currently defines these as CASE-SENSITIVE. It is important to
			    // leave these as negatives so that they can come before numerical versions
			    // and as if no letters were there to begin with.
			    // (1alpha is < 1 and < 1.1 but > 1dev1)
			    // If a non-numerical value can't be mapped to this table, it receives
			    // -7 as its value.
			    vm          = {
				    'dev':   -6,
				    'alpha': -5,
				    'a':     -5,
				    'beta':  -4,
				    'b':     -4,
				    'RC':    -3,
				    'rc':    -3,
				    '#':     -2,
				    'p':     -1,
				    'pl':    -1
			    },
			    // This function will be called to prepare each version argument.
			    // It replaces every _, -, and + with a dot.
			    // It surrounds any nonsequence of numbers/dots with dots.
			    // It replaces sequences of dots with a single dot.
			    //    version_compare('4..0', '4.0') == 0
			    // Important: A string of 0 length needs to be converted into a value
			    // even less than an unexisting value in vm (-7), hence [-8].
			    // It's also important to not strip spaces because of this.
			    //   version_compare('', ' ') == 1
			    prepVersion = function (v)
			    {
				    v = ('' + v).replace(/[_\-+]/g, '.');
				    v = v.replace(/([^.\d]+)/g, '.$1.').replace(/\.{2,}/g, '.');
				    return (!v.length ? [-8] : v.split('.'));
			    },
			    // This converts a version component to a number.
			    // Empty component becomes 0.
			    // Non-numerical component becomes a negative number.
			    // Numerical component becomes itself as an integer.
			    numVersion  = function (v)
			    {
				    return !v ? 0 : (isNaN(v) ? vm[v] || -7 : parseInt(v, 10));
			    };
			v1              = prepVersion(v1);
			v2              = prepVersion(v2);
			x               = Math.max(v1.length, v2.length);
			for (i = 0; i < x; i++)
			{
				if (v1[i] == v2[i])
				{
					continue;
				}
				v1[i] = numVersion(v1[i]);
				v2[i] = numVersion(v2[i]);
				if (v1[i] < v2[i])
				{
					compare = -1;
					break;
				}
				else if (v1[i] > v2[i])
				{
					compare = 1;
					break;
				}
			}
			if (!operator)
			{
				return compare;
			}

			// Important: operator is CASE-SENSITIVE.
			// "No operator" seems to be treated as less than
			// Any other values seem to make the function return null.
			switch (operator)
			{
				case '>':
				case 'gt':
					return (compare > 0);
				case '>=':
				case 'ge':
					return (compare >= 0);
				case '<=':
				case 'le':
					return (compare <= 0);
				case '==':
				case '=':
				case 'eq':
					return (compare === 0);
				case '<>':
				case '!=':
				case 'ne':
					return (compare !== 0);
				case '':
				case '<':
				case 'lt':
					return (compare < 0);
				default:
					return null;
			}
		}

		function checkUpdates()
		{
			var structure =
			    {
				    type:        "GET",
				    url:         'http://query.yahooapis.com/v1/public/yql',
				    data:        {
					    <?php if(KICKSTARTPRO): ?>
					    q:        'SELECT * FROM xml WHERE url="http://nocdn.akeebabackup.com/updates/kickstart.xml"',
					    <?php else: ?>
					    q:        'SELECT * FROM xml WHERE url="http://nocdn.akeebabackup.com/updates/kickstartpro.xml"',
					    <?php endif; ?>
					    format:   'json',
					    callback: 'updatesCallback'
				    },
				    cache:       true,
				    crossDomain: true,
				    jsonp:       'updatesCallback',
				    timeout:     15000
			    };
			$.ajax(structure);
		}

		function updatesCallback(msg)
		{
			$.each(msg.query.results.updates.update, function (i, el)
			{
				var myUpdate = {
					'version': el.version,
					'infourl': el.infourl['content'],
					'dlurl':   el.downloads.downloadurl.content
				}
				if (version_compare(myUpdate.version, akeeba_update.version, 'ge'))
				{
					akeeba_update = myUpdate;
				}
			});

			if (version_compare(akeeba_update.version, akeeba_version, 'gt'))
			{
				notifyAboutUpdates();
			}
		}

		function notifyAboutUpdates()
		{
			$('#update-version').text(akeeba_update.version);
			$('#update-dlnow').attr('href', akeeba_update.dlurl);
			$('#update-whatsnew').attr('href', akeeba_update.infourl);
			$('#update-notification').show('slow');
		}

		<?php callExtraFeature('onExtraHeadJavascript'); ?>
	</script>
	<?php
}

function echoTranslationStrings(){callExtraFeature('onLoadTranslations');$translation = AKText::getInstance();echo $translation->asJavascript();}function echoPage(){$edition         = KICKSTARTPRO ? 'Professional' : 'Core';$bestArchivePath = AKKickstartUtils::getBestArchivePath();$filelist        = AKKickstartUtils::getArchivesAsOptions($bestArchivePath);?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Akeeba Kickstart <?php echo $edition ?> <?php echo VERSION ?></title>
		<style type="text/css" media="all" rel="stylesheet">
			<?php echoCSS();?>
		</style>
		<?php if (@file_exists('jquery.min.js')): ?>
			<script type="text/javascript" src="jquery.min.js"></script>
		<?php else: ?>
			<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<?php endif; ?>
<?php if (@file_exists('json2.min.js')): ?>
			<script type="text/javascript" src="json2.min.js"></script>
		<?php else: ?>
			<script type="text/javascript" src="//yandex.st/json2/2011-10-19/json2.min.js"></script>
		<?php endif; ?>
<?php if (@file_exists('jquery-ui.min.js')): ?>
			<script type="text/javascript" src="jquery-ui.min.js"></script>
		<?php else: ?>
			<script type="text/javascript"
			        src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
		<?php endif; ?>
<?php echoHeadJavascript(); ?>
	</head>
	<body>

	<div id="automode" style="display:none;">
		AUTOMODEON
	</div>

	<div id="fade" class="black_overlay"></div>

	<div id="page-container">

		<div id="preextraction" class="white_content">
			<div id="ie7Warning" style="display:none;">
				<h2>Deprecated Internet Explorer version</h2>
				<p>
					This script is not guaranteed to work properly on Internet Explorer 8
					or earlier versions, or on Internet Explorer 9 and higher running
					in compatibility mode.
				</p>
				<p>
					Please use Internet Explorer 9 or later in native mode (the
					&quot;broken page&quot; icon next to the address bar should not be
					enabled). Alternatively, you may use the latest versions of Firefox,
					Safari, Google Chrome or Opera.
				</p>
			</div>

			<h2>THINGS_HEADER</h2>
			<ol>
				<li>THINGS_01</li>
				<li>THINGS_02</li>
				<li>THINGS_03</li>
				<li>THINGS_04</li>
				<li>THINGS_05</li>
				<li>THINGS_06</li>
				<li>THINGS_07</li>
				<li>THINGS_08</li>
				<li>THINGS_09</li>
			</ol>
			<a href="javascript:void(0)" onclick="closeLightbox();">CLOSE_LIGHTBOX</a>
		</div>

		<div id="genericerror" class="white_content">
			<pre id="genericerrorInner"></pre>
		</div>

		<div id="header">
			<div class="title">Akeeba Kickstart <?php echo $edition ?> 5.3.0</div>
		</div>

		<div id="update-notification" style="display: none">
			<p class="update-notify">UPDATE_HEADER</p>
			<p class="update-whyupdate">UPDATE_NOTICE</p>
			<p class="update-links">
				<a href="#" id="update-dlnow">UPDATE_DLNOW</a>
				<a href="#" id="update-whatsnew" target="_blank">UPDATE_MOREINFO</a>
			</p>
		</div>

		<div id="page1">
			<?php callExtraFeature('onPage1'); ?>

			<div id="page1-content">

				<div class="helpme">
					<span>NEEDSOMEHELPKS</span> <a
						href="https://www.akeebabackup.com/documentation/quick-start-guide/using-kickstart.html"
						target="_blank">QUICKSTART</a>
				</div>

				<div class="step1">
					<div class="circle">1</div>
					<h2>SELECT_ARCHIVE</h2>
					<div class="area-container">
						<?php callExtraFeature('onPage1Step1'); ?>
						<div class="clr"></div>

						<label for="kickstart.setup.sourcepath">ARCHIVE_DIRECTORY</label>
			<span class="field">
				<input type="text" id="kickstart.setup.sourcepath"
				       value="<?php echo htmlentities($bestArchivePath); ?>"/>
				<span class="button" id="reloadArchives" style="margin-top:0;margin-bottom:0">RELOAD_ARCHIVES</span>
			</span>
						<br/>

						<label for="kickstart.setup.sourcefile">ARCHIVE_FILE</label>
			<span class="field" id="sourcefileContainer">
				<?php if (!empty($filelist)): ?>
					<select id="kickstart.setup.sourcefile">
						<?php echo $filelist; ?>
					</select>
				<?php else: ?>
					<a href="https://www.akeebabackup.com/documentation/troubleshooter/ksnoarchives.html"
					   target="_blank">NOARCHIVESCLICKHERE</a>
				<?php endif; ?>
			</span>
						<br/>
						<label for="kickstart.jps.password">JPS_PASSWORD</label>
						<span class="field"><input type="password" id="kickstart.jps.password" value=""/></span>
					</div>
				</div>

				<div class="clr"></div>

				<div class="step2">
					<div class="circle">2</div>
					<h2>SELECT_EXTRACTION</h2>
					<div class="area-container">
						<label for="kickstart.procengine">WRITE_TO_FILES</label>
			<span class="field">
				<select id="kickstart.procengine">
					<option value="hybrid">WRITE_HYBRID</option>
					<option value="direct">WRITE_DIRECTLY</option>
					<option value="ftp">WRITE_FTP</option>
					<option value="sftp">WRITE_SFTP</option>
				</select>
			</span><br/>

						<label for="kickstart.setup.ignoreerrors">IGNORE_MOST_ERRORS</label>
						<span class="field"><input type="checkbox" id="kickstart.setup.ignoreerrors"/></span>

						<div id="ftp-options">
							<label for="kickstart.ftp.host">FTP_HOST</label>
							<span class="field"><input type="text" id="kickstart.ftp.host"
							                           value="localhost"/></span><br/>
							<label for="kickstart.ftp.port">FTP_PORT</label>
							<span class="field"><input type="text" id="kickstart.ftp.port" value="21"/></span><br/>
							<div id="ftp-ssl-passive">
								<label for="kickstart.ftp.ssl">FTP_FTPS</label>
								<span class="field"><input type="checkbox" id="kickstart.ftp.ssl"/></span><br/>
								<label for="kickstart.ftp.passive">FTP_PASSIVE</label>
								<span class="field"><input type="checkbox" id="kickstart.ftp.passive"
								                           checked="checked"/></span><br/>
							</div>
							<label for="kickstart.ftp.user">FTP_USER</label>
							<span class="field"><input type="text" id="kickstart.ftp.user" value=""/></span><br/>
							<label for="kickstart.ftp.pass">FTP_PASS</label>
							<span class="field"><input type="password" id="kickstart.ftp.pass" value=""/></span><br/>
							<label for="kickstart.ftp.dir">FTP_DIR</label>
				<span class="field">
                    <input type="text" id="kickstart.ftp.dir" value=""/>
                    <?php ?>
                </span><br/>

							<label for="kickstart.ftp.tempdir">FTP_TEMPDIR</label>
				<span class="field">
					<input type="text" id="kickstart.ftp.tempdir"
					       value="<?php echo htmlentities(AKKickstartUtils::getTemporaryPath()) ?>"/>
					<span class="button" id="checkFTPTempDir">BTN_CHECK</span>
					<span class="button" id="resetFTPTempDir">BTN_RESET</span>
				</span><br/>
							<label></label>
							<span class="button" id="testFTP">BTN_TESTFTPCON</span>
							<a id="notWorking" class="button"
							   href="https://www.akeebabackup.com/documentation/troubleshooter/kscantextract.html"
							   target="_blank">CANTGETITTOWORK</a>
							<br/>
						</div>

					</div>
				</div>

				<div class="clr"></div>

				<div class="step3">
					<div class="circle">3</div>
					<h2>FINE_TUNE</h2>
					<div style="text-align: center;">
						<span id="showFineTune" class="button bluebutton loprofile"
						      style="margin: 0">BTN_SHOW_FINE_TUNE</span>
					</div>
					<div id="fine-tune-holder" class="area-container" style="display: none">
						<label for="kickstart.tuning.min_exec_time">MIN_EXEC_TIME</label>
						<span class="field"><input type="text" id="kickstart.tuning.min_exec_time" value="1"/></span>
						<span>SECONDS_PER_STEP</span><br/>
						<label for="kickstart.tuning.max_exec_time">MAX_EXEC_TIME</label>
						<span class="field"><input type="text" id="kickstart.tuning.max_exec_time" value="5"/></span>
						<span>SECONDS_PER_STEP</span><br/>

						<label for="kickstart.stealth.enable">STEALTH_MODE</label>
						<span class="field"><input type="checkbox" id="kickstart.stealth.enable"/></span><br/>
						<label for="kickstart.stealth.url">STEALTH_URL</label>
						<span class="field"><input type="text" id="kickstart.stealth.url" value=""/></span><br/>

						<label for="kickstart.setup.renameback">RENAME_FILES</label>
						<span class="field"><input type="checkbox" id="kickstart.setup.renameback"
						                           checked="checked"/></span><br/>

						<label for="kickstart.setup.restoreperms">RESTORE_PERMISSIONS</label>
						<span class="field"><input type="checkbox" id="kickstart.setup.restoreperms"/></span><br/>
					</div>
				</div>

				<div class="clr"></div>

				<div class="step4">
					<div class="circle">4</div>
					<h2>EXTRACT_FILES</h2>
					<div class="area-container">
						<span></span>
						<span id="gobutton" class="button">BTN_START</span>
					</div>
				</div>

				<div class="clr"></div>

			</div>

			<div id="ftpdialog" style="display:none;">
				<p class="instructions alert alert-info">FTPBROWSER_LBL_INSTRUCTIONS</p>
				<div class="error alert alert-error" id="ftpBrowserErrorContainer">
					<h3>FTPBROWSER_LBL_ERROR</h3>
					<p id="ftpBrowserError"></p>
				</div>
				<ul id="ak_crumbs" class="breadcrumb"></ul>
				<div class="row-fluid">
					<div class="span12">
						<table id="ftpBrowserFolderList" class="table table-striped">
						</table>
					</div>
				</div>
			</div>
		</div>

		<div id="page2">
			<div id="page2a">
				<div class="circle">5</div>
				<h2>EXTRACTING</h2>
				<div class="area-container">
					<div id="warn-not-close">DO_NOT_CLOSE_EXTRACT</div>
					<div id="progressbar">
						<div id="progressbar-inner">&nbsp;</div>
					</div>
					<div id="currentFile"></div>
				</div>
			</div>

			<div id="extractionComplete" style="display: none">
				<div class="circle">6</div>
				<h2>RESTACLEANUP</h2>
				<div id="runInstaller" class="button">BTN_RUNINSTALLER</div>
				<div id="runCleanup" class="button" style="display:none">BTN_CLEANUP</div>
				<div id="gotoSite" class="button" style="display:none">BTN_SITEFE</div>
				<div id="gotoAdministrator" class="button" style="display:none">BTN_SITEBE</div>
				<div id="gotoPostRestorationRroubleshooting" style="display:none">
					<a href="https://www.akeebabackup.com/documentation/troubleshooter/post-restoration.html"
					   target="_blank">POSTRESTORATIONTROUBLESHOOTING</a>
				</div>
			</div>

			<div id="warningsBox" style="display: none;">
				<div id="warningsHeader">
					<h2>WARNINGS</h2>
				</div>
				<div id="warningsContainer">
					<div id="warnings"></div>
				</div>
			</div>

			<div id="error" style="display: none;">
				<h3>ERROR_OCCURED</h3>
				<p id="errorMessage"></p>
				<div id="gotoStart" class="button">BTN_GOTOSTART</div>
				<div>
					<a href="https://www.akeebabackup.com/documentation/troubleshooter/kscantextract.html"
					   target="_blank">CANTGETITTOWORK</a>
				</div>
			</div>
		</div>

		<div id="footer">
			<div class="copyright">Copyright &copy; 2008&ndash;<?php echo date('Y'); ?> <a
					href="http://www.akeebabackup.com">Nicholas K.
					Dionysopoulos / Akeeba Backup</a>. All legal rights reserved.<br/>

				This program is free software: you can redistribute it and/or modify it under the terms of
				the <a href="http://www.gnu.org/gpl-3.htmlhttp://www.gnu.org/copyleft/gpl.html">GNU General
					Public License</a> as published by the Free Software Foundation, either version 3 of the License,
				or (at your option) any later version.<br/>
				Design credits: <a href="http://internet-inspired.com/">Internet Inspired</a>, heavily modified by
				AkeebaBackup.com
			</div>
		</div>

	</div>

	</body>
	</html>
	<?php
}

function clearCodeCaches(){if (function_exists('opcache_reset')){opcache_reset();}if (function_exists('apc_clear_cache')){@apc_clear_cache();}}function removeKickstartFiles(AKAbstractPostproc $postProc){$postProc->unlink(basename(__FILE__));$dh = opendir(AKKickstartUtils::getPath());if ($dh !== false){$basename = basename(__FILE__, '.php');while (false !== $file = @readdir($dh)){if (strstr($file, $basename . '.ini')){$postProc->unlink($file);}}}$postProc->unlink('cacert.pem');$postProc->unlink('jquery.min.js');$postProc->unlink('json2.min.js');}function finalizeAfterRestoration(AKAbstractUnarchiver $unarchiver, AKAbstractPostproc $postProc){
    recursive_remove_directory('installation');rollbackAutomaticRenames($unarchiver, $postProc);foreach ($unarchiver->archiveList as $archive){$postProc->unlink($archive);}}function rollbackAutomaticRenames(AKAbstractUnarchiver $unarchiver, AKAbstractPostproc $postProc){$renameBack = AKFactory::get('kickstart.setup.renameback', true);if ($renameBack){$renames = $unarchiver->renameFiles;if (!empty($renames)){foreach ($renames as $original => $renamed){$postProc->rename($renamed, $original);}}}}

class AKCliParams{protected static $cliOptions = array();public static function parseOptions(){global $argc, $argv;if (!isset($argc) && !isset($argv)){$query = "";if (!empty($_GET)){foreach ($_GET as $k => $v){$query .= " $k";if ($v != ""){$query .= "=$v";}}}$query = ltrim($query);$argv  = explode(' ', $query);$argc  = count($argv);}$currentName = "";$options     = array();for ($i = 1; $i < $argc; $i++){$argument = $argv[$i];$value = $argument;if (strpos($argument, "-") === 0){$argument = ltrim($argument, '-');$name  = $argument;$value = null;if (strstr($argument, '=')){list($name, $value) = explode('=', $argument, 2);}$currentName = $name;if (!isset($options[$currentName]) || ($options[$currentName] == null)){$options[$currentName] = array();}}if ((!is_null($value)) && (!is_null($currentName))){$key = null;if (strstr($value, '=')){$parts = explode('=', $value, 2);$key   = $parts[0];$value = $parts[1];}$values = $options[$currentName];if (is_null($values)){$values = array();}if (is_null($key)){array_push($values, $value);}else{$values[$key] = $value;}$options[$currentName] = $values;}}self::$cliOptions = $options;}public static function getOption($key, $default = null, $array = false){if (!array_key_exists($key, self::$cliOptions)){self::$cliOptions[$key] = is_array($default) ? $default : array($default);}if ($array){return self::$cliOptions[$key];}return self::$cliOptions[$key][0];}public static function hasOption($key){return array_key_exists($key, self::$cliOptions);}}class CLIExtractionObserver extends ExtractionObserver{public static $silent = false;public function update($object, $message){parent::update($object, $message);if (self::$silent){return;}if (!is_object($message)){return;}if (!array_key_exists('type', get_object_vars($message))){return;}if ($message->type == 'startfile'){echo $message->content->file . "\n";}}}function kickstart_application_cli(){AKCliParams::parseOptions();$silent = AKCliParams::hasOption('silent');$year   = gmdate('Y');if (!$silent){echo <<< BANNER
Akeeba Kickstart CLI 5.3.0
Copyright (c) 2008-$year Akeeba Ltd / Nicholas K. Dionysopoulos
-------------------------------------------------------------------------------
Akeeba Kickstart is Free Software, distributed under the terms of the GNU General
Public License version 3 or, at your option, any later version.
This program comes with ABSOLUTELY NO WARRANTY as per sections 15 & 16 of the
license. See http://www.gnu.org/licenses/gpl-3.0.html for details.
-------------------------------------------------------------------------------


BANNER;
}$paths = AKCliParams::getOption('', array(), true);if (empty($paths)){global $argv;echo <<< HOWTOUSE
Usage: {$argv[0]} archive.jpa [output_path] [--password=yourPassword]
         [--silent] [--permissions] [--dry-run] [--ignore-errors]


HOWTOUSE;
die;}AKFactory::nuke();$targetPath  = isset($paths[1]) ? $paths[1] : getcwd();$targetPath  = realpath($targetPath);$archive     = $paths[0];$archive     = realpath($archive);$archivePath = dirname($archive);$archivePath = empty($archivePath) ? getcwd() : $archivePath;$archivePath = empty($archivePath) ? __DIR__ : $archivePath;$archiveName = basename($paths[0]);$archiveForDisplay = $archive;$cwd               = getcwd();if ($archivePath == realpath($cwd)){$archiveForDisplay = $archiveName;}if (!$silent){echo <<< BANNER
Extracting $archiveForDisplay
to folder  $targetPath

BANNER;
}AKFactory::set('kickstart.setup.sourcepath', $archivePath);AKFactory::set('kickstart.setup.sourcefile', $archiveName);AKFactory::set('kickstart.jps.password', AKCliParams::getOption('password'));AKFactory::set('kickstart.setup.restoreperms', AKCliParams::hasOption('permissions'));AKFactory::set('kickstart.setup.dryrun', AKCliParams::hasOption('dry-run'));AKFactory::set('kickstart.setup.ignoreerrors', AKCliParams::hasOption('ignore-errors'));AKFactory::set('kickstart.setup.renamefiles', array());AKFactory::set('kickstart.tuning.max_exec_time', 20);AKFactory::set('kickstart.tuning.run_time_bias', 75);AKFactory::set('kickstart.tuning.min_exec_time', 0);AKFactory::set('kickstart.procengine', 'direct');if (empty($targetPath)){$targetPath = AKKickstartUtils::getPath();}AKFactory::set('kickstart.setup.destdir', $targetPath);$unarchiver = AKFactory::getUnarchiver();$observer   = new CLIExtractionObserver();$unarchiver->attach($observer);if ($silent){CLIExtractionObserver::$silent = true;}if (!$silent){echo "\n\n";}$retArray = array('done' => false,);while (!$retArray['done']){$unarchiver->tick();$ret = $unarchiver->getStatusArray();if ($ret['Error'] != ''){$retArray['status']  = false;$retArray['done']    = true;$retArray['message'] = $ret['Error'];}elseif (!$ret['HasRun']){$retArray['files']    = $observer->filesProcessed;$retArray['bytesIn']  = $observer->compressedTotal;$retArray['bytesOut'] = $observer->uncompressedTotal;$retArray['status']   = true;$retArray['done']     = true;}else{$retArray['files']    = $observer->filesProcessed;$retArray['bytesIn']  = $observer->compressedTotal;$retArray['bytesOut'] = $observer->uncompressedTotal;$retArray['status']   = true;$retArray['done']     = false;}if (!is_null($observer->totalSize)){$retArray['totalsize'] = $observer->totalSize;$retArray['filelist']  = $observer->fileList;}$retArray['Warnings'] = $ret['Warnings'];$retArray['lastfile'] = $observer->lastFile;if (!empty($retArray['Warnings']) && !$silent){echo "\n\n";foreach ($retArray['Warnings'] as $line){echo "\t$line\n";}echo "\n";}}if (!$silent){echo "\n\n";}if (!$retArray['status']){if (!$silent){echo "An error has occurred:\n{$retArray['message']}\n\n";}exit(255);}$postProc = AKFactory::getPostProc();rollbackAutomaticRenames($unarchiver, $postProc);clearCodeCaches();}

function kickstart_application_web(){$retArray = array('status'  => true,'message' => null);$task = getQueryParam('task', 'display');$json = getQueryParam('json');$ajax = true;switch ($task){case 'checkTempdir':$retArray['status'] = false;if (!empty($json)){$data = json_decode($json, true);$dir  = @$data['kickstart.ftp.tempdir'];if (!empty($dir)){$retArray['status'] = is_writable($dir);}}break;case 'checkFTP':$retArray['status'] = false;if (!empty($json)){$data = json_decode($json, true);foreach ($data as $key => $value){AKFactory::set($key, $value);}if ($data['type'] == 'ftp'){$ftp = new AKPostprocFTP();}else{$ftp = new AKPostprocSFTP();}$retArray['message'] = $ftp->getError();$retArray['status']  = empty($retArray['message']);}break;case 'ftpbrowse':if (!empty($json)){$data = json_decode($json, true);$retArray =getListing($data['directory'], $data['host'], $data['port'], $data['username'], $data['password'], $data['passive'], $data['ssl']);}break;case 'sftpbrowse':if (!empty($json)){$data = json_decode($json, true);$retArray =getSftpListing($data['directory'], $data['host'], $data['port'], $data['username'], $data['password']);}break;case 'startExtracting':case 'continueExtracting':$retArray['status'] = false;if (!empty($json)){if ($task == 'startExtracting'){AKFactory::nuke();}$oldJSON = $json;$json    = json_decode($json, true);if (is_null($json)){$json = stripslashes($oldJSON);$json = json_decode($json, true);}if (!empty($json)){foreach ($json as $key => $value){if (substr($key, 0, 9) == 'kickstart'){AKFactory::set($key, $value);}}}if (array_key_exists('factory', $json)){$serialized = $json['factory'];AKFactory::unserialize($serialized);AKFactory::set('kickstart.enabled', true);}$removePath = AKFactory::get('kickstart.setup.destdir', '');if (empty($removePath)){AKFactory::set('kickstart.setup.destdir', AKKickstartUtils::getPath());}if ($task == 'startExtracting'){if (AKFactory::get('kickstart.stealth.enable', false)){createStealthURL();}}$engine   = AKFactory::getUnarchiver(); $observer = new ExtractionObserver(); $engine->attach($observer); $engine->tick();$ret = $engine->getStatusArray();if ($ret['Error'] != ''){$retArray['status']  = false;$retArray['done']    = true;$retArray['message'] = $ret['Error'];}elseif (!$ret['HasRun']){$retArray['files']    = $observer->filesProcessed;$retArray['bytesIn']  = $observer->compressedTotal;$retArray['bytesOut'] = $observer->uncompressedTotal;$retArray['status']   = true;$retArray['done']     = true;}else{$retArray['files']    = $observer->filesProcessed;$retArray['bytesIn']  = $observer->compressedTotal;$retArray['bytesOut'] = $observer->uncompressedTotal;$retArray['status']   = true;$retArray['done']     = false;$retArray['factory']  = AKFactory::serialize();}if (!is_null($observer->totalSize)){$retArray['totalsize'] = $observer->totalSize;$retArray['filelist']  = $observer->fileList;}$retArray['Warnings'] = $ret['Warnings'];$retArray['lastfile'] = $observer->lastFile;}break;case 'cleanUp':if (!empty($json)){$json = json_decode($json, true);if (array_key_exists('factory', $json)){$serialized = $json['factory'];AKFactory::unserialize($serialized);AKFactory::set('kickstart.enabled', true);}}$unarchiver = AKFactory::getUnarchiver(); $postProc   = AKFactory::getPostProc();finalizeAfterRestoration($unarchiver, $postProc);removeKickstartFiles($postProc);clearCodeCaches();break;case 'display':$ajax = false;echoPage();break;case 'isJoomla':$ajax = true;if (!empty($json)){$json = json_decode($json, true);if (array_key_exists('factory', $json)){$serialized = $json['factory'];AKFactory::unserialize($serialized);AKFactory::set('kickstart.enabled', true);}}$path     = AKFactory::get('kickstart.setup.destdir', '');$path     = rtrim($path, '/\\');$isJoomla = @is_dir($path . '/administrator');if ($isJoomla){$isJoomla = @is_dir($path . '/libraries/joomla');}$retArray = $isJoomla;break;case 'listArchives':$ajax = true;$path = null;if (!empty($json)){$json = json_decode($json, true);if (array_key_exists('path', $json)){$path = $json['path'];}}if (empty($path) || !@is_dir($path)){$filelist = null;}else{$filelist = AKKickstartUtils::getArchivesAsOptions($path);}if (empty($filelist)){$retArray ='<a href="https://www.akeebabackup.com/documentation/troubleshooter/ksnoarchives.html" target="_blank">' .AKText::_('NOARCHIVESCLICKHERE'). '</a>';}else{$retArray = '<select id="kickstart.setup.sourcefile">' . $filelist . '</select>';}break;default:$ajax = true;if (!empty($json)){$params = json_decode($json, true);}else{$params = array();}$retArray = callExtraFeature($task, $params);break;}if ($ajax){$json = json_encode($retArray);$password = AKFactory::get('kickstart.security.password', null);if (!empty($password)){$json = AKEncryptionAES::AESEncryptCtr($json, $password, 128);}echo "###$json###";}}

callExtraFeature();$isCli = !isset($_SERVER) || !is_array($_SERVER);if (isset($_SERVER) && is_array($_SERVER)){$isCli = !array_key_exists('REQUEST_METHOD', $_SERVER);}if (isset($_GET) && is_array($_GET) && !empty($_GET)){if (isset($_GET['cli'])){$isCli = $_GET['cli'] == 1;}elseif (isset($_GET['web'])){$isCli = $_GET['web'] != 1;}}if ($isCli){kickstart_application_cli();}else{kickstart_application_web();}

class AKFeatureURLImport{private $params = array();public function onExtraHeadCSS(){}public function onExtraHeadJavascript(){?>

		var akeeba_url_filename = null;

		$(document).ready(function(){
		$('#ak-url-showgui').click(function(e){
		$('#ak-url-gui').show('fast');
		$('#ak-url-progress').hide('fast');
		$('#ak-url-complete').hide('fast');
		$('#ak-url-error').hide('fast');
		$('#page1-content').hide('fast');
		});
		$('#ak-url-hidegui').click(function(e){
		$('#ak-url-gui').hide('fast');
		$('#ak-url-progress').hide('fast');
		$('#ak-url-complete').hide('fast');
		$('#ak-url-error').hide('fast');
		$('#page1-content').show('fast');
		});
		$('#ak-url-reload').click(function(e){
		window.location.reload();
		});
		$('#ak-url-gotoStart').click(function(e){
		$('#ak-url-gui').show('fast');
		$('#ak-url-progress').hide('fast');
		$('#ak-url-complete').hide('fast');
		$('#ak-url-error').hide('fast');
		});
		});

		function onAKURLImport()
		{
		akeeba_url_filename = $('#url\\.filename').val();
		ak_urlimport_start();
		}

		function AKURLsetProgressBar(percent)
		{
		var newValue = 0;

		if(percent <= 1) {
		newValue = 100 * percent;
		} else {
		newValue = percent;
		}

		$('#ak-url-progressbar-inner').css('width',percent+'%');
		}

		function ak_urlimport_start()
		{
		akeeba_error_callback = AKURLerrorHandler;

		$('#ak-url-gui').hide('fast');
		$('#ak-url-progress').show('fast');
		$('#ak-url-complete').hide('fast');
		$('#ak-url-error').hide('fast');

		AKURLsetProgressBar(0);
		$('#ak-url-progresstext').html('');

		var data = {
		'task' : 'urlimport',
		'json' : JSON.stringify({
		'file'        : akeeba_url_filename,
		'frag'        : "-1",
		'totalSize'    : "-1"
		})
		};
		doAjax(data, function(ret){
		ak_urlimport_step(ret);
		});
		}

		function ak_urlimport_step(data)
		{
		// Look for errors
		if(!data.status)
		{
		AKURLerrorHandler(data.error);
		return;
		}

		var totalSize = 0;
		var doneSize = 0;
		var percent = 0;
		var frag = -1;

		// get running stats
		if(array_key_exists('totalSize', data)) {
		totalSize = data.totalSize;
		}
		if(array_key_exists('doneSize', data)) {
		doneSize = data.doneSize;
		}
		if(array_key_exists('percent', data)) {
		percent = data.percent;
		}
		if(array_key_exists('frag', data)) {
		frag = data.frag;
		}

		// Update GUI
		AKURLsetProgressBar(percent);
		//$('#ak-url-progresstext').text( percent+'% ('+doneSize+' / '+totalSize+' bytes)' );
		$('#ak-url-progresstext').text( percent+'% ('+doneSize+' bytes)' );

		post = {
		'task'    : 'urlimport',
		'json'    : JSON.stringify({
		'file'        : akeeba_url_filename,
		'frag'        : frag,
		'totalSize'    : totalSize,
		'doneSize'  : doneSize
		})
		};

		if(percent < 100) {
		// More work to do
		doAjax(post, function(ret){
		ak_urlimport_step(ret);
		});
		} else {
		// Done!
		$('#ak-url-gui').hide('fast');
		$('#ak-url-progress').hide('fast');
		$('#ak-url-complete').show('fast');
		$('#ak-url-error').hide('fast');
		}
		}

		function onAKURLJoomla()
		{
		akeeba_error_callback = AKURLerrorHandler;

		var data = {
		'task' : 'getjurl'
		};

		doAjax(data, function(ret)
		{
		ak_urlimport_gotjurl(ret);
		});
		}

		function onAKURLWordpress()
		{
		$('#url\\.filename').val('http://wordpress.org/latest.zip');
		}

		function ak_urlimport_gotjurl(data)
		{
		var url = '';

		if(array_key_exists('url', data)) {
		url = data.url;
		}

		$('#url\\.filename').val(url);
		}

		function AKURLerrorHandler(msg)
		{
		$('#ak-url-gui').hide('fast');
		$('#ak-url-progress').hide('fast');
		$('#ak-url-complete').hide('fast');
		$('#ak-url-error').show('fast');

		$('#ak-url-errorMessage').html(msg);
		}
		<?php
}public function onPage1(){?>
		<div id="ak-url-gui" style="display: none">
			<div class="step1">
				<div class="circle">1</div>
				<h2>AKURL_TITLE_STEP1</h2>
				<div class="area-container">
					<label for="url.filename">AKURL_FILENAME</label>
					<span class="field"><input type="text" style="width: 45%" id="url.filename" value=""/></span>
					<a id="ak-url-joomla" class="button bluebutton loprofile" onclick="onAKURLJoomla()">AKURL_JOOMLA</a>
					<a id="ak-url-wordpress" class="button bluebutton loprofile" onclick="onAKURLWordpress()">AKURL_WORDPRESS</a>

					<div class="clr"></div>
					<a id="ak-url-connect" class="button" onclick="onAKURLImport()">AKURL_IMPORT</a>
					<a id="ak-url-hidegui" class="button bluebutton">AKURL_CANCEL</a>
				</div>
			</div>
			<div class="clr"></div>
		</div>

		<div id="ak-url-progress" style="display: none">
			<div class="circle">2</div>
			<h2>AKURL_TITLE_STEP2</h2>
			<div class="area-container">
				<div id="ak-url-importing">
					<div class="warn-not-close">AKURL_DO_NOT_CLOSE</div>
					<div id="ak-url-progressbar" class="progressbar">
						<div id="ak-url-progressbar-inner" class="progressbar-inner">&nbsp;</div>
					</div>
					<div id="ak-url-progresstext"></div>
				</div>
			</div>
		</div>

		<div id="ak-url-complete" style="display: none">
			<div class="circle">3</div>
			<h2>AKURL_TITLE_STEP3</h2>
			<div class="area-container">
				<div id="ak-url-reload" class="button">AKURL_BTN_RELOAD</div>
			</div>
		</div>

		<div id="ak-url-error" class="error" style="display: none;">
			<h3>ERROR_OCCURED</h3>
			<p id="ak-url-errorMessage" class="errorMessage"></p>
			<div id="ak-url-gotoStart" class="button">BTN_GOTOSTART</div>
		</div>
		<?php
}public function onPage1Step1(){?>
		<a id="ak-url-showgui" class="button bluebutton loprofile">AKURL_IMPORT</a>
		<?php
}public function urlimport($params){$this->params = $params;$filename  = $this->getParam('file');$frag      = $this->getParam('frag', -1);$totalSize = $this->getParam('totalSize', -1);$doneSize  = $this->getParam('doneSize', -1);debugMsg('Importing from URL');debugMsg('  file      : ' . $filename);debugMsg('  frag      : ' . $frag);debugMsg('  totalSize : ' . $totalSize);debugMsg('  doneSize  : ' . $doneSize);$retArray = array("status"    => true,"error"     => '',"frag"      => $frag,"totalSize" => $totalSize,"doneSize"  => $doneSize,"percent"   => 0,);try{AKFactory::set('kickstart.tuning.max_exec_time', '5');AKFactory::set('kickstart.tuning.run_time_bias', '75');$timer = new AKCoreTimer();$start = $timer->getRunningTime(); $break = false; while (($timer->getTimeLeft() > 0) && !$break){$fileparts = explode('?', $filename, 2);$local_file = KSROOTDIR . '/' . basename($fileparts[0]);debugMsg("- Importing from $filename");if ($frag == -1){debugMsg("-- First frag, killing local file");$doneSize = 0;@unlink($local_file);$fp = @fopen($local_file, 'wb');if ($fp !== false){@fclose($fp);}$frag = 0;}$length = 1048576;$from   = $frag * $length;$to     = $length + $from - 1;$temp_file = $local_file . '.tmp';@unlink($temp_file);$required_time = 1.0;debugMsg("-- Importing frag $frag, byte position from/to: $from / $to");$filesize = 0;try{$ch = curl_init();curl_setopt($ch, CURLOPT_URL, $filename);curl_setopt($ch, CURLOPT_RANGE, "$from-$to");curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);if (defined('AKEEBA_CACERT_PEM')){curl_setopt($ch, CURLOPT_CAINFO, AKEEBA_CACERT_PEM);}$result = curl_exec($ch);$errno       = curl_errno($ch);$errmsg      = curl_error($ch);$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);if ($result === false){$error = "cURL error $errno: $errmsg";}elseif ($http_status > 299){$result = false;$error  = "HTTP status $http_status";}else{$result = file_put_contents($temp_file, $result);if ($result === false){$error = "Could not open temporary file $temp_file for writing";}}curl_close($ch);}catch (Exception $e){$error = $e->getMessage();}if (!$result){@unlink($temp_file);if ($frag == 0){$retArray['status'] = false;$retArray['error']  = $error;debugMsg("-- Download FAILED");return $retArray;}else{$frag = -1;debugMsg("-- Import complete");$doneSize = $totalSize;$break    = true;continue;}}if ($result){clearstatcache();$filesize = (int) @filesize($temp_file);debugMsg("-- Successful download of $filesize bytes");$doneSize += $filesize;$fp = @fopen($local_file, 'ab');if ($fp === false){debugMsg("-- Can't open local file for writing");@unlink($temp_file);$retArray['status'] = false;$retArray['error']  = 'Can\'t write to the local file';return false;}$tf = fopen($temp_file, 'rb');while (!feof($tf)){$data = fread($tf, 262144);fwrite($fp, $data);}fclose($tf);fclose($fp);@unlink($temp_file);debugMsg("-- Temporary file merged and removed");if ($filesize > $length){debugMsg("-- Read more data than the requested length. I assume this file is complete.");$break = true;$frag  = - 1;}elseif ($filesize < $length){debugMsg("-- Read less data than the requested length. I assume this file is complete.");$break = true;$frag  = - 1;}else{$frag++;debugMsg("-- Proceeding to next fragment, frag $frag");}}$end = $timer->getRunningTime();$required_time = max(1.1 * ($end - $start), $required_time);if ($required_time > (10 - $end + $start)){$break = true;}$start = $end;}if ($frag == -1){$percent = 100;}elseif ($doneSize <= 0){$percent = 0;}else{if ($totalSize > 0){$percent = 100 * ($doneSize / $totalSize);}else{$percent = 0;}}$retArray = array("status"    => true,"error"     => '',"frag"      => $frag,"totalSize" => $totalSize,"doneSize"  => $doneSize,"percent"   => $percent,);}catch (Exception $e){debugMsg("EXCEPTION RAISED:");debugMsg($e->getMessage());$retArray['status'] = false;$retArray['error']  = $e->getMessage();}return $retArray;}private function getParam($key, $default = null){if (array_key_exists($key, $this->params)){return $this->params[$key];}else{return $default;}}public function getjurl($params){return array("url" => $this->getLatestJoomlaURL(),);}private function getLatestJoomlaURL(){$ch = curl_init();curl_setopt($ch, CURLOPT_URL, 'https://downloads.joomla.org/latest');curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);if (defined('AKEEBA_CACERT_PEM')){curl_setopt($ch, CURLOPT_CAINFO, AKEEBA_CACERT_PEM);}$pageHTML    = curl_exec($ch);$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);if (($pageHTML === false) || ($http_status >= 300)){return '';}$dom = new DOMDocument();$dom->loadHTML($pageHTML);$xml = $dom->saveXML();$doc = new SimpleXMLElement($xml);$links = $doc->xpath("//a[contains(@href,'format=zip')]");$dlLinkAttributes = $links[0]->attributes();return $this->resolveRedirect('https://downloads.joomla.org' . $dlLinkAttributes['href']);}private function resolveRedirect($url){$ch = curl_init();curl_setopt($ch, CURLOPT_URL, $url);curl_setopt($ch, CURLOPT_NOBODY, 1);curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);curl_setopt($ch, CURLOPT_HEADER, 1);@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);if (defined('AKEEBA_CACERT_PEM')){curl_setopt($ch, CURLOPT_CAINFO, AKEEBA_CACERT_PEM);}$headers     = curl_exec($ch);$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);if (($http_status < 300) || ($http_status > 399)){return $url;}$headers = explode("\r", $headers);$headers = array_map('trim', $headers);$newURL = '';foreach ($headers as $line){if (strpos($line, 'Location') === false){continue;}list($junk, $newURL) = explode(':', $line, 2);$newURL = trim($newURL);}return $this->resolveRedirect($newURL);}public function onLoadTranslations(){$translation = AKText::getInstance();$translation->addDefaultLanguageStrings(array('AKURL_IMPORT'       => "Import from URL",'AKURL_TITLE_STEP1'  => "Specify the URL",'AKURL_FILENAME'     => "URL to import",'AKURL_JOOMLA'       => "Latest Joomla! release",'AKURL_WORDPRESS'    => "Latest WordPress release",'AKURL_CANCEL'       => "Cancel import",'AKURL_TITLE_STEP2'  => "Importing...",'AKURL_DO_NOT_CLOSE' => "Please do not close this window while your backup archives are being imported",'AKURL_TITLE_STEP3'  => "Import is complete",'AKURL_BTN_RELOAD'   => "Reload Kickstart",));}}

class AKFeatureGeorgeWSpecialEdition{public function onExtraHeadCSS(){if (!isset($_REQUEST['george'])){return;}echo <<< CSS
html {
    background: #FFA500;
}

body {
	font-family: "Comic Sans MS";
}

#header {
	background: #FFA500;
	color: red;
	font-weight: bold;
}

#footer {
	color: #333;
	background: #FFA500;
}

#footer a {
	color: #f33;
}

h2 {
	background: #FFA500;
	color: #993300;
}

.button:active {
	border: 1px solid #FFA500;
}

.ui-button {
    font-family: "Comic Sans MS";
}

.ribbon-box {
   width:100%;
   height:400px;
   margin-top: -4.5em;
   right: 5px;
   position: absolute;
}
.ribbon {
   position: absolute;
   right: -5px; top: -5px;
   z-index: 1;
   overflow: hidden;
   width: 450px; height: 450px;
   text-align: right;
}
.ribbon span {
   font-size: 12pt;
   color: red;
   text-transform: uppercase;
   text-align: center;
   font-weight: bold;
   line-height: 20px;
   transform: rotate(45deg);
   -webkit-transform: rotate(45deg); /* Needed for Safari */
   width: 300px; display: block;
   background: #FFA500;
   background: linear-gradient(#FFA500 0%, #FFA500 100%);
   position: absolute;
   border: thin solid firebrick;
   top: 80px;
   right: -55px;
   box-shadow: yellow 1px 1px 20px;
   text-shadow: 1px 1px 2px white;
}
CSS;
}public function onExtraHeadJavascript(){if (!isset($_REQUEST['george'])){return;}echo <<< JS
// Matrix effect from http://thecodeplayer.com/walkthrough/matrix-rain-animation-html5-canvas-javascript
var c = null;
var ctx = null;

//japanese characters - taken from the unicode charset
var japanese = "あいうえおかきくけこさしすせそがぎぐげごぱぴぷぺぽ";
//converting the string into an array of single characters
japanese = japanese.split("");

var font_size = 14;
var columns = 0; //number of columns for the rain
//an array of drops - one per column
var drops = [];

//drawing the characters
function draw()
{
	//Black BG for the canvas
	//translucent BG to show trail
	ctx.fillStyle = "rgba(0, 0, 0, 0.05)";
	ctx.fillRect(0, 0, c.width, c.height);

	ctx.fillStyle = "#0F0"; //green text
	ctx.font = font_size + "px Arial";

	//looping over drops
	for(var i = 0; i < drops.length; i++)
	{
		//a random japanese character to print
		var text = japanese[Math.floor(Math.random()*japanese.length)];
		//x = i*font_size, y = value of drops[i]*font_size
		ctx.fillText(text, i*font_size, drops[i]*font_size);

		//sending the drop back to the top randomly after it has crossed the screen
		//adding a randomness to the reset to make the drops scattered on the Y axis
		if(drops[i]*font_size > c.height && Math.random() > 0.975)
			drops[i] = 0;

		//incrementing Y coordinate
		drops[i]++;
	}
}

/*
 * Konami Code Javascript Object
 * 1.3.0, 7 March 2014
 *
 * Using the Konami code, easily configure an Easter Egg for your page or any element on the page.
 *
 * Options:
 * - code : set your own custom code, takes array of keycodes / default is original Konami code
 * - cheat : the function to call when the proper sequence is entered
 * - elem : the element to set the instance on
 *
 * Copyright 2013 - 2014 Kurtis Kemple, http://kurtiskemple.com
 * Released under the MIT License
 */

var KONAMI = function ( options ) {
	var elem, ret, defaults, keycode, config, cache;

	// set the default code,function, and element
	defaults = {
		code : [38,38,40,40,37,39,37,39,66,65],
		cheat : null,
		elem : window
	};

	// build our return object
	ret = {

		/**
		 * handles the initialization of the KONAMI instance
		 *
		 * @param  {object} options the config to pass in to the instance
		 * @return {none}
		 * @method  init
		 * @public
		 */
		init : function ( options ) {
			cache = [], config = {};

			if ( options ) {

				for ( var key in defaults ) {

					if ( defaults.hasOwnProperty( key ) ) {

						if ( !options[ key ] ) {

							config[ key ] = defaults[ key ];
						} else {

							config[ key ] = options[ key ];
						}
					}
				}
			} else {

				config = defaults;
			}

			ret.bind( config.elem, 'keyup', ret.konami );
		},

		/**
		 * handles disassembling of the instance
		 *
		 * @return {none}
		 * @method  destroy
		 * @public
		 */
		destroy : function () {
			ret.unbind( config.elem, 'keyup', ret.konami );
			cache = config = null;
		},

		/**
		 * handles adding events to elements
		 *
		 * @param   {elem}     elem  DOM element to attach to
		 * @param   {string}   evt   the event type to bind to
		 * @param   {Function} fn    the function to bind
		 * @return  {none}
		 * @method  bind
		 * @private
		 */
		bind : function ( elem, evt, fn ) {
			if ( elem.addEventListener ) {

				elem.addEventListener( evt, fn, false );
			} else if ( elem.attachEvent ) {

				elem.attachEvent( 'on'+ evt, function( e ) {
					fn( e || window.event );
				});
			}
		},

		/**
		 * handles removing events from elements
		 *
		 * @param   {elem}     elem DOM element to remove from
		 * @param   {string}   evt  the event type to unbind
		 * @param   {Function} fn   the function to unbind
		 * @return  {none}
		 * @method  unbind
		 * @private
		 */
		unbind : function ( elem, evt, fn ) {
			if ( elem.removeEventListener ) {

				elem.removeEventListener( evt, fn, false );
			} else if ( elem.detachEvent ) {

				elem.detachEvent( 'on' + evt, function( e ) {
					fn( e || window.event );
				});
			}
		},

		/**
		 * handles the business logic for checking for valid konami code
		 *
		 * @param   {object} e the event object
		 * @return  {none}
		 * @method  konami
		 * @private
		 */
		konami : function( e ) {
			keycode = e.keyCode || e.which;

			if ( config.code.length > cache.push( keycode ) ) {

				return;
			}

			if ( config.code.length < cache.length ) {

				cache.shift();
			}

			if ( config.code.toString() !== cache.toString() ) {

				return;
			}

			config.cheat();
		}
	};

	ret.init( options );
	return ret;
};

var options = {
	cheat : function() {
		c = document.createElement('canvas');
		window.jQuery('html').html('').append(c);
		window.jQuery(c).attr('id', 'c');

		//making the canvas full screen
		c.height = window.innerHeight;
		c.width = window.innerWidth;
		c.left = 0;
		c.top = 0;

		ctx = c.getContext("2d");
		columns = c.width/font_size; //number of columns for the rain

		p1 = document.getElementById('page1');
		window.jQuery(p1).hide();

		//x below is the x coordinate
		//1 = y co-ordinate of the drop(same for every drop initially)
		for(var x = 0; x < columns; x++)
			drops[x] = 1;

		setInterval(draw, 33);
	}
};

var konamiCode = new KONAMI( options );

JS;
}public function onPage1(){if (!isset($_REQUEST['george'])){return;}echo <<< HTML
<div class="ribbon-box">
	<div class="ribbon">
		<span>George W. Edition</span>
	</div>
</div>
HTML;
}public function onPage1Step1(){}}
