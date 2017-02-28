<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

// Protect from unauthorized access
use Awf\Html\Select;
use Awf\Text\Text;
use Awf\Uri\Uri;

/** @var  $this  Solo\View\Transfer\Html */

?>

<fieldset>
	<legend>
		<?php echo Text::_('COM_AKEEBA_TRANSFER_HEAD_REMOTECONNECTION'); ?>
	</legend>

	<div class="form form-horizontal">
		<div class="form-group">
			<label class="col-sm-3 control-label" for="akeeba-transfer-url">
				<?php echo Text::_('COM_AKEEBA_TRANSFER_LBL_NEWURL'); ?>
			</label>

			<div class="col-sm-9" id="akeeba-transfer-row-url">
                <div class="input-group">
                    <input type="text" class="form-control" id="akeeba-transfer-url" placeholder="http://www.example.com"
                           value="<?php echo htmlentities($this->newSiteUrl) ?>">
                    <span class="input-group-btn">
                        <button onclick="Solo.Transfer.onUrlChange(true);" class="btn btn-default" id="akeeba-transfer-btn-url">
                            <?php echo Text::_('COM_AKEEBA_TRANSFER_ERR_NEWURL_BTN') ?>
                        </button>
                    </span>
                </div>

				<img src="<?php echo Uri::base() ?>/media/loading.gif" id="akeeba-transfer-loading" style="display: none;" />

				<br/>

				<div id="akeeba-transfer-lbl-url">
					<small>
						<?php echo Text::_('COM_AKEEBA_TRANSFER_LBL_NEWURL_TIP'); ?>
					</small>
				</div>
				<div id="akeeba-transfer-err-url-same" class="alert alert-info" style="display: none;">
					<?php echo Text::_('COM_AKEEBA_TRANSFER_ERR_NEWURL_SAME'); ?>
					<p style="text-align: center">
						<iframe width="560" height="315" src="https://www.youtube.com/embed/vo_r0r6cZNQ" frameborder="0" allowfullscreen></iframe>
					</p>
				</div>
				<div id="akeeba-transfer-err-url-invalid" class="alert alert-danger" style="display: none;">
					<?php echo Text::_('COM_AKEEBA_TRANSFER_ERR_NEWURL_INVALID'); ?>
				</div>
				<div id="akeeba-transfer-err-url-notexists" class="alert alert-danger" style="display: none;">
					<p>
						<?php echo Text::_('COM_AKEEBA_TRANSFER_ERR_NEWURL_NOTEXISTS'); ?>
					</p>
					<p>
						<button type="button" class="btn btn-danger" id="akeeba-transfer-err-url-notexists-btn-ignore">
							&#9888;
							<?php echo Text::_('COM_AKEEBA_TRANSFER_ERR_NEWURL_BTN_IGNOREERROR') ?>
						</button>
					</p>
				</div>
			</div>
		</div>
	</div>

	<div class="form-horizontal" id="akeeba-transfer-ftp-container" style="display: none">
		<div class="form-group">
			<label for="akeeba-transfer-ftp-method" class="col-sm-3 control-label">
				<?php echo Text::_('COM_AKEEBA_TRANSFER_LBL_TRANSFERMETHOD'); ?>
			</label>
			<div class="col-sm-9">
				<?php echo Select::genericList($this->transferOptions, 'akeeba-transfer-ftp-method', array('class' => 'form-control'), 'value', 'text', $this->transferOption, 'akeeba-transfer-ftp-method') ?>
				<?php if ($this->hasFirewalledMethods): ?>
					<div class="help-block">
						<div class="alert alert-warning">
							<h4>
								<?php echo Text::_('COM_AKEEBA_TRANSFER_WARN_FIREWALLED_HEAD'); ?>
							</h4>
							<p>
								<?php echo Text::_('COM_AKEEBA_TRANSFER_WARN_FIREWALLED_BODY'); ?>
							</p>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<div class="form-group">
			<label for="akeeba-transfer-ftp-host" class="col-sm-3 control-label">
				<?php echo Text::_('COM_AKEEBA_TRANSFER_LBL_FTP_HOST'); ?>
			</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" value="<?php echo $this->ftpHost ?>" id="akeeba-transfer-ftp-host"
					   placeholder="ftp.example.com"/>
			</div>
		</div>

		<div class="form-group">
			<label for="akeeba-transfer-ftp-port" class="col-sm-3 control-label">
				<?php echo Text::_('COM_AKEEBA_TRANSFER_LBL_FTP_PORT'); ?>
			</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" value="<?php echo $this->ftpPort ?>" id="akeeba-transfer-ftp-port"
					   placeholder="21"/>
			</div>
		</div>

		<div class="form-group">
			<label for="akeeba-transfer-ftp-username" class="col-sm-3 control-label">
				<?php echo Text::_('COM_AKEEBA_TRANSFER_LBL_FTP_USERNAME'); ?>
			</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" value="<?php echo $this->ftpUsername ?>" id="akeeba-transfer-ftp-username"
					   placeholder="myUserName"/>
			</div>
		</div>

		<div class="form-group">
			<label for="akeeba-transfer-ftp-password" class="col-sm-3 control-label">
				<?php echo Text::_('COM_AKEEBA_TRANSFER_LBL_FTP_PASSWORD'); ?>
			</label>
			<div class="col-sm-9">
				<input type="password" class="form-control" value="<?php echo $this->ftpPassword ?>" id="akeeba-transfer-ftp-password"
					   placeholder="myPassword"/>
			</div>
		</div>

		<div class="form-group">
			<label for="akeeba-transfer-ftp-pubkey" class="col-sm-3 control-label">
				<?php echo Text::_('COM_AKEEBA_TRANSFER_LBL_FTP_PUBKEY'); ?>
			</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" value="<?php echo $this->ftpPubKey ?>" id="akeeba-transfer-ftp-pubkey"
					   placeholder="<?php echo APATH_SITE . DIRECTORY_SEPARATOR ?>id_rsa.pub"/>
			</div>
		</div>

		<div class="form-group">
			<label for="akeeba-transfer-ftp-privatekey" class="col-sm-3 control-label">
				<?php echo Text::_('COM_AKEEBA_TRANSFER_LBL_FTP_PRIVATEKEY'); ?>
			</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" value="<?php echo $this->ftpPrivateKey ?>" id="akeeba-transfer-ftp-privatekey"
					   placeholder="<?php echo APATH_SITE . DIRECTORY_SEPARATOR ?>id_rsa"/>
			</div>
		</div>

		<div class="form-group">
			<label for="akeeba-transfer-ftp-directory" class="col-sm-3 control-label">
				<?php echo Text::_('COM_AKEEBA_TRANSFER_LBL_FTP_DIRECTORY'); ?>
			</label>
			<div class="col-sm-9">
				<div class="input-append">
					<input type="text" class="form-control" value="<?php echo $this->ftpDirectory ?>" id="akeeba-transfer-ftp-directory"
						   placeholder="public_html"/>
					<!--
					<button class="btn" type="button" id="akeeba-transfer-ftp-directory-browse">
						<?php echo Text::_('COM_AKEEBA_CONFIG_UI_BROWSE'); ?>
					</button>
					<button class="btn" type="button" id="akeeba-transfer-ftp-directory-detect">
						<?php echo Text::_('COM_AKEEBA_TRANSFER_BTN_FTP_DETECT'); ?>
					</button>
					-->
				</div>
			</div>
		</div>

		<div class="form-group" id="akeeba-transfer-ftp-passive-container">
			<label for="akeeba-transfer-ftp-passive" class="col-sm-3 control-label">
				<?php echo Text::_('COM_AKEEBA_TRANSFER_LBL_FTP_PASSIVE'); ?>
			</label>
			<div class="col-sm-9">
				<?php echo Select::booleanList('akeeba-transfer-ftp-passive', array(), $this->ftpPassive ? 1 : 0, 'AWF_YES', 'AWF_NO', 'akeeba-transfer-ftp-passive') ?>
			</div>
		</div>

		<div class="form-group" id="akeeba-transfer-ftp-passive-fix-container">
			<label for="akeeba-transfer-ftp-passive-fix" class="col-sm-3 control-label">
				<?php echo Text::_('COM_AKEEBA_CONFIG_ENGINE_ARCHIVER_DIRECTFTPCURL_PASVWORKAROUND_TITLE'); ?>
			</label>
			<div class="col-sm-9">
				<?php echo Select::booleanList('akeeba-transfer-ftp-passive-fix', array(), $this->ftpPassive ? 1 : 0, 'AWF_YES', 'AWF_NO', 'akeeba-transfer-ftp-passive-fix') ?>
                <span class="help-block">
                    <?php echo Text::_('COM_AKEEBA_CONFIG_ENGINE_ARCHIVER_DIRECTFTPCURL_PASVWORKAROUND_DESCRIPTION'); ?>
                </span>
			</div>
		</div>

		<div class="alert alert-danger" id="akeeba-transfer-ftp-error" style="display:none;">
            <!--<h3 id="akeeba-transfer-ftp-error-title">TITLE</h3>-->
			<p id="akeeba-transfer-ftp-error-body">MESSAGE</p>

			<a href="<?php echo $this->getContainer()->router->route('index.php?view=transfer&force=1')?>"
			   class="btn btn-warning" style="display:none" id="akeeba-transfer-ftp-error-force">
				<?php echo Text::_('COM_AKEEBA_TRANSFER_ERR_OVERRIDE'); ?>
			</a>
		</div>

        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
                <button type="button" class="btn btn-primary" id="akeeba-transfer-btn-apply">
                    <?php echo Text::_('COM_AKEEBA_TRANSFER_BTN_FTP_PROCEED'); ?>
                </button>

                <span id="akeeba-transfer-apply-loading" style="display: none;">&nbsp;
				<span class="label label-info">
					<?php echo Text::_('COM_AKEEBA_TRANSFER_LBL_VALIDATING'); ?>
				</span>
				&nbsp;
				<img src="<?php echo Uri::base() ?>/media/loading.gif" />
			</span>

            </div>
        </div>

	</div>

</fieldset>