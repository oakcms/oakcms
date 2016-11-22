<div class="uk-form" data-app="widgetkit" ng-controller="contentCtrl as vm" ng-switch="vm.view" ng-cloak>
    <div ng-switch-when="content">

        <h2 class="js-header">{{'Widgets' | trans}}</h2>

        <hr class="uk-margin-bottom">

        <div class="uk-clearfix">

            <div class="js-header uk-float-left">
                <button class="uk-button uk-button-primary" type="button" ng-click="vm.createContent()">{{'New' | trans}}</button>
            </div>

            <div class="uk-float-right" ng-show="data.content | length">
                <input class="uk-form-width-small uk-margin-small-right" type="text" ng-model="search.name" placeholder="{{'Search...' | trans}}">

                <select ng-model="search.data._widget.name" ng-options="widget.name as widget.label for widget in vm.widgets"></select>

                <div class="uk-button-group">
                    <button class="uk-button" ng-class="{'uk-active':(vm.viewmode == 'list')}" ng-click="vm.setViewMode('list')"><i class="uk-icon-bars"></i></button>
                    <button class="uk-button" ng-class="{'uk-active':(vm.viewmode == 'blocks')}" ng-click="vm.setViewMode('blocks')"><i class="uk-icon-th"></i></button>
                </div>
            </div>

        </div>

        <ul class="uk-margin-top uk-grid uk-grid-width-small-1-2 uk-grid-width-medium-1-3 uk-grid-width-xlarge-1-5" data-uk-grid-margin="observe:true" ng-if="(vm.viewmode == 'blocks' && data.content | length)">
            <li ng-repeat="content in data.content | toArray | filter:search | orderBy:'name'">

                <div class="uk-panel uk-panel-box uk-panel-box-hover uk-visible-hover">

                    <div class="uk-panel-teaser uk-cover-background wk-image" ng-style="{'background-image': 'url(' + vm.previewContent(content) + ')'}"></div>

                    <a class="uk-position-cover" ng-click="vm.editContent(content, 'content')"></a>

                    <p class="uk-h4 uk-margin-top-remove uk-flex">
                        <span class="uk-flex-item-1 uk-text-truncate">{{ content.name }}</span>
                        <a class="uk-icon-hover uk-icon-files-o uk-invisible uk-margin-small-right" ng-click="vm.copyContent(content); $event.stopPropagation()" title="{{'Copy' | trans}}"></a>
                        <a class="uk-icon-hover uk-icon-trash-o uk-invisible" ng-click="vm.deleteContent(content); $event.stopPropagation()" title="{{'Delete' | trans}}"></a>
                    </p>

                </div>
            </li>
        </ul>

        <div class="uk-panel uk-panel-box uk-margin" ng-if="(vm.viewmode == 'list' && data.content | length)">
            <div class="uk-overflow-container">
                <table class="uk-table uk-table-hover uk-table-middle wk-table">
                    <tbody>
                        <tr class="uk-visible-hover-inline" ng-repeat="content in data.content | toArray | filter:search | orderBy:'name'">
                            <td class="uk-h4 uk-link-reset uk-text-nowrap">
                                <a ng-click="vm.editContent(content, 'content')">
                                    <div class="wk-preview-thumb uk-cover-background uk-margin-right" ng-style="{'background-image': 'url(' + vm.previewContent(content) + ')'}"></div>
                                    {{ content.name }}
                                </a>
                            </td>
                            <td class="uk-h5 uk-text-nowrap uk-text-muted">{{ vm.getWidget(content).label }}</td>
                            <td class="uk-h5 uk-text-nowrap uk-text-muted">[widgetkit id="{{ content.id }}"]</td>
                            <td class="wk-table-width-minimum uk-text-nowrap">
                                <a class="uk-icon-hover uk-icon-files-o uk-invisible uk-margin-small-right" ng-click="vm.copyContent(content); $event.stopPropagation()" title="{{'Copy' | trans}}"></a>
                                <a class="uk-icon-hover uk-icon-trash-o uk-invisible" ng-click="vm.deleteContent(content); $event.stopPropagation()" title="{{'Delete' | trans}}"></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <p class="uk-text-large uk-text-muted uk-text-center" ng-hide="data.content | length">
            {{"You haven't created any widgets yet." | trans}}
        </p>

    </div>
    <div ng-switch-when="contentConfig">

        <h2 class="js-header">{{content.id ? ('Edit %content%' | trans: {'content': content.name}) : 'New Widget' | trans}}</h2>

        <hr class="uk-margin-bottom">

        <select class="uk-form-large uk-width-1-1 uk-margin-bottom" ng-model="content.type" ng-options="type.name as type.label for type in data.types | toArray" autofocus>
            <option value="">- {{'Select Content Type' | trans}} -</option>
        </select>

        <div class="uk-panel uk-panel-box">

            <ul class="uk-grid uk-grid-width-1-2 uk-grid-width-small-1-3 uk-grid-width-medium-1-4 uk-grid-width-large-1-5 wk-grid-width-xlarge-1-8 uk-margin-large-top uk-margin-large-bottom" data-uk-grid-margin>
                <li ng-repeat="wgt in data.widgets | toArray | filter:{core: 'true'}" ng-class="{'uk-active':(content.data._widget.name == wgt.name)}">

                    <a class="uk-panel uk-panel-hover uk-text-center" ng-click="vm.selectWidget(wgt)">
                        <img ng-src="{{ wgt.icon }}" width="40" height="40" alt="{{ wgt.label }}">
                        <h3 class="uk-h4 uk-margin-top uk-margin-bottom-remove">{{ wgt.label }}</h3>
                    </a>

                </li>
            </ul>

            <div ng-show="(data.widgets | toArray | filter:{core: '!true'}).length">

                <h3 class="wk-heading">{{'Theme' | trans}}</h3>

                <ul class="uk-grid uk-grid-width-1-2 uk-grid-width-small-1-3 uk-grid-width-medium-1-4 uk-grid-width-large-1-5 wk-grid-width-xlarge-1-8 uk-margin-large-top uk-margin-large-bottom" data-uk-grid-margin>
                    <li ng-repeat="wgt in data.widgets | toArray | filter:{core: '!true'}" ng-class="{'uk-active':(content.data._widget.name == wgt.name)}">

                        <a class="uk-panel uk-panel-hover uk-text-center" ng-click="vm.selectWidget(wgt)">
                            <img ng-src="{{ wgt.icon }}" width="40" height="40" alt="{{ wgt.label }}">
                            <h3 class="uk-h4 uk-margin-top uk-margin-bottom-remove">{{ wgt.label }}</h3>
                        </a>

                    </li>
                </ul>
            </div>

        </div>

        <p>
            <button class="uk-button uk-button-primary" ng-click="vm.editContent(content, 'content')" ng-disabled="!content.type || !content.data._widget.name">{{content.id ? 'Apply' : 'Create' | trans}}</button>
            <button class="uk-button" ng-click="content.id ? vm.editContent(content, 'content') : vm.setView('content')">{{'Cancel' | trans}}</button>
        </p>

    </div>
    <div ng-switch-when="contentEdit">

        <h2 class="uk-margin-bottom js-header">{{content.id ? ('Edit %content%' | trans: {'content': content.name}) : 'New Widget' | trans}}</h2>

        <hr class="uk-margin-bottom">

        <form name="form" novalidate>

            <div class="uk-grid uk-flex-middle uk-margin-bottom">
                <div class="uk-flex-item-1">
                    <input class="uk-form-large uk-width-1-1" type="text" ng-model="content.name" placeholder="{{'Name' | trans}}" required autofocus>
                </div>
                <div>
                    <ul class="uk-subnav wk-subnav">
                        <li ng-class="{'uk-active':(vm.include == 'content')}"><a ng-click="vm.setView('contentEdit', 'content')">{{'Content' | trans}}</a></li>
                        <li ng-class="{'uk-active':(vm.include == 'widget')}"><a ng-click="vm.setView('contentEdit', 'widget')">{{'Settings' | trans}}</a></li>
                        <li class="wk-subnav-divider"><a ng-click="vm.setView('contentConfig')"><i class="uk-icon-cog"></i></a></li>
                    </ul>
                </div>
            </div>

            <div class="uk-panel uk-panel-box" ng-show="vm.include == 'content'" ng-include="content.type + '.edit'"></div>
            <div class="uk-panel uk-panel-box" ng-show="vm.include == 'widget'" ng-include="widget.name + '.edit'"></div>

            <p class="js-action-buttons">
                <button class="uk-button uk-button-primary" ng-click="vm.saveContent()" ng-disabled="form.$invalid">{{'Save' | trans}}</button>
                <button class="uk-button" ng-click="vm.setView('content')">{{'Cancel' | trans}}</button>
            </p>

        </form>

    </div>

</div>
