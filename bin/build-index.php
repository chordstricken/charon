#!/usr/bin/php
<?php
/**
 * Utility script for building and re-writing the _index file
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 9/30/15
 * @package charon
 */

require_once(__DIR__.'/../core.php');

// first, take a backup of the current index
echo "Backing up index as _old_index\n";
$old_index = File::read(Index::ID);
File::write('_old_index', $old_index);
unlink(ROOT . '/data/' . Index::ID); // manually delete because it's not allowed through the File class

try {

    // pull out all files first
    echo "Reading data directory\n";
    if (!$data_files = scandir(ROOT . '/data'))
        throw new Exception('Unable to read data directory');

    // iterate over files, skipping private files
    echo "Scanning data files and rebuilding the index\n";
    foreach ($data_files as $file) {

        if ($file[0] === '.' || $file[0] === '_')
            continue;

        // Read the data object
        $data = Data::read($file);
        $meta = array();

        // iterate over the items and build an array of meta values for searching.
        // We'll use title, url, and user
        foreach ($data->items as $item) {

            if (isset($item->title) && trim($item->title))
                $meta[$item->title] = 1;

            if (isset($item->url) && trim($item->url))
                $meta[$item->url] = 1;

            if (isset($item->user) && trim($item->user))
                $meta[$item->user] = 1;

        }

        if (isset($data->note) && trim($data->note))
            $meta[$data->note] = 1;

        // add the item to the new index
        Index::add($data->id, $data->name, array_keys($meta));

    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Restoring old index\n";
    File::write($old_index, Index::ID);
}

// finally, delete the index backup
unlink(ROOT . "/data/_old_index");

echo "Success!\n";
