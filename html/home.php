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
                <a class="navbar-brand" href="#/">Charon</a>

                <div class="navbar-form navbar-right">
                    <input id="search" type="search" class="form-control" placeholder="Search" ng-model="query">
                </div>

            </div>

            <div class="navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li><a class="btn btn-link" ng-click="logout()">Logout</a></li>
                </ul>
            </div>

        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">

            <div class="col-sm-3 col-md-2 sidebar">
                <ul class="nav nav-sidebar">
                    <li ng-class="{active: !object.id}">
                        <a href="#/"><span class="glyphicon glyphicon-plus"></span> Add New Group</a>
                    </li>
                </ul>

                <hr />

                <ul class="nav nav-sidebar">
                    <li ng-repeat="(id, name) in index" ng-class="{active: id == object.id}" ng-if="query.length === 0 || name.indexOf(query) > -1">
                        <a ng-href="{{'#/' + id}}"><span class="glyphicon glyphicon-book"></span> {{name}}</a>
                    </li>
                </ul>
            </div>

            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

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

                    <div class="row clearfix">
                        <div class="col-md-3">
                            <div class="text-muted">Title</div>
                        </div>

                        <div class="col-md-3">
                            <div class="text-muted">URL</div>
                        </div>

                        <div class="col-md-2">
                            <div class="text-muted">User</div>
                        </div>

                        <div class="col-md-3">
                            <div class="text-muted">Password</div>
                        </div>
                    </div>
                    <br />

                    <div class="row clearfix slide-50" ng-repeat="(key, item) in object.items">
                        <div class="col-md-3">
                            <input type="text" class="form-control" ng-model="item.title" placeholder="Title">
                        </div>

                        <div class="col-md-3">
                            <input type="text" class="form-control" ng-model="item.url" ng-focus="highlight($event)" placeholder="URL">
                        </div>

                        <div class="col-md-2">
                            <input type="text" class="form-control" ng-model="item.user" ng-focus="highlight($event)" placeholder="User">
                        </div>

                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="password" class="form-control" placeholder="Password" ng-model="item.pass" ng-focus="highlight($event)" ng-blur="set_type($event, 'password')">

                                <div class="input-group-addon pointer">
                                    <span class="glyphicon glyphicon-refresh text-warning" ng-click="generate_password(key)"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-1">
                            <div class="input-group-addon pointer" ng-click="remove_item(key)">
                                <span class="glyphicon glyphicon-fire text-danger"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <br />
                            <div class="btn btn-link text-success" ng-click="add_item()"><span class="glyphicon glyphicon-plus"></span> Add</div>
                        </div>
                    </div>

                    <hr />

                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">Note</div>
                                <div class="panel-body">
                                    <textarea class="form-control" rows="3" ng-model="object.note" placeholder="Type text here..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr />

                    <div class="row" ng-if="object.name.length">
                        <div class="col-md-3 col-md-offset-9 col-sm-12 text-right">
                            <button class="btn btn-danger btn-lg" data-toggle="modal" data-target="#confirm-delete">Delete</button>
                            <button class="btn btn-info btn-lg" ng-click="save_object()">Save</button>
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
                <small>&copy <?=date('Y')?> Charon v<?=CHARON_VERSION?></small>
            </div>
        </div>
    </div>

</div>
</body>
</html>
