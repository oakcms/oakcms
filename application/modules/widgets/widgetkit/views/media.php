<div class="uk-modal-dialog uk-modal-dialog-large" data-media-path="">

    <div class="uk-modal-header">
        <div class="uk-h2">{{'Pick Media' | trans }}</div>
    </div>

        <div>
            <span class="uk-button uk-button-primary uk-form-file">{{'Upload' | trans }}<input id="wk-upload-select" type="file"></span>
            <button type="button" ng-click="vm.addFolder()" class="uk-button">{{'Add Folder' | trans }}</button>
            <button type="button" ng-click="vm.remove()" ng-show="media | filter : { selected : true } | length" class="uk-button uk-button-danger">{{'Delete' | trans }}</button>
        </div>

        <ul class="uk-breadcrumb uk-margin">
            <li ng-repeat="folder in breadcrumbs">
                <span ng-if="$last">{{ folder.title }}</span>
                <a ng-if="!$last" ng-click="vm.open(folder.path)">{{ folder.title }}</a>
            </li>
        </ul>

        <div class="uk-overflow-container">
            <ul class="uk-grid uk-grid-width-small-1-2 uk-grid-width-large-1-3 uk-grid-width-xlarge-1-4 uk-form" data-uk-grid-margin data-uk-grid-match="{target:'.uk-panel'}">

                <li ng-repeat="folder in media | filter: { type: 'folder' }">
                    <div ng-click="selectItem(folder, $event)" class="uk-panel uk-panel-box uk-text-center uk-visible-hover" ng-class="folder.selected ? 'wk-selected':''">
                        <div class="uk-panel-teaser">
                            <div class="wk-thumbnail wk-thumbnail-folder"></div>
                        </div>
                        <div class="uk-text-truncate">
                            <input type="checkbox" ng-if="folder.title" ng-click="$event.stopPropagation(); folder.selected = !folder.selected" ng-checked="folder.selected">
                            <a ng-click="vm.open(folder.path)">{{ folder.title || '..' }}</a>
                        </div>
                    </div>
                </li>

                <li ng-repeat="file in media | filter: { type: 'file' }">
                    <div ng-click="selectItem(file, $event)" class="uk-panel uk-panel-box uk-text-center uk-visible-hover" ng-class="file.selected ? 'wk-selected':''">
                        <div class="uk-panel-teaser">
                            <div ng-if="file.media" class="wk-thumbnail" style="background-image: url('{{ file.href }}');"></div>
                            <div ng-if="!file.media" class="wk-thumbnail wk-thumbnail-file"></div>
                        </div>
                        <div class="uk-text-nowrap uk-text-truncate">
                            <input type="checkbox" ng-checked="file.selected">
                            {{ file.title }}
                        </div>
                    </div>
                </li>

            </ul>
        </div>

        <div id="wk-upload-drop" class="uk-placeholder uk-text-center">
            {{'Drop files here' | trans}}
        </div>

        <div id="wk-upload-progressbar" class="uk-progress uk-hidden">
            <div class="uk-progress-bar" style="width: 0%;"></div>
        </div>

    <div class="uk-modal-footer">
        <button type="button" ng-click="vm.close()" class="uk-button">{{'Close' | trans}}</button>
        <button type="button" ng-click="vm.select()" ng-disabled="!(media | filter : { selected : true } | length)" class="uk-button uk-button-primary">{{'Select' | trans}}</button>
    </div>

</div>
