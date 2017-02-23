<div class="uk-form uk-form-stacked">

    <div class="uk-form-row">
        <label class="uk-form-label" for="wk-content"><?php _e('Content') ?></label>
        <div class="uk-form-controls">
            <select id="wk-content" class="uk-form-width-large" ng-model="content.data['content']">
                <option value="intro"><?php _e('Intro Description') ?></option>
                <option value="full"><?php _e('Full Description') ?></option>
                <option value="excerpt"><?php _e('Short Description') ?></option>
            </select>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label" for="wk-category"><?php _e('Category') ?></label>
        <div class="uk-form-controls">
            <select id="wk-category" class="uk-form-width-large" ng-model="content.data['category']">
                <option value=""><?php _e('All') ?></option>
                <?php foreach (get_categories(array('taxonomy' => 'product_cat')) as $category) : ?>
                    <option value="<?php echo $category->cat_ID; ?>"><?php echo $category->name; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label" for="wk-number"><?php _e('Limit') ?></label>
        <div class="uk-form-controls">
            <input id="wk-number" class="uk-form-width-large" type="number" value="5" min="1" step="1" ng-model="content.data['number']">
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label" for="wk-order"><?php _e('Order') ?></label>
        <div class="uk-form-controls">
            <select id="wk-order" class="uk-form-width-large" ng-model="content.data['order_by']">
                <option value="post_none"><?php _e('Default') ?></option>
                <option value="post_date"><?php _e('Latest First') ?></option>
                <option value="post_date_asc"><?php _e('Latest Last') ?></option>
                <option value="post_title"><?php _e('Alphabetical') ?></option>
                <option value="post_title_asc"><?php _e('Alphabetical Reversed') ?></option>

                <option value="post_price"><?php _e('Price') ?></option>
                <option value="post_price_asc"><?php _e('Price Reversed') ?></option>
                <option value="post_sales"><?php _e('Sales') ?></option>
                <option value="post_sales_asc"><?php _e('Sales Reversed') ?></option>

                <option value="rand"><?php _e('Random') ?></option>
            </select>
        </div>
    </div>

</div>