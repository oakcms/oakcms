<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

/**
 * @var $data array
 * @var $slug string
 */
?>
    <div class="form-group formBuilder-block field-formbuilder-<?= $slug ?>">
        <label class="col-sm-3 control-label formControlLabel" for="fb_input_<?= $slug ?>">
            {<?= $slug ?>:label}
        </label>
        <div class="col-sm-9 formControls">
            {<?= $slug ?>:body}
            <div class="hint-block">{<?= $slug ?>:description}</div>
            <div class="help-block">{<?= $slug ?>:validation}</div>
        </div>
    </div>
