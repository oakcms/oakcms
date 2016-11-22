<div class="wrap">

    <h2><?php _e('Widgetkit'); ?></h2>

    <form name="form" action="<?php echo add_query_arg(array('page' => $app['name'].'-config', 'action' => 'save'), admin_url('options-general.php')); ?>" method="post">

        <table class="form-table">
            <tr>
                <th><?php _e('YOOtheme API Key'); ?></th>
                <td>
                    <label for="config-apikey">
                        <input id="config-apikey" class="regular-text" type="text" name="config[apikey]" value="<?php echo $app['config']->get('apikey'); ?>">
                    </label>
                    <p class="description"><?php _e('In order to update commercial extensions set your API Key that can be found in your <a href="http://yootheme.com/account" target="_blank">YOOtheme account</a>.'); ?></p>
                </td>
            </tr>
        </table>

        <?php wp_nonce_field(); ?>
        <?php submit_button(); ?>

    </form>

</div>