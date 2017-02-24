<div class="uk-form uk-form-horizontal" ng-controller="folderCtrl as folder">
    <h3 class="wk-form-heading">{{'Content' | trans}}</h3>

    <div class="uk-form-row">
        <label class="uk-form-label" for="wk-path">Folder Path</label>
        <div class="uk-form-controls">
            <input id="wk-path" class="uk-form-width-large" type="text" ng-model="content.data['folder']">
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label" for="wk-order">{{'Order' | trans}}</label>
        <div class="uk-form-controls">
            <select id="wk-order" class="uk-form-width-large" ng-model="content.data['sort_by']">
                <option value="filename_asc">{{'Alphabetical' | trans}}</option>
                <option value="filename_desc">{{'Alphabetical Reversed' | trans}}</option>
                <option value="date_desc">{{'Latest First' | trans}}</option>
                <option value="date_asc">{{'Latest Last' | trans}}</option>
                <option value="random">{{'Random' | trans}}</option>
            </select>
        </div>
    </div>

    <div class="uk-form-row">
        <span class="uk-form-label">{{'Title' | trans}}</span>
        <div class="uk-form-controls uk-form-controls-text">
            <p class="uk-form-controls-condensed">
                <label><input type="checkbox" ng-model="content.data['strip_leading_numbers']"> {{'Remove leading numbers from title' | trans}}</label>
            </p>
            <p class="uk-form-controls-condensed">
                <label><input type="checkbox" ng-model="content.data['strip_trailing_numbers']"> {{'Remove trailing numbers from title' | trans}}</label>
            </p>
        </div>
    </div>

    <div class="uk-form-row">
        <label class="uk-form-label" for="wk-max-images">{{'Max Images' | trans}}</label>
        <div class="uk-form-controls">
            <input id="wk-max-images" class="uk-form-width-large" type="text" ng-model="content.data['max_images']" placeholder="{{ 'Leave empty to load all images' | trans }}" >
        </div>
    </div>

</div>
