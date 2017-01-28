<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/img/favicon.png" />

    <title><?=APP_NAME?> - Password Locker</title>

    <link href="/css/build.css" rel="stylesheet">
</head>

<body>
    <!-- Fixes the strange chrome/firefox autocomplete spaz bug -->
    <input type="text" name="user" value="" style="display:none;" />
    <input type="password" name="password" value="" style="display:none;" />

    <div id="page-container">

        <div id="locker-app">

            <nav-bar>
                <span class="btn btn-success" v-if="hasChanged" @click="saveObject">Save Pending Changes</span>
            </nav-bar>

            <div class="container-fluid">
                <div class="row">

                    <div id="sidebar" class="col-xs-12 col-md-2 sidebar">
                        <div class="hidden-sm hidden-md hidden-lg" style="height: 120px;"></div>

                        <ul class="nav nav-sidebar">
                            <li>
                                <input id="search" type="search" class="form-control sidebar-search" placeholder="Search" v-model="query" autofocus style="padding-right:2em;">
                                <span class="search-clear" @click="query = ''" v-show="query.length !== 0"><i class="fa fa-times-circle"></i></span>
                            </li>
                            <li :class="{active: !object.id}">
                                <a href="#/" tabindex="-1"><span class="fa fa-plus"></span> Add New Locker</a>
                            </li>
                            <hr />
                            <li v-for="(indexItem, key) in index" :class="{active: indexItem.id == object.id}" v-if="query.length === 0 || search(key)">
                                <a :href="'#/' + indexItem.id" tabindex="-1"><i class="fa fa-book"></i> <span v-text="indexItem.name"></span></a>
                            </li>
                        </ul>

                        <div class="text-center hidden-sm hidden-md hidden-lg">
                            <div class="btn btn-link" data-toggle="collapse" data-target="#navbar, #sidebar">Close</div>
                        </div>
                        <hr class="hidden-sm hidden-md hidden-lg" />
                    </div>

                    <div class="col-xs-12 col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

                        <div class="text-center" v-show="loader">
                            <img src="/img/loader.svg" width="100%">
                        </div>

                        <div v-show="!loader">

                            <div class="alert alert-success" v-if="success.length">
                                <button type="button" class="close" @click="clearMessages">&times;</button>
                                <span v-text="success"></span>
                            </div>
                            <div class="alert alert-danger" v-if="error.length">
                                <button type="button" class="close" @click="clearMessages">&times;</button>
                                <span v-text="error"></span>
                            </div>

                            <h1 class="page-header">
                                <input type="text" class="form-control input-lg" v-model="object.name" placeholder="Add New Locker" autofocus>
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

                            <draggable :list="object.items" :options="{handle:'.sort-handle'}">

                                <div class="row clearfix slide-50" v-if="object.items.length" v-for="(item, key) in object.items">

                                    <div class="col-sm-3 col-xs-5">
                                        <div class="input-group">
                                            <div class="input-group-addon hidden-xs sort-handle">
                                                <span class="fa fa-ellipsis-v"></span>
                                            </div>

                                            <div class="input-group-addon pointer dropdown">
                                                <i class="fa text-primary dropdown-toggle" :class="item.icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="width:15px;"></i>
                                                <div class="dropdown-menu icon-menu">
                                                    <div class="fa icon" v-for="icon in icons" @click="item.icon = icon" :class="icon + (item.icon != icon ? ' text-primary' : ' text-success')"></div>
                                                </div>
                                            </div>

                                            <input type="text" class="form-control" :class="{'alert-info':fieldMatch(item.title)}" v-model="item.title" placeholder="Title" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="col-sm-3 col-xs-7">
                                        <div class="input-group">
                                            <input type="text" class="form-control" :class="{'alert-info':fieldMatch(item.url)}" v-model="item.url" :focus="highlight" placeholder="URL" autocomplete="off">

                                            <div class="input-group-addon pointer" ng-if="item.url.length">
                                                <a class="fa fa-link btn-link" :href="(item.url.indexOf('//') !== -1 ? item.url : 'http://' + item.url)" target="_new" tabindex="-1"></a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-2 col-xs-5">
                                        <input type="text" class="form-control" :class="{'alert-info':fieldMatch(item.user)}" v-model="item.user" @focus="highlight" placeholder="User" autocomplete="off">
                                    </div>

                                    <div class="col-sm-3 col-xs-7">
                                        <div class="input-group">
                                            <input type="password" class="form-control password-mask" placeholder="Password" autocomplete="off" v-model="item.pass" @focus="highlight">

                                            <div class="input-group-addon pointer" data-toggle="popover" data-content="Generates a new 16-character password">
                                                <span class="fa fa-refresh text-warning" @click="generatePassword(key)" tabindex="-1"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-1 col-xs-12">
                                        <div class="input-group-addon pointer" @click="removeItem(key)" data-toggle="popover" data-content="Deletes the corresponding item entry">
                                            <span class="fa fa-trash text-danger pull-right" tabindex="-1"></span>
                                        </div>
                                    </div>

                                </div>

                            </draggable>

                            <div class="row">
                                <div class="col-xs-12">
                                    <br />
                                    <div class="btn btn-link text-success" @click="addItem" data-toggle="popover" data-content="Adds a new key entry"><span class="fa fa-plus"></span> Add</div>
                                </div>
                            </div>

                            <hr />

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel panel-default" :class="{'panel-info':fieldMatch(object.note)}">
                                        <div class="panel-heading">Note</div>
                                        <div class="panel-body">
                                            <textarea class="form-control" rows="10" v-model="object.note" placeholder="Type text here..." tabindex="-1"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr />

                            <div class="row" v-if="object.name.length">
                                <div class="col-md-3 col-md-offset-9 col-sm-12 text-right">
                                    <span data-toggle="popover" data-content="Deletes the Group permanently. You will be prompted for confirmation.">
                                        <button class="btn btn-danger btn-lg" data-toggle="modal" data-target="#confirm-delete" tabindex="-1">Delete</button>
                                    </span>
                                    <span data-toggle="popover" data-content="Saves the Group">
                                        <button class="btn btn-success btn-lg" @click="saveObject">Save</button>
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
                                    <h5>Delete <span v-text="object.name"></span> Group?</h5>
                                    <div>This action cannot be undone.</div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger" @click="deleteObject" data-dismiss="modal">Delete</button>
                                </div>
                                </div>
                            </div>
                        </div>
                        <!-- Delete Modal -->

                    </div>
                </div>

            </div>
        </div>
        <noscript><h1>Javascript is required to use this application.</noscript>

    </div>
</body>

<script type="text/x-template" id="tmpl-nav-bar"><?php include(ROOT . '/html/templates/nav-bar.html'); ?></script>
<script src="/js/build.js"></script>
<script src="/js/locker.js"></script>

</html>
