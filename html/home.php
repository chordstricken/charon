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
                <div class="navbar-form navbar-left">
                    <input id="search" type="search" class="form-control" placeholder="Search" ng-model="query">
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
                        <a href="#/" tabindex="-1"><span class="glyphicon glyphicon-plus"></span> Add New Group</a>
                    </li>
                </ul>

                <hr />

                <ul class="nav nav-sidebar">
                    <li ng-repeat="(id, name) in index" ng-class="{active: id == object.id}" ng-if="query.length === 0 || name.indexOf(query) > -1">
                        <a ng-href="{{'#/' + id}}" tabindex="-1"><span class="glyphicon glyphicon-book"></span> {{name}}</a>
                    </li>
                </ul>

                <div class="text-center hidden-sm hidden-md hidden-lg" data-toggle="collapse" data-target="#navbar, #sidebar">Close</div>
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
                                        <span class="glyphicon glyphicon-option-vertical"></span>
                                    </div>
                                    <input type="text" class="form-control" ng-model="item.title" placeholder="Title">
                                </div>
                            </div>

                            <div class="col-sm-3 col-xs-7">
                                <div class="input-group">
                                    <input type="text" class="form-control" ng-model="item.url" ng-focus="highlight($event)" placeholder="URL">

                                    <div class="input-group-addon pointer" ng-if="item.url.length">
                                        <a class="glyphicon glyphicon-link btn-link" ng-href="{{(item.url.search('//') !== -1 ? item.url : 'http://' + item.url)}}" target="_new" tabindex="-1"></a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-2 col-xs-5">
                                <input type="text" class="form-control" ng-model="item.user" ng-focus="highlight($event)" placeholder="User">
                            </div>

                            <div class="col-sm-3 col-xs-7">
                                <div class="input-group">
                                    <input type="password" class="form-control" placeholder="Password" ng-model="item.pass" ng-focus="highlight($event)" ng-blur="set_type($event, 'password')">

                                    <div class="input-group-addon pointer" data-toggle="popover" data-content="Generates a new 16-character password">
                                        <span class="glyphicon glyphicon-refresh text-warning" ng-click="generate_password(key)" tabindex="-1"></span>
                                    </div>
                                </div>
                            </div>

                            <br class="hidden-sm hidden-md hidden-lg" />

                            <div class="col-sm-1 hidden-xs">
                                <div class="input-group-addon pointer" ng-click="remove_item(key)" data-toggle="popover" data-content="Deletes the corresponding item entry">
                                    <span class="glyphicon glyphicon-trash text-danger" tabindex="-1"></span>
                                </div>
                            </div>

                            <hr class="hidden-sm hidden-md hidden-lg" />

                        </div>

                    </div>

                    <div class="row">
                        <div class="col-xs-12">
                            <br />
                            <div class="btn btn-link text-success" ng-click="add_item()" data-toggle="popover" data-content="Adds a new key entry"><span class="glyphicon glyphicon-plus"></span> Add</div>
                        </div>
                    </div>

                    <hr />

                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">Note</div>
                                <div class="panel-body">
                                    <textarea class="form-control" rows="5" ng-model="object.note" placeholder="Type text here..." tabindex="-1"></textarea>
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
                                <button class="btn btn-info btn-lg" ng-click="save_object()">Save</button>
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
