#!/usr/bin/php
<?php
/**
 * Changes the crypt password and updates config.php
 * @author Jason Wright <jason@invexi.com>
 * @since 5/6/2015
 * @package charon
 */

// check the config file, and load it
if (!file_exists(__DIR__.'/../config.php')) {
    echo "Could not find config.php\n";
    die(0);
}
require_once(__DIR__.'/../config.php');

// prompt the user for a new keyphrase
$new_key = trim(readline('Enter the new key: '));

$data_dir = ROOT.'/data';

// start re-encrypting
if ($files = scandir(ROOT.'/data')) {

    // make a backup of the data directory in case it screws up
    echo "Backing up data directory\n";
    $backup_file = __DIR__.'/data-backup.tgz';
    echo `tar -czf $backup_file $data_dir`;

    try {

        echo "Re-encrypting data files\n";
        foreach ($files as $file) {

            if ($file[0] == '.')
                continue;

            if (!$old_data = file_get_contents("$data_dir/$file"))
                throw new Exception("Failed to read file: $file");

            if (!$old_data = gzuncompress($old_data))
                throw new Exception("Failed to uncompress fild: $file");

            if (!$old_data = Crypt::dec($old_data))
                throw new Exception("Failed to decrypt file: $file");

            if (!$new_data = Crypt::enc($old_data, $new_key))
                throw new Exception("Failed to encrypt new file: $file");

            if (!$new_data = gzcompress($new_data))
                throw new Exception("Failed to compress new file: $file");

            if (!file_put_contents("$data_dir/$file", $new_data))
                throw new Exception("Failed to write new file: $file");

            echo '.';
        }
        echo "\n";

    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
        echo "Restoring data directory backup\n";
        echo `tar -xzf $backup_file`;
        echo `rm -f $backup_file`;
        die(0);
    }
}

echo "Writing new config file\n";
// escape the crypt key so it can be inserted into the new config file
$crypt_key_escaped = str_replace("'", "\\'", $new_key);
// load the old config file as a string
$old_config = file_get_contents(__DIR__.'/../config.php');
// replace the define() call with the new key
$new_config = preg_replace("/^define\('CRYPT_KEY'.*$/m", "define('CRYPT_KEY', '$crypt_key_escaped');", $old_config);
// save the file
if (!file_put_contents(ROOT.'/config.php', $new_config)) {
    echo "Failed to write config file. Restoring old configuration file\n";
    file_put_contents(ROOT.'/config.php', $old_config);
    if (isset($backup_file)) {
        echo "Restoring old data\n";
        echo `tar -xzf $backup_file`;
        echo `rm -f $backup_file`;
    }
    die();
}

// remove backup
if (isset($backup_file))
    echo `rm -f $backup_file`;

echo "Success!\n";