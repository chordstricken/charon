<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Charon - Dashboard</title>

    <link href="/css/rollup.css.php" rel="stylesheet">
    <link href="/css/home.css" rel="stylesheet">
    <script type="text/javascript" src="/js/rollup.js.php"></script>
    <script type="text/javascript" src="/js/home.js"></script>
</head>

<body ng-app="Charon">

<div ng-controller="Home">

    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar, #sidebar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#/" tabindex="-1">Charon</a>
            </div>

            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav navbar-right hidden-xs">
                    <li><a class="btn btn-link" ng-click="logout()" tabindex="-1">Logout</a></li>
                </ul>
                <div class="navbar-form navbar-right hidden-xs" style="margin-right:100px;" ng-if="has_changed">
                    <span class="btn btn-success" ng-click="save_object()">Save Pending Changes</span>
                </div>
                <div class="navbar-form navbar-left" style="position:relative;">
                    <input id="search" type="search" class="form-control" placeholder="Search" ng-model="query" ng-submit="console.log('submitted')" autofocus style="padding-right:2em;">
                    <span class="search-clear" ng-click="query = ''" ng-show="query.length !== 0"><i class="fa fa-times-circle"></i></span>
                </div>
            </div>

        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">

            <div id="sidebar" class="col-xs-12 col-md-2 sidebar">
                <div class="hidden-sm hidden-md hidden-lg" style="height: 120px;"></div>

                <ul class="nav nav-sidebar">
                    <li ng-class="{active: !object.id}">
                        <a href="#/" tabindex="-1"><span class="fa fa-plus"></span> Add New Group</a>
                    </li>
                </ul>

                <hr />

                <ul class="nav nav-sidebar">
                    <li ng-repeat="(key, index_item) in index" ng-class="{active: index_item.id == object.id}" ng-if="query.length === 0 || search(key)"">
                        <a ng-href="{{'#/' + index_item.id}}" tabindex="-1""><span class="fa fa-book"></span> {{index_item.name}}</a>
                    </li>
                </ul>

                <div class="text-center hidden-sm hidden-md hidden-lg">
                    <div class="btn btn-link" data-toggle="collapse" data-target="#navbar, #sidebar">Close</div>
                </div>
                <hr class="hidden-sm hidden-md hidden-lg" />
            </div>

            <div class="col-xs-12 col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

                <div class="text-center" ng-show="loader">
                    <img src="/css/loader.svg" width="100%">
                </div>

                <div ng-show="!loader">

                    <div class="alert alert-success" ng-if="success.length">
                        <button type="button" class="close" ng-click="clear_messages()">&times;</button>
                        {{success}}
                    </div>
                    <div class="alert alert-danger" ng-if="error.length">
                        <button type="button" class="close" ng-click="clear_messages()">&times;</button>
                        {{error}}
                    </div>

                    <h1 class="page-header">
                        <input type="text" class="form-control input-lg" ng-model="object.name" placeholder="Add New Group" autofocus>
                    </h1>

                    <br />

                    <div class="row clearfix hidden-xs">
                        <div class="col-sm-3">
                            <div class="text-muted">Title</div>
                        </div>

                        <div class="col-sm-3">
                            <div class="text-muted">URL</div>
                        </div>

                        <div class="col-sm-2">
                            <div class="text-muted">User</div>
                        </div>

                        <div class="col-sm-3">
                            <div class="text-muted">Password</div>
                        </div>
                    </div>
                    <br />

                    <div sv-root sv-part="object.items">

                        <div class="row clearfix slide-50" ng-repeat="(key, item) in object.items" sv-element>

                            <div class="col-sm-3 col-xs-5">
                                <div class="input-group">
                                    <div class="input-group-addon hidden-xs" sv-handle>
                                        <span class="fa fa-ellipsis-v"></span>
                                    </div>

                                    <div class="input-group-addon pointer dropdown">
                                        <i class="fa text-primary dropdown-toggle" ng-class="item.icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="width:15px;"></i>
                                        <div class="dropdown-menu icon-menu">
                                            <div class="fa icon" ng-repeat="icon in icons" ng-click="item.icon = icon" ng-class="icon + (item.icon != icon ? ' text-primary' : ' text-success')"></div>
                                        </div>
                                    </div>

                                    <input type="text" class="form-control" ng-class="{'alert-info':field_match(item.title)}" ng-model="item.title" placeholder="Title" autocomplete="off">
                                </div>
                            </div>

                            <div class="col-sm-3 col-xs-7">
                                <div class="input-group">
                                    <input type="text" class="form-control" ng-class="{'alert-info':field_match(item.url)}" ng-model="item.url" ng-focus="highlight($event)" placeholder="URL" autocomplete="off">

                                    <div class="input-group-addon pointer" ng-if="item.url.length">
                                        <a class="fa fa-link btn-link" ng-href="{{(item.url.search('//') !== -1 ? item.url : 'http://' + item.url)}}" target="_new" tabindex="-1"></a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-2 col-xs-5">
                                <input type="text" class="form-control" ng-class="{'alert-info':field_match(item.user)}" ng-model="item.user" ng-focus="highlight($event)" placeholder="User" autocomplete="off">
                            </div>

                            <div class="col-sm-3 col-xs-7">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Password" autocomplete="off"
                                           ng-model="item.pass"
                                           ng-focus="highlight($event); pw_style={};"
                                           ng-blur="pw_style={'text-security':'disc'}"
                                           ng-init="pw_style={'text-security':'disc'}"
                                           ng-style="pw_style">

                                    <div class="input-group-addon pointer" data-toggle="popover" data-content="Generates a new 16-character password">
                                        <span class="fa fa-refresh text-warning" ng-click="generate_password(key)" tabindex="-1"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-1 col-xs-12">
                                <div class="input-group-addon pointer" ng-click="remove_item(key)" data-toggle="popover" data-content="Deletes the corresponding item entry">
                                    <span class="fa fa-trash text-danger pull-right" tabindex="-1"></span>
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="row">
                        <div class="col-xs-12">
                            <br />
                            <div class="btn btn-link text-success" ng-click="add_item()" data-toggle="popover" data-content="Adds a new key entry"><span class="fa fa-plus"></span> Add</div>
                        </div>
                    </div>

                    <hr />

                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default" ng-class="{'panel-info':field_match(object.note)}">
                                <div class="panel-heading">Note</div>
                                <div class="panel-body">
                                    <textarea class="form-control" rows="10" ng-model="object.note" placeholder="Type text here..." tabindex="-1"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr />

                    <div class="row" ng-if="object.name.length">
                        <div class="col-md-3 col-md-offset-9 col-sm-12 text-right">
                            <span data-toggle="popover" data-content="Deletes the Group permanently. You will be prompted for confirmation.">
                                <button class="btn btn-danger btn-lg" data-toggle="modal" data-target="#confirm-delete" tabindex="-1">Delete</button>
                            </span>
                            <span data-toggle="popover" data-content="Saves the Group">
                                <button class="btn btn-success btn-lg" ng-click="save_object()">Save</button>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Delete Modal -->
                <div class="modal" id="confirm-delete">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Are you sure?</h4>
                        </div>
                        <div class="modal-body">
                            <h5>Delete {{object.name}} Group?</h5>
                            <div>This action cannot be undone.</div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger" ng-click="delete_object()" data-dismiss="modal">Delete</button>
                        </div>
                        </div>
                    </div>
                </div>
                <!-- Delete Modal -->

            </div>
        </div>

        <div class="row">
            <div class="col-md-12 text-right">
                <a class="btn btn-link" ng-click="logout()" tabindex="-1">Logout</a>
                <small>&copy <?=date('Y')?> Charon v<?=CHARON_VERSION?></small>
            </div>
        </div>
    </div>

</div>
</body>
</html>
