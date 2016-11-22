<div ng-controller="pickerCtrl as vm" ng-switch="vm.view">
    <div ng-switch-when="content">

        <div class="uk-modal-header uk-form uk-flex uk-flex-middle">

            <div class="uk-flex-item-1 uk-h2 uk-margin-remove">{{'Select Widget' | trans}}</div>

            <input class="uk-form-width-small uk-margin-small-right" type="text" ng-show="data.content | length" ng-model="search.name" placeholder="{{'Search...' | trans}}">

            <select class="uk-form-width-small uk-margin-small-right" ng-model="search.data._widget.name" ng-options="widget.name as widget.label for widget in vm.widgets" ng-show="data.content | length"></select>

            <div class="uk-button-group uk-margin-small-right" ng-show="data.content | length">
                <button class="uk-button" ng-class="{'uk-active':(vm.viewmode == 'list')}" ng-click="vm.setViewMode('list')"><i class="uk-icon-bars"></i></button>
                <button class="uk-button" ng-class="{'uk-active':(vm.viewmode == 'blocks')}" ng-click="vm.setViewMode('blocks')"><i class="uk-icon-th"></i></button>
            </div>

            <button class="uk-button uk-button-primary" type="button" ng-click="vm.createContent()">{{'New' | trans}}</button>

        </div>

        <ul class="uk-grid uk-grid-width-small-1-2 uk-grid-width-medium-1-3 uk-margin-large-top uk-margin-large-bottom" data-uk-grid-margin ng-if="(vm.viewmode == 'blocks' && data.content | length)">
            <li ng-class="{'uk-active': vm.active(content)}" ng-repeat="content in data.content | toArray | filter:search | orderBy:'name'">

                <div class="uk-panel uk-panel-box uk-panel-box-hover uk-visible-hover" ng-click="vm.update(content)">

                    <div class="uk-panel-teaser uk-cover-background wk-image" ng-style="{'background-image': 'url(' + vm.previewContent(content) + ')'}"></div>

                    <a class="uk-position-cover" ng-click="vm.update(content)"></a>

                    <p class="uk-h4 uk-margin-top-remove uk-flex">
                        <span class="uk-flex-item-1 uk-text-truncate">{{ content.name }}</span>
                        <a class="uk-icon-hover uk-icon-pencil  uk-invisible uk-margin-small-right" ng-click="vm.editContent(content, 'content'); $event.stopPropagation()" title="{{'Edit' | trans}}"></a>
                        <a class="uk-icon-hover uk-icon-files-o uk-invisible uk-margin-small-right" ng-click="vm.copyContent(content); $event.stopPropagation()" title="{{'Copy' | trans}}"></a>
                        <a class="uk-icon-hover uk-icon-trash-o uk-invisible" ng-click="vm.deleteContent(content); $event.stopPropagation()"></a>
                    </p>

                </div>
            </li>
        </ul>

        <table class="uk-table uk-table-hover uk-table-middle wk-table uk-margin-large-top uk-margin-large-bottom" ng-if="(vm.viewmode == 'list' && data.content | length)">
            <tbody>
                <tr class="uk-visible-hover-inline" ng-class="{'uk-active': vm.active(content)}" ng-repeat="content in data.content | toArray | filter:search | orderBy:'name'">
                    <td class="uk-h4 uk-link-reset">
                        <a ng-click="vm.update(content)">
                            <div class="wk-preview-thumb uk-cover-background uk-margin-small-right" ng-style="{'background-image': 'url(' + vm.previewContent(content) + ')'}"></div>
                            {{ content.name }}
                        </a>
                    </td>
                    <td class="uk-h5 uk-text-nowrap uk-text-muted">{{ vm.getWidget(content).label }}</td>
                    <td class="wk-table-width-minimum uk-text-nowrap">
                        <a class="uk-icon-hover uk-icon-pencil  uk-invisible uk-margin-small-right" ng-click="vm.editContent(content, 'content'); $event.stopPropagation()" title="{{'Edit' | trans}}"></a>
                        <a class="uk-icon-hover uk-icon-files-o uk-invisible uk-margin-small-right" ng-click="vm.copyContent(content); $event.stopPropagation()" title="{{'Copy' | trans}}"></a>
                        <a class="uk-icon-hover uk-icon-trash-o uk-invisible" ng-click="vm.deleteContent(content); $event.stopPropagation()" title="{{'Delete' | trans}}"></a>
                    </td>
                </tr>
            </tbody>
        </table>

        <p class="uk-text-large uk-text-muted uk-text-center" ng-hide="data.content | length">
            {{"You haven't created any widgets yet." | trans}}
        </p>

        <div class="uk-modal-footer">
            <button class="uk-button" type="button" ng-click="vm.cancel()">{{'Close' | trans}}</button>
        </div>

    </div>
    <div ng-switch-when="contentConfig">

        <div class="uk-modal-header wk-modal-header-blank">
            <div class="uk-h2">{{content.id ? ('Edit %content%' | trans: {'content': content.name}) : 'New Widget' | trans}}</div>
        </div>

        <div class="uk-modal-header uk-form">

            <select class="uk-form-large uk-width-1-1" ng-model="content.type" ng-options="type.name as type.label for type in data.types | toArray">
                <option value="">- {{'Select Content Type' | trans}} -</option>
            </select>

        </div>

        <ul class="uk-grid uk-grid-width-1-2 uk-grid-width-small-1-3 uk-grid-width-medium-1-4 uk-margin-large-top uk-margin-large-bottom" data-uk-grid-margin>
            <li ng-repeat="wgt in data.widgets | toArray | filter:{core: 'true'}" ng-class="{'uk-active':(content.data._widget.name == wgt.name)}">

                <a class="uk-panel uk-panel-hover uk-text-center" ng-click="vm.selectWidget(wgt)">
                    <img ng-src="{{ wgt.icon }}" width="40" height="40" alt="{{ wgt.label }}">
                    <h3 class="uk-h4 uk-margin-top uk-margin-bottom-remove">{{ wgt.label }}</h3>
                </a>

            </li>
        </ul>

        <div ng-show="(data.widgets | toArray | filter:{core: '!true'}).length">

            <h3 class="wk-heading">{{'Theme' | trans}}</h3>

            <ul class="uk-grid uk-grid-width-1-2 uk-grid-width-small-1-3 uk-grid-width-medium-1-4 uk-margin-large-top uk-margin-large-bottom" data-uk-grid-margin>
                <li ng-repeat="wgt in data.widgets | toArray | filter:{core: '!true'}" ng-class="{'uk-active':(content.data._widget.name == wgt.name)}">

                    <a class="uk-panel uk-panel-hover uk-text-center" ng-click="vm.selectWidget(wgt)">
                        <img ng-src="{{ wgt.icon }}" width="40" height="40" alt="{{ wgt.label }}">
                        <h3 class="uk-h4 uk-margin-top uk-margin-bottom-remove">{{ wgt.label }}</h3>
                    </a>

                </li>
            </ul>
        </div>

        <div class="uk-modal-footer">
            <button class="uk-button" ng-click="content.id ? vm.editContent(content, 'content') : vm.setView('content')">{{'Cancel' | trans}}</button>
            <button class="uk-button uk-button-primary" ng-click="vm.editContent(content, 'content')" ng-disabled="!content.type || !content.data._widget.name">{{content.id ? 'Apply' : 'Create' | trans}}</button>
        </div>

    </div>
    <div ng-switch-when="contentEdit">

        <form class="uk-margin-remove" name="form" novalidate>

            <div class="uk-modal-header uk-form">
                <div class="uk-flex uk-flex-middle">
                    <div class="uk-margin-small-right">
                        <img ng-src="{{ widget.icon }}" width="30" height="30" alt="{{ widget.label }}">
                    </div>
                    <div class="uk-flex-item-1 uk-margin-right">
                        <input class="uk-form-large uk-form-blank wk-form-blank uk-width-1-1" type="text" ng-model="content.name" placeholder="{{'Name' | trans}}" required autofocus>
                    </div>
                    <div>
                        <ul class="uk-subnav wk-subnav">
                            <li ng-class="{'uk-active':(vm.include == 'content')}"><a ng-click="vm.setView('contentEdit', 'content')">{{'Content' | trans}}</a></li>
                            <li ng-class="{'uk-active':(vm.include == 'widget')}"><a ng-click="vm.setView('contentEdit', 'widget')">{{'Settings' | trans}}</a></li>
                            <li class="wk-subnav-divider"><a ng-click="vm.setView('contentConfig')"><i class="uk-icon-cog"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div ng-show="vm.include == 'content'" ng-include="content.type + '.edit'"></div>
            <div ng-show="vm.include == 'widget'" ng-include="widget.name + '.edit'"></div>

            <div class="uk-modal-footer">
                <button class="uk-button" type="button" ng-show="vm.mode != 'edit'" ng-click="vm.setView('content')">{{'Cancel' | trans}}</button>
                <button class="uk-button" type="button" ng-show="vm.mode == 'edit'" ng-click="vm.update(content)">{{'Close' | trans}}</button>
                <button class="uk-button uk-button-primary" ng-click="vm.saveContent(content)" ng-disabled="form.$invalid">{{'Save' | trans}}</button>
            </div>

        </form>

    </div>
</div>
