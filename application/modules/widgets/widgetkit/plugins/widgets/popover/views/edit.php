<div class="uk-grid uk-grid-divider uk-form uk-form-horizontal" data-uk-grid-margin>
    <div class="uk-width-medium-1-4">

        <div class="wk-panel-marginless">
            <ul class="uk-nav uk-nav-side" data-uk-switcher="{connect:'#nav-content'}">
                <li><a href="">Popover</a></li>
                <li><a href="">{{'Media' | trans}}</a></li>
                <li><a href="">{{'Content' | trans}}</a></li>
                <li><a href="">{{'General' | trans}}</a></li>
            </ul>
        </div>

    </div>
    <div class="uk-width-medium-3-4">

        <ul id="nav-content" class="uk-switcher">
            <li>

                <h3 class="wk-form-heading">{{'Popover' | trans}}</h3>

                <div class="uk-form-row">
                    <label class="uk-form-label" for="wk-width">{{'Width (px)' | trans}}</label>
                    <div class="uk-form-controls">
                        <input id="wk-width" class="uk-form-width-medium" type="text" ng-model="widget.data['width']">
                    </div>
                </div>

                <div class="uk-form-row">
                    <label class="uk-form-label">{{'Image' | trans}}</label>
                    <div class="uk-form-controls">
                        <field-media title="item.title" media="widget.data.image"></field-media>
                        <p class="uk-form-controls-condensed">
                            <label><input class="uk-form-width-small" type="text" ng-model="widget.data['image_hero_width']"> {{'Width (px)' | trans}}</label>
                        </p>
                        <p class="uk-form-controls-condensed">
                            <label><input class="uk-form-width-small" type="text" ng-model="widget.data['image_hero_height']"> {{'Height (px)' | trans}}</label>
                        </p>
                    </div>
                </div>

                <div class="uk-form-row">
                    <label class="uk-form-label" for="wk-position">{{'Position' | trans}}</label>
                    <div class="uk-form-controls">
                        <select id="wk-position" class="uk-form-width-medium" ng-model="widget.data['position']">
                            <option value="top-center">Top</option>
                            <option value="bottom-center">Bottom</option>
                            <option value="left-center">Left</option>
                            <option value="right-center">Right</option>
                        </select>
                    </div>
                </div>

                <div class="uk-form-row">
                    <label class="uk-form-label" for="wk-mode">{{'Mode' | trans}}</label>
                    <div class="uk-form-controls">
                        <select id="wk-mode" class="uk-form-width-medium" ng-model="widget.data['mode']">
                            <option value="hover">Hover</option>
                            <option value="click">Click</option>
                        </select>
                    </div>
                </div>

                <div class="uk-form-row">
                    <label class="uk-form-label" for="wk-toggle">{{'Toggle Icon' | trans}}</label>
                    <div class="uk-form-controls">
                        <select id="wk-toggle" class="uk-form-width-medium" ng-model="widget.data['toggle']">
                            <option value="eye">{{'Eye' | trans}}</option>
                            <option value="info">{{'Info' | trans}}</option>
                            <option value="info-circle">{{'Info Circle' | trans}}</option>
                            <option value="search">{{'Search' | trans}}</option>
                            <option value="search-plus">{{'Search Plus' | trans}}</option>
                            <option value="plus">{{'Plus' | trans}}</option>
                            <option value="plus-square">{{'Plus Square' | trans}}</option>
                            <option value="plus-square-o">{{'Plus Square Outlined' | trans}}</option>
                            <option value="plus-circle">{{'Plus Circle' | trans}}</option>
                            <option value="map-marker">{{'Map Marker' | trans}}</option>
                            <option value="">{{'Custom' | trans}}</option>
                        </select>
                    </div>
                </div>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{'Color' | trans}}</span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['contrast']"> {{'Use a high-contrast color.' | trans}}</label>
                    </div>
                </div>

                <h3 class="wk-form-heading">{{'Items' | trans}}</h3>

                <div class="uk-form-row">
                    <label class="uk-form-label" for="wk-panel">{{'Panel' | trans}}</label>
                    <div class="uk-form-controls">
                        <select id="wk-panel" class="uk-form-width-medium" ng-model="widget.data['panel']">
                            <option value="box">{{'Box' | trans}}</option>
                            <option value="primary">{{'Box Primary' | trans}}</option>
                            <option value="secondary">{{'Box Secondary' | trans}}</option>
                        </select>
                        <p class="uk-form-controls-condensed">
                            <label><input type="checkbox" ng-model="widget.data['panel_link']"> {{'Link entire panel, if link exists' | trans}}</label>
                        </p>
                    </div>
                </div>

            </li>
            <li>

                <h3 class="wk-form-heading">{{'Media' | trans}}</h3>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{'Display' | trans}}</span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['media']"> {{'Show media' | trans}}</label>
                    </div>
                </div>

                <div class="uk-form-row">
                    <label class="uk-form-label">{{'Image' | trans}}</label>
                    <div class="uk-form-controls">
                        <label><input class="uk-form-width-small" type="text" ng-model="widget.data['image_width']"> {{'Width (px)' | trans}}</label>
                        <p class="uk-form-controls-condensed">
                            <label><input class="uk-form-width-small" type="text" ng-model="widget.data['image_height']"> {{'Height (px)' | trans}}</label>
                        </p>
                    </div>
                </div>

                <h3 class="wk-form-heading">{{'Overlay' | trans}}</h3>

                <div class="uk-form-row">
                    <label class="uk-form-label" for="wk-media-overlay">{{'Overlay' | trans}}</label>
                    <div class="uk-form-controls">
                        <select id="wk-media-overlay" class="uk-form-width-medium" ng-model="widget.data['media_overlay']">
                            <option value="none">{{'None' | trans}}</option>
                            <option value="link">{{'Link' | trans}}</option>
                            <option value="icon">{{'Icon' | trans}}</option>
                            <option value="image">{{'Image' | trans}} ({{'If second one exists' | trans}})</option>
                        </select>
                        <p class="uk-form-controls-condensed" ng-if="widget.data.media_overlay == 'icon'">
                            <label>
                                <select class="uk-form-width-small" ng-model="widget.data['overlay_animation']">
                                    <option value="fade">{{'Fade' | trans}}</option>
                                    <option value="slide-top">{{'Slide Top' | trans}}</option>
                                    <option value="slide-bottom">{{'Slide Bottom' | trans}}</option>
                                    <option value="slide-left">{{'Slide Left' | trans}}</option>
                                    <option value="slide-right">{{'Slide Right' | trans}}</option>
                                </select>
                                {{'Animation' | trans}}
                            </label>
                        </p>
                    </div>
                </div>

                <div class="uk-form-row">
                    <label class="uk-form-label" for="wk-thumbnail-animation">{{'Image Animation' | trans}}</label>
                    <div class="uk-form-controls">
                        <select id="wk-thumbnail-animation" class="uk-form-width-medium" ng-model="widget.data['media_animation']">
                            <option value="none">{{'None' | trans}}</option>
                            <option value="fade">{{'Fade' | trans}}</option>
                            <option value="scale">{{'Scale' | trans}}</option>
                            <option value="spin">{{'Spin' | trans}}</option>
                            <option value="grayscale">{{'Grayscale' | trans}}</option>
                        </select>
                    </div>
                </div>

            </li>
            <li>

                <h3 class="wk-form-heading">{{'Text' | trans}}</h3>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{'Display' | trans}}</span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <p class="uk-form-controls-condensed">
                            <label><input type="checkbox" ng-model="widget.data['title']"> {{'Show title' | trans}}</label>
                        </p>
                        <p class="uk-form-controls-condensed">
                            <label><input type="checkbox" ng-model="widget.data['content']"> {{'Show content' | trans}}</label>
                        </p>
                    </div>
                </div>

                <div class="uk-form-row">
                    <label class="uk-form-label" for="wk-title-size">{{'Title Size' | trans}}</label>
                    <div class="uk-form-controls">
                        <select id="wk-title-size" class="uk-form-width-medium" ng-model="widget.data['title_size']">
                            <option value="panel">{{'Default' | trans}}</option>
                            <option value="h1">H1</option>
                            <option value="h2">H2</option>
                            <option value="h3">H3</option>
                            <option value="h4">H4</option>
                            <option value="h5">H5</option>
                            <option value="h6">H6</option>
                            <option value="large">{{'Extra Large' | trans}}</option>
                        </select>
                    </div>
                </div>

                <div class="uk-form-row">
                    <label class="uk-form-label" for="wk-text-align">{{'Alignment' | trans}}</label>
                    <div class="uk-form-controls">
                        <select id="wk-text-align" class="uk-form-width-medium" ng-model="widget.data['text_align']">
                            <option value="left">{{'Left' | trans}}</option>
                            <option value="right">{{'Right' | trans}}</option>
                            <option value="center">{{'Center' | trans}}</option>
                        </select>
                    </div>
                </div>

                <h3 class="wk-form-heading">{{'Link' | trans}}</h3>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{'Display' | trans}}</span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['link']"> {{'Show link' | trans}}</label>
                    </div>
                </div>

                <div class="uk-form-row">
                    <label class="uk-form-label" for="wk-link-style">{{'Style' | trans}}</label>
                    <div class="uk-form-controls">
                        <select id="wk-link-style" class="uk-form-width-medium" ng-model="widget.data['link_style']">
                            <option value="text">{{'Text' | trans}}</option>
                            <option value="button">{{'Button' | trans}}</option>
                            <option value="primary">{{'Button Primary' | trans}}</option>
                            <option value="button-large">{{'Button Large' | trans}}</option>
                            <option value="primary-large">{{'Button Large Primary' | trans}}</option>
                            <option value="button-link">{{'Button Link' | trans}}</option>
                        </select>
                    </div>
                </div>

                <div class="uk-form-row">
                    <label class="uk-form-label" for="wk-link-text">{{'Text' | trans}}</label>
                    <div class="uk-form-controls">
                        <input id="wk-link-text" class="uk-form-width-medium" type="text" ng-model="widget.data['link_text']">
                    </div>
                </div>

            </li>
            <li>

                <h3 class="wk-form-heading">{{'General' | trans}}</h3>

                <div class="uk-form-row">
                    <span class="uk-form-label">{{'Link Target' | trans}}</span>
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" ng-model="widget.data['link_target']"> {{'Open all links in a new window' | trans}}</label>
                    </div>
                </div>

                <div class="uk-form-row">
                    <label class="uk-form-label" for="wk-class">{{'HTML Class' | trans}}</label>
                    <div class="uk-form-controls">
                        <input id="wk-class" class="uk-form-width-medium" type="text" ng-model="widget.data['class']">
                    </div>
                </div>

            </li>
        </ul>

    </div>
</div>
