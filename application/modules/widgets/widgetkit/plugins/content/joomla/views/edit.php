<div class="uk-form uk-form-horizontal" ng-class="vm.name == 'contentCtrl' ? 'uk-width-large-2-3 wk-width-xlarge-1-2' : ''">

    <h3 class="wk-form-heading">{{'Content' | trans}}</h3>

    <div class="uk-form-row">
        <label class="uk-form-label" for="wk-category">{{'Category' | trans}}</label>
        <div class="uk-form-controls">
            <select id="wk-category" class="uk-form-width-large" ng-model="content.data['category']" multiple>
                <option value="0">{{'All' | trans}}</option>
                <?php foreach (\JHtml::_('category.options', 'com_content') as $option) : ?>
                    <option value="<?php echo $option->value ?>"><?php echo $option->text ?></option>
                <?php endforeach ?>
            </select>
            <p class="uk-form-controls-condensed">
                <label><input type="checkbox" ng-model="content.data['featured']" ng-true-value="1" ng-false-value="0"> {{'Featured articles only' | trans}}</label>
            </p>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label" for="wk-number">{{'Limit' | trans}}</label>
        <div class="uk-form-controls">
            <input id="wk-number" class="uk-form-width-large" type="number" value="5" min="1" step="1" ng-model="content.data['number']">
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label" for="wk-order">{{'Order' | trans}}</label>
        <div class="uk-form-controls">
            <select id="wk-order" class="uk-form-width-large" ng-model="content.data['order_by']">
                <option value="ordering">{{'Default' | trans}}</option>
                <option ng-if="content.data.featured" value="featured">{{'Featured Articles Order' | trans}}</option>
                <option value="rdate">{{'Latest First' | trans}}</option>
                <option value="date">{{'Latest Last' | trans}}</option>
                <option value="rmodified">{{'Modified First' | trans}}</option>
                <option value="modified">{{'Modified Last' | trans}}</option>
                <option value="alpha">{{'Alphabetical' | trans}}</option>
                <option value="ralpha">{{'Alphabetical Reversed' | trans}}</option>
                <option value="hits">{{'Most Hits' | trans}}</option>
                <option value="rhits">{{'Least Hits' | trans}}</option>
                <option value="random">{{'Random' | trans}}</option>
            </select>
        </div>
    </div>

    <h3 class="wk-form-heading uk-margin-large-top">{{'Mapping' | trans}}</h3>

    <div class="uk-form-row">
        <span class="uk-form-label">{{'Fields' | trans}}</span>
        <div class="uk-form-controls">

            <div class="uk-grid uk-grid-small uk-grid-width-1-2">
                <div>
                    <input class="uk-width-1-1" type="text" value="media" disabled>
                </div>
                <div>
                    <select class="uk-width-1-1" ng-model="content.data['image']">
                        <option value="intro">{{'Intro Image' | trans}}</option>
                        <option value="full">{{'Full Article Image' | trans}}</option>
                    </select>
                </div>
            </div>

            <div class="uk-grid uk-grid-small uk-grid-width-1-2">
                <div>
                    <input class="uk-width-1-1" type="text" value="content" disabled>
                </div>
                <div>
                    <select class="uk-width-1-1" ng-model="content.data['content']">
                        <option value="intro">{{'Intro Text' | trans}}</option>
                        <option value="full">{{'Full Text' | trans}}</option>
                    </select>
                </div>
            </div>

            <div class="uk-grid uk-grid-small uk-grid-width-1-2">
                <div>
                    <input class="uk-width-1-1" type="text" value="link" disabled>
                </div>
                <div>
                    <select class="uk-width-1-1" ng-model="content.data['link']">
                        <option value="">{{'Article Link' | trans}}</option>
                        <option value="a">{{'Link' | trans}} A</option>
                        <option value="b">{{'Link' | trans}} B</option>
                        <option value="c">{{'Link' | trans}} C</option>
                    </select>
                </div>
            </div>

            <div class="uk-grid uk-grid-small uk-grid-width-1-2">
                <div>
                    <input class="uk-width-1-1" type="text" value="date" disabled>
                </div>
                <div>
                    <select class="uk-width-1-1" ng-model="content.data['date']">
                        <option value="">{{'None' | trans}}</option>
                        <option value="publish_up">{{'Published' | trans}}</option>
                        <option value="created">{{'Created' | trans}}</option>
                    </select>
                </div>
            </div>

             <div class="uk-grid uk-grid-small uk-grid-width-1-2">
                <div>
                    <input class="uk-width-1-1" type="text" value="author" disabled>
                </div>
                <div>
                    <select class="uk-width-1-1" ng-model="content.data['author']">
                        <option value="">{{'None' | trans}}</option>
                        <option value="author">{{'Author' | trans}}</option>
                    </select>
                </div>
            </div>

            <div class="uk-grid uk-grid-small uk-grid-width-1-2">
                <div>
                    <input class="uk-width-1-1" type="text" value="categories" disabled>
                </div>
                <div>
                    <select class="uk-width-1-1" ng-model="content.data['categories']">
                        <option value="">{{'None' | trans}}</option>
                        <option value="categories">{{'Categories' | trans}}</option>
                    </select>
                </div>
            </div>

        </div>
    </div>

</div>
