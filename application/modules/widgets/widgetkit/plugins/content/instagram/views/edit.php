<div class="uk-form uk-form-horizontal" ng-class="vm.name == 'contentCtrl' ? 'uk-width-large-2-3 wk-width-xlarge-1-2' : ''">

    <h3 class="wk-form-heading">{{'Content' | trans}}</h3>

    <div class="uk-form-row">
        <label class="uk-form-label" for="wk-username">{{'Username' | trans}}</label>
        <div class="uk-form-controls">
            <input id="wk-username" class="uk-form-width-large" type="text" placeholder="{{ 'Username' | trans}}" ng-model="content.data['username']">
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label" for="wk-limit">{{'Limit' | trans}}</label>
        <div class="uk-form-controls">
            <input id="wk-limit" min="1" max="60" class="uk-form-width-large" type="number" ng-model="content.data['limit']">
        </div>
    </div>

    <h3 class="wk-form-heading">{{'Mapping' | trans}}</h3>

    <div class="uk-form-row">
        <span class="uk-form-label">{{'Fields' | trans}}</span>
        <div class="uk-form-controls">

            <div class="uk-grid uk-grid-small uk-grid-width-1-2">
                <div>
                    <input class="uk-width-1-1" type="text" value="title" disabled>
                </div>
                <div>
                    <select class="uk-width-1-1" ng-model="content.data['title']">
                        <option value="username">{{'Username' | trans}}</option>
                        <option value="fullname">{{'Full name' | trans}}</option>
                        <option value="combined">{{'Username and full name' | trans}}</option>
                    </select>
                </div>
            </div>

        </div>
    </div>
</div>
