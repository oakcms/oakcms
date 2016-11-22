<div class="subhead-collapse collapse" style="height: 43px;">
    <div class="subhead">
        <div class="container-fluid">
            <div id="container-collapse" class="container-collapse"></div>
            <div class="row-fluid">
                <div class="span12">
                    <div class="btn-toolbar" id="toolbar"><div class="btn-wrapper" id="toolbar-create"><button class="btn btn-small btn-success">New</button></div><div class="btn-wrapper" id="toolbar-options"><button class="btn btn-small"><span class="icon-options"></span> Options</button></div></div>

                </div>
            </div>
        </div>
    </div>
</div>
<?php
\mihaildev\elfinder\AssetsCallBack::register($this);
$app->handle();
