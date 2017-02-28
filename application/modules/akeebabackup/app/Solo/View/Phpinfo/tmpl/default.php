<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

$available = true;

$functions  = ini_get('disable_functions').',';
$functions .= ini_get('suhosin.executor.func.blacklist');

if ($functions)
{
    $array = preg_split('/,\s*/', $functions);

    if (in_array('phpinfo', $array))
    {
        $available = false;
    }
}


if($available)
{
    phpinfo();
}
else
{
    ?>
<div>
    <p class="alert alert-warning">
        <?php echo \Awf\Text\Text::_('SOLO_PHPINFO_DISABLED')?>
    </p>

    <p>
        <strong>PHP Version: </strong> <?php echo phpversion() ?>
    </p>
</div>
<?php
}