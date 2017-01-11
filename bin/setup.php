<?php
/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 1/4/17
 * @package charon
 */
require_once(__DIR__ . '/../core.php');

$db = core\SQLite::initWrite();

$queries = [

    'CREATE TABLE users (
        id TEXT PRIMARY KEY NOT NULL,
        name TEXT NOT NULL,
        passhash TEXT NOT NULL
    )',

    'CREATE TABLE locker (
        id TEXT NOT NULL,
        name TEXT NOT NULL,
        items TEXT,
        note TEXT
    )',

    'CREATE UNIQUE INDEX user_id ON users (id)',
    'CREATE UNIQUE INDEX user_name ON users (name)',
    'CREATE UNIQUE INDEX locker_id ON locker (id)',

];

foreach ($queries as $sql)
    if (!$db->exec($sql)) throw new Exception($db->lastErrorMsg());

$db->close();