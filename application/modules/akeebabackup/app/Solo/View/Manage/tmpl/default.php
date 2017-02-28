<?php
/**
 * @package     Solo
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

use \Awf\Text\Text;

// Used for type hinting
/** @var \Solo\View\Manage\Html $this */

$router = $this->container->router;

$token = $this->container->session->getCsrfToken()->getValue();

$dateFormat = $this->getContainer()->appConfig->get('dateformat', '');
$dateFormat = trim($dateFormat);
$dateFormat = !empty($dateFormat) ? $dateFormat : Text::_('DATE_FORMAT_LC4');
$dateFormat = !empty($dateFormat) ? $dateFormat : Text::_('DATE_FORMAT_LC4');

// Timezone settings
$serverTimezone = new DateTimeZone($this->container->appConfig->get('timezone', 'UTC'));
$useLocalTime   = $this->container->appConfig->get('localtime', '1') == 1;
$timeZoneFormat = $this->container->appConfig->get('timezonetext', 'T');
?>

<?php if (!AKEEBABACKUP_PRO && (rand(0, 9) == 0)): ?>
    <div style="border: thick solid green; border-radius: 10pt; padding: 1em; background-color: #f0f0ff; color: #333; font-weight: bold; text-align: center; margin: 1em 0">
        <p><?php echo Text::_('SOLO_MAIN_LBL_SUBSCRIBE_TEXT') ?></p>
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="text-align: center; margin: 0px;">
            <input type="hidden" name="cmd" value="_s-xclick"/>
            <input type="hidden" name="hosted_button_id" value="3NTKQ3M2DYPYW"/>
            <button onclick="this.form.submit(); return false;" class="btn btn-success">
                <img src="https://www.paypal.com/en_GB/i/btn/btn_donate_LG.gif" border="0">
                Donate via PayPal
            </button>
            <a class="small" style="font-weight: normal; color: #666"
               href="https://www.akeebabackup.com/subscribe/new/backupwp.html?layout=default">
				<?php echo Text::_('SOLO_MAIN_BTN_SUBSCRIBE_UNOBTRUSIVE'); ?>
            </a>
        </form>
    </div>
<?php endif; ?>

<?php
// Restoration information prompt
$proKey = (defined('AKEEBABACKUP_PRO') && AKEEBABACKUP_PRO) ? 'PRO' : 'CORE';
if (\Akeeba\Engine\Platform::getInstance()->get_platform_configuration_option('show_howtorestoremodal', 1)):
	echo $this->loadAnyTemplate('Manage/howtorestore_modal');
else:
	?>
    <div class="alert alert-info">
        <button class="close" data-dismiss="alert">×</button>
        <h4 class="alert-heading"><?php echo Text::_('COM_AKEEBA_BUADMIN_LABEL_HOWDOIRESTORE_LEGEND') ?></h4>

		<?php echo Text::sprintf('COM_AKEEBA_BUADMIN_LABEL_HOWDOIRESTORE_TEXT_' . $proKey,
			'https://www.akeebabackup.com/videos/1214-akeeba-solo/1637-abts05-restoring-site-new-server.html',
			$router->route('index.php?view=Transfer'),
			'https://www.akeebabackup.com/latest-kickstart-core.zip'
		); ?>
    </div>
<?php endif; ?>

