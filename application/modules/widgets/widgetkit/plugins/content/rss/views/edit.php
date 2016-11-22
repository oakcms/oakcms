<div class="uk-form uk-form-horizontal" ng-class="vm.name == 'contentCtrl' ? 'uk-width-large-2-3 wk-width-xlarge-1-2' : ''">

    <h3 class="wk-form-heading">{{'Content' | trans}}</h3>

    <div class="uk-form-row">
        <label class="uk-form-label" for="wk-source">{{'Source' | trans}}</label>
        <div class="uk-form-controls">
            <input id="wk-source" class="uk-form-width-large" type="text" placeholder="{{ 'Source' | trans}}" ng-model="content.data['src']">
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label" for="wk-limit">{{'Limit' | trans}}</label>
        <div class="uk-form-controls">
            <input id="wk-limit" min="1" max="60" class="uk-form-width-large" type="number" ng-model="content.data['limit']">
        </div>
    </div>
</div>
