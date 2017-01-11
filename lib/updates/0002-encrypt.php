<?php
/**
 * Encrypts the Locker files in sqlite
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 1/11/17
 * @package charon
 */

$results = core\SQLite::initWrite()->query("SELECT * FROM locker");
while ($lockerData = $results->fetchArray(SQLITE3_ASSOC)) {
    $lockerData['items'] = json_decode($lockerData['items']);
    models\Locker::new($lockerData)->save();
}