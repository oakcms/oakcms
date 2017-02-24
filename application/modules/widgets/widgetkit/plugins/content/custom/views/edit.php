<div ng-controller="customCtrl as custom">

    <div class="uk-grid uk-grid-divider uk-form uk-form-stacked" data-uk-grid-margin>
        <div ng-class="vm.name == 'contentCtrl' ? 'wk-width-xlarge-1-4' : ''" class="uk-width-medium-1-3">

            <div class="wk-panel-marginless">

                <ul id="js-content-items" class="uk-nav uk-nav-side uk-sortable" data-uk-sortable="{dragCustomClass:'wk-sortable wk-noconflict'}" ng-show="content.data.items.length">
                    <li class="uk-visible-hover" ng-repeat="item in content.data.items" ng-class="(item === $parent.item ? 'uk-active':'')">
                        <div class="wk-subnav-right uk-hidden">
                            <ol class="uk-subnav wk-subnav-icon">
                                <li>
                                    <a ng-click="custom.deleteItem(item)"><i class="uk-icon-times"></i></a>
                                </li>
                            </ol>
                        </div>
                        <a ng-click="custom.editItem(item)">
                            <div class="wk-preview-thumb uk-cover-background uk-margin-small-right" ng-style="{'background-image': 'url(' + custom.previewItem(item) + ')'}"></div>
                            {{ item.title }}
                        </a>
                    </li>
                </ul>

                <p class="uk-margin">
                    <button class="uk-button" ng-click="custom.addItem()">{{'Add Item' | trans}}</button>
                    <button class="uk-button" ng-click="custom.importItems()">{{'Add Media' | trans}}</button>
                </p>

                <div class="uk-form-row uk-margin-large-top">
                    <label class="uk-form-label">{{'Settings' | trans}}</label>
                    <div class="uk-form-controls uk-form-controls-condensed">
                        <label class="uk-flex uk-flex-middle {{ content.data['random'] ? '':'uk-text-muted'}}"><input class="uk-margin-small-right" type="checkbox" ng-model="content.data['random']" ng-true-value="1" ng-false-value="0"> {{'Random Order' | trans}}</label>
                    </div>
                    <div class="uk-form-controls uk-form-controls-condensed">
                        <label class="uk-flex uk-flex-middle {{ content.data['parse_shortcodes'] ? '':'uk-text-muted'}}"><input class="uk-margin-small-right" type="checkbox" ng-model="content.data['parse_shortcodes']" ng-true-value="1" ng-false-value="0"> {{'Parse shortcodes' | trans}}</label>
                    </div>
                </div>

            </div>

        </div>
        <div ng-class="vm.name == 'contentCtrl' ? 'wk-width-xlarge-3-4' : ''" class="uk-width-medium-2-3" ng-show="item">

            <div class="uk-form-row">
                <label class="uk-form-label" for="wk-title">{{'Title' | trans}}</label>
                <div class="uk-form-controls">
                    <input id="wk-title" class="uk-width-1-1" type="text" ng-model="item.title">
                </div>
            </div>

            <div class="uk-form-row">
                <label class="uk-form-label">{{'Media' | trans}}</label>
                <div class="uk-form-controls">
                    <field-media title="item.title" media="item.media" options="item.options['media']"></field-media>
                </div>
            </div>

            <div class="uk-form-row">
                <label class="uk-form-label" for="wk-content">{{'Content' | trans}}</label>
                <div class="uk-form-controls">
                    <field type="editor" id="wk-content" class="uk-width-1-1" ng-model="item.content" rows="10"></field>
                </div>
            </div>

            <div class="uk-form-row">
                <label class="uk-form-label" for="wk-link">{{'Link' | trans}}</label>
                <div class="uk-form-controls">
                    <field type="text" options='{"attributes":{"id":"wk-link", "placeholder":"http://"}, "icon":"link"}' ng-model="item.link"></field>
                </div>
            </div>

            <div class="uk-form-row" ng-repeat="field in extrafields" ng-show="!custom.editfields">
                <label class="uk-form-label" for="wk-field-{{ $index }}">{{ field.label }}</label>
                <div class="uk-form-controls" ng-switch="field.type">
                    <field-media ng-switch-when="media" media="item[field.name]" options="item.options[field.name]"></field-media>
                    <field ng-switch-default type="{{ field.type }}" options='{{ custom.getFieldOptions(field, $index) }}' ng-model="item[field.name]" options="item.options[field.name]"></field>
                </div>
            </div>

            <div class="uk-panel uk-panel-box uk-panel-box-primary uk-margin-large-top" ng-show="custom.editfields">

                <h3 class="uk-h3">{{'Manage Custom Fields' | trans}}</h3>

                <div class="uk-margin uk-sortable" id="js-fields-items" data-uk-sortable ng-show="extrafields.length">
                    <div class="uk-margin-small" ng-repeat="field in extrafields">
                        <div class="uk-panel uk-panel-box wk-panel-small" ng-switch="(custom.editField==field ? 'edit':'')">

                            <div ng-switch-when="edit">

                                <div class="uk-grid uk-grid-width-1-3">
                                    <div>

                                        <label class="uk-form-label">{{'Label' | trans}}</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-width-1-1" type="text" ng-model="field.label" placeholder="{{'Field label' | trans}}">
                                        </div>

                                    </div>
                                    <div>

                                        <label class="uk-form-label">{{'Name' | trans}}</label>
                                        <div class="uk-form-controls">
                                            <input class="uk-width-1-1" type="text" ng-model="field.name" placeholder="{{'Field name' | trans}}" disabled>
                                        </div>

                                    </div>
                                    <div>

                                        <label class="uk-form-label">{{'Type' | trans}}</label>
                                        <div class="uk-form-controls">
                                            <select class="uk-width-1-1" ng-model="field.type" ng-options="f.name as f.label for f in custom.fields" disabled></select>
                                        </div>

                                    </div>
                                </div>

                                <p class="uk-margin-bottom-remove">
                                    <button class="uk-button" ng-click="custom.editField=false" type="button">{{'Close' | trans}}</button>
                                </p>

                            </div>

                            <div ng-switch-default>

                                <span>{{ field.label || field.name }}</span>

                                <ul class="uk-subnav uk-margin-bottom-remove uk-float-right">
                                    <li class="uk-disabled"><span>{{ field.type }}</span></li>
                                    <li><a ng-click="custom.editField=field"><i class="uk-icon-pencil"></i></a></li>
                                    <li><a ng-click="custom.deleteField(field)"><i class="uk-icon-trash-o"></i></a></li>
                                </ul>

                            </div>

                        </div>
                    </div>
                </div>

                <div class="uk-margin-top" ng-show="custom.addCustomField && !custom.editField">

                    <div class="uk-panel uk-panel-box wk-panel-small">

                        <div class="uk-grid uk-grid-width-1-3">
                            <div>

                                <label class="uk-form-label">{{'Label' | trans}}</label>
                                <div class="uk-form-controls">
                                    <input class="uk-width-1-1" type="text" ng-model="custom.custom.field.label" placeholder="{{'Field label' | trans}}">
                                </div>

                            </div>
                            <div>

                                <label class="uk-form-label">{{'Name' | trans}}</label>
                                <div class="uk-form-controls">
                                    <input class="uk-width-1-1" type="text" ng-model="custom.custom.field.name" placeholder="{{'Field name' | trans}}">
                                </div>

                            </div>

                            <div>

                                <label class="uk-form-label">{{'Type' | trans}}</label>
                                <div class="uk-form-controls">
                                    <select class="uk-width-1-1" ng-model="custom.custom.field.type" ng-options="f.name as f.label for f in custom.fields"></select>
                                </div>

                            </div>

                        </div>

                        <p>
                            <button class="uk-button uk-button-success" ng-click="custom.addField(custom.custom.field);custom.addCustomField=false" ng-disabled="!(custom.custom.field.name && custom.custom.field.label && custom.custom.field.type)" type="button">{{'Add' | trans}}</button>
                            <button class="uk-button" ng-click="custom.addCustomField=false" type="button">{{'Cancel' | trans}}</button>
                        </p>

                    </div>

                </div>

                <div class="uk-margin-top" ng-show="!custom.addCustomField">
                    <div class="uk-button-dropdown" data-uk-dropdown="{ mode: 'click' }" >
                        <button class="uk-button uk-button-primary" type="button">{{'New Field' | trans}} &nbsp; <i class="uk-icon-caret-down"></i></button>
                        <div class="uk-dropdown uk-dropdown-up uk-dropdown-small uk-text-left">
                            <ul class="uk-nav uk-nav-dropdown">
                                <li class="uk-nav-header">{{'Field Types' | trans}}</li>
                                <li ng-repeat="(fieldname, fieldsettings) in custom.corefields" ng-show="!custom.hasField(fieldname)"><a ng-click="custom.addField({name:fieldname, type:fieldsettings.type, label:fieldsettings.label, core:true})">{{ fieldsettings.label}}</a></li>
                                <li class="uk-nav-divider"></li>
                                <li><a ng-click="custom.custom.field={};custom.addCustomField=true">{{'Custom' | trans}} <i class="uk-icon-magic"></i></a></li>
                            </ul>
                        </div>
                    </div>

                    <a class="uk-button" ng-click="custom.toggleEditFields()">{{'Close' | trans}}</a>
                </div>

            </div>

            <div class="uk-margin-large-top" ng-show="!custom.editfields">
                <a class="uk-button" ng-click="custom.toggleEditFields()">{{'Manage Fields' | trans}}</a>
            </div>

        </div>
    </div>

</div>