<form action="<?php echo $router->route('index.php?view=manage') ?>" method="post" name="adminForm" id="adminForm"
      role="form">
    <input type="hidden" name="boxchecked" id="boxchecked" value="0">
    <input type="hidden" name="task" id="task" value="default">
    <input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->lists->order ?>">
    <input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->lists->order_Dir ?>">
    <input type="hidden" name="token" value="<?php echo $token ?>">

    <table class="table table-striped" id="itemsList">
        <thead>
        <tr>
            <th width="20">
                <input type="checkbox" name="toggle" value="" onclick="Solo.System.checkAll(this);"/>
            </th>
            <th width="20" class="hidden-xs">
				<?php echo \Awf\Html\Grid::sort('COM_AKEEBA_BUADMIN_LABEL_ID', 'id', $this->lists->order_Dir, $this->lists->order, 'default'); ?>
            </th>
            <th width="30%">
				<?php echo \Awf\Html\Grid::sort('COM_AKEEBA_BUADMIN_LABEL_DESCRIPTION', 'description', $this->lists->order_Dir, $this->lists->order, 'default'); ?>
            </th>
            <th class="hidden-xs">
				<?php echo \Awf\Html\Grid::sort('COM_AKEEBA_BUADMIN_LABEL_PROFILEID', 'profile_id', $this->lists->order_Dir, $this->lists->order, 'default'); ?>
            </th>
            <th width="80">
				<?php echo \Awf\Html\Grid::sort('COM_AKEEBA_BUADMIN_LABEL_DURATION', 'backupstart', $this->lists->order_Dir, $this->lists->order, 'default'); ?>
            </th>
            <th width="80">
				<?php echo Text::_('COM_AKEEBA_BUADMIN_LABEL_STATUS'); ?>
            </th>
            <th width="110" class="hidden-xs">
				<?php echo Text::_('COM_AKEEBA_BUADMIN_LABEL_SIZE'); ?>
            </th>
            <th class="hidden-xs">
				<?php echo Text::_('COM_AKEEBA_BUADMIN_LABEL_MANAGEANDDL'); ?>
            </th>
        </tr>
        <tr>
            <td></td>
            <td class="hidden-xs"></td>
            <td class="form-inline">
                <div class="col-lg-12">
                    <div class="input-group">
                        <input type="text" name="filter_description" id="description"
                               class="form-control" onchange="document.adminForm.submit();"
                               value="<?php echo $this->escape($this->lists->fltDescription) ?>"
                               placeholder="<?php echo Text::_('SOLO_MANAGE_FIELD_DESCRIPTION') ?>">
                        <span class="input-group-btn">
								<button class="btn btn-default" type="button"
                                        title="<?php echo Text::_('SOLO_BTN_FILTER_SUBMIT'); ?>"
                                        onclick="this.form.submit(); return false;">
									<span class="glyphicon glyphicon-search"></span>
								</button>
								<button class="btn btn-default" type="button"
                                        title="<?php echo Text::_('SOLO_BTN_FILTER_CLEAR'); ?>"
                                        onclick="document.adminForm.description.value='';this.form.submit(); return;">
									<span class="glyphicon glyphicon-remove"></span>
								</button>
							</span>
                    </div>
                </div>
            </td>
            <td class="hidden-xs">
				<?php echo \Awf\Html\Select::genericList($this->profileList, 'filter_profile', array(
					'onchange' => "document.forms.adminForm.submit()",
					'class'    => 'form-control'
				), 'value', 'text', $this->lists->fltProfile); ?>
            </td>
            <td></td>
            <td></td>
            <td colspan="2" class="hidden-xs"></td>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <td colspan="11" class="center"><?php echo $this->pagination->getListFooter(); ?></td>
        </tr>
        </tfoot>
        <tbody>
		<?php if (!empty($this->list)): ?>
			<?php $i = 0;
			foreach ($this->list as $record):?>
				<?php
				$check = \Awf\Html\Grid::id(++$i, $record['id']);

				$backupId          = isset($record['backupid']) ? $record['backupid'] : '';
				$originLanguageKey = 'COM_AKEEBA_BUADMIN_LABEL_ORIGIN_' . strtoupper($record['origin']);
				$originDescription = Text::_($originLanguageKey);
				$originIcon        = 'akeeba-icon-origin-' . strtolower($record['origin']);

				if (empty($originLanguageKey) || ($originDescription == $originLanguageKey))
				{
					$originDescription = '&ndash;';
					$originIcon        = 'akeeba-icon-origin-unknown';
				}

				if (array_key_exists($record['type'], $this->backupTypes))
				{
					$type = $this->backupTypes[$record['type']];
				}
				else
				{
					$type = '&ndash;';
				}

				$gmtTimezone = new DateTimeZone('GMT');
				$startTime   = new \Awf\Date\Date($record['backupstart'], $gmtTimezone);
				$endTime     = new \Awf\Date\Date($record['backupend'], $gmtTimezone);

				if ($useLocalTime)
				{
					$startTime->setTimezone($serverTimezone);
				}

				$duration = $endTime->toUnix() - $startTime->toUnix();

				if ($duration > 0)
				{
					$seconds  = $duration % 60;
					$duration = $duration - $seconds;

					$minutes  = ($duration % 3600) / 60;
					$duration = $duration - $minutes * 60;

					$hours    = $duration / 3600;
					$duration = sprintf('%02d', $hours) . ':' . sprintf('%02d', $minutes) . ':' . sprintf('%02d', $seconds);
				}
				else
				{
					$duration = '';
				}

				// Label class based on status
				$status      = Text::_('COM_AKEEBA_BUADMIN_LABEL_STATUS_' . $record['meta']);
				$statusClass = '';
				switch ($record['meta'])
				{
					case 'ok':
						$statusIcon  = 'glyphicon-ok';
						$statusClass = 'label-success';
						break;
					case 'pending':
						$statusIcon  = 'glyphicon-play-circle';
						$statusClass = 'label-warning';
						break;
					case 'fail':
						$statusIcon  = 'glyphicon-remove';
						$statusClass = 'label-danger';
						break;
					case 'remote':
						$statusIcon  = 'glyphicon-cloud';
						$statusClass = 'label-info';
						break;
					default:
						$statusIcon  = 'glyphicon-trash';
						$statusClass = 'label-default';
						break;
				}

				$edit_link = $router->route('index.php?view=manage&task=showComment&id=' . $record['id'] . '&token=' . $token);

				if (empty($record['description']))
				{
					$record['description'] = Text::_('COM_AKEEBA_BUADMIN_LABEL_NODESCRIPTION');
				}
				?>
                <tr>
                    <td>
						<?php echo $check; ?>
                    </td>
                    <td class="hidden-xs">
						<?php echo $record['id']; ?>
                    </td>
                    <td>
				<span class="akeeba-icon <?php echo $originIcon ?> akeebaCommentPopover" rel="popover"
                      title="<?php echo Text::_('COM_AKEEBA_BUADMIN_LABEL_ORIGIN'); ?>"
                      data-content="<?php echo htmlentities($originDescription) ?>"
                ></span>

						<?php if (!empty($record['comment'])): ?>
                            <span class="glyphicon glyphicon-info-sign" rel="popover"
                                  data-content="<?php echo $this->escape($record['comment']) ?>"></span>
						<?php endif; ?>
                        <a href="<?php echo $edit_link; ?>">
							<?php echo $this->escape($record['description']) ?>
                        </a>
                        <br/>
                        <div style="border-top: 1px solid #eee; color: #999; padding-top: 2px; margin-top: 2px"
                             title="<?php echo Text::_('COM_AKEEBA_BUADMIN_LABEL_START') ?>">
                            <small>
                                <span class="fa fa-fw fa-calendar"></span>
								<?php echo $startTime->format($dateFormat, true); ?>
								<?php echo empty($timeZoneFormat) ? '' : $startTime->format($timeZoneFormat, true); ?>
                            </small>
                        </div>
                    </td>
                    <td class="hidden-xs">
						<?php
						$profileName = '&mdash;';

						if (isset($this->profiles[$record['profile_id']]))
						{
							$profileName = $this->escape($this->profiles[$record['profile_id']]->description);
						}
						?>
                        #<?php echo $record['profile_id'] ?>. <?php echo $profileName ?>
                        <br/>
                        <small>
                            <em><?php echo $type ?></em>
                        </small>
                    </td>
                    <td>
						<?php if ($duration): ?>
							<?php echo $duration; ?>
						<?php endif; ?>
                    </td>
                    <td>
				<span class="label <?php echo $statusClass; ?> akeebaCommentPopover" rel="popover"
                      title="<?php echo Text::_('COM_AKEEBA_BUADMIN_LABEL_STATUS') ?>"
                      data-content="<?php echo $status ?>"
                >
					<span class="glyphicon <?php echo $statusIcon; ?>"></span>
				</span>
                    </td>
                    <td class="hidden-xs"><?php echo ($record['meta'] == 'ok') ? \Solo\Helper\Format::fileSize($record['size']) : ($record['total_size'] > 0 ? "(<i>" . \Solo\Helper\Format::fileSize($record['total_size']) . "</i>)" : '&mdash;') ?></td>
                    <td class="hidden-xs">
						<?php echo $this->loadAnyTemplate('Manage/manage_column', array(
							'record' => &$record,
						)); ?>
                    </td>
                </tr>
			<?php endforeach; ?>
		<?php else: ?>
            <tr>
                <td colspan="11">
					<?php echo Text::_('SOLO_LBL_NO_RECORDS') ?>
                </td>
            </tr>
		<?php endif; ?>
        </tbody>
    </table>
</form>