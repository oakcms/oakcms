<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

// Protect from unauthorized access
use Awf\Text\Text;
use Solo\Helper\Escape;

/** @var  $this  Solo\View\Transfer\Html */

?>
<?php if ($this->force): ?>
    <div class="alert alert-danger">
        <h3><?php echo Text::_('COM_AKEEBA_TRANSFER_FORCE_HEADER') ?></h3>
        <p><?php echo Text::_('COM_AKEEBA_TRANSFER_FORCE_BODY') ?></p>
    </div>
<?php endif; ?>

<?php
echo $this->loadAnyTemplate('Transfer/default_dialogs');
echo $this->loadAnyTemplate('Transfer/default_prerequisites');

if (empty($this->latestBackup))
{
	return;
}

echo $this->loadAnyTemplate('Transfer/default_remoteconnection');
echo $this->loadAnyTemplate('Transfer/default_manualtransfer');
echo $this->loadAnyTemplate('Transfer/default_upload');