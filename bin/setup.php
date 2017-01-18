<?php
/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 1/4/17
 * @package charon
 */
require_once(__DIR__ . '/../core.php');

$db = core\SQLite::initWrite();

// database instantiation
$queries = [

    'CREATE TABLE IF NOT EXISTS users (
        id TEXT PRIMARY KEY NOT NULL,
        name TEXT NOT NULL,
        passhash TEXT NOT NULL
    )',

    'CREATE TABLE IF NOT EXISTS locker (
        id TEXT NOT NULL,
        name TEXT NOT NULL,
        items TEXT,
        note TEXT
    )',

    'CREATE UNIQUE INDEX IF NOT EXISTS user_id ON users (id)',
    'CREATE UNIQUE INDEX IF NOT EXISTS user_name ON users (name)',
    'CREATE UNIQUE INDEX IF NOT EXISTS locker_id ON locker (id)',

];

foreach ($queries as $sql)
    if (!$db->exec($sql)) throw new Exception($db->lastErrorMsg());

echo "Creating admin user\n";
echo "Please enter the admin password\n";

// set Admin user
// use hidden password functionality in read library
$pass1 = trim(`/bin/bash -c "read -s -p 'Pass: ' password && echo \\\$password"`);
echo "\n";

// use hidden password functionality in read library
$pass2 = trim(`/bin/bash -c "read -s -p 'Re-enter Pass: ' password && echo \\\$password"`);
echo "\n";

// check passwords
if ($pass1 != $pass2) throw new Exception("Passwords do not match");

$user = models\User::findOne(['name' => 'admin']) ?? models\User::new(['name' => 'admin']);
$user->setPasswordHash($pass1)->save();

echo "Done!\n";
die(0);