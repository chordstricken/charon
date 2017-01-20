<?php
/**
 * Migrates old User data structure to the new data structure
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 1/9/17
 * @package charon
 */

function read_encrypted_datafile($filename) {
    $path = ROOT."/data/$filename";

    if (!file_exists($path))
        throw new Exception(__METHOD__ . " $filename does not exist", 404);

    if (!$raw_data = file_get_contents($path))
        throw new Exception(__METHOD__ . " Failed to read data for $filename", 400);

    // If the string is empty, just return it
    if (mb_strlen($raw_data) === 0)
        return $raw_data;

    // First uncompress
    if (!$output = gzuncompress($raw_data))
        throw new Exception(__METHOD__ . ' Failed to uncompress data', 500);

    // decrypt
    if (!$output = core\openssl\AES::decrypt($output))
        throw new Exception(__METHOD__ . ' Failed to decrypt data', 500);

    // Then decode json
    if (!$output = json_decode($output))
        throw new Exception(__METHOD__ . ' Failed to decode data', 500);

    return $output;
}

try {

    // migrate user data
    $users = read_encrypted_datafile('_users');
    foreach ($users as $username => $passhash)
        models\User::new(['name' => $username, 'passhash' => $passhash])->save();

    unset($users);
    unlink(ROOT . '/data/_users');

    // migrate locker data
    $index = read_encrypted_datafile('_index');
    foreach ($index as $itemhash => $item) {
        $oldItem = read_encrypted_datafile($itemhash);
        $newItem = models\Locker::new($oldItem);
        $newItem->id = null;
        $newItem->save();
        unlink(ROOT . "/data/$itemhash");
    }
    unlink(ROOT . '/data/_index');

} catch (Exception $e) {
    echo "\n" . $e->getMessage() . "\n";
    die();
}
