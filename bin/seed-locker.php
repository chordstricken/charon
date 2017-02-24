<?php
require_once(__DIR__ . '/../core.php');
require_once(__DIR__ . '/functions.php');

$account = models\Account::findOne(['slug' => mb_strtolower(trim(readline('Account Slug: ')))]);
if (!$account)
    finish('Account does not exist.');

function randomString($len) {
    $chars   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ?><.,][}{!@#$%^&*()-=_+|`~';
    $charLen = strlen($chars) - 1;
    $result  = '';
    $len     = $len ?? 16;
    for ($i = 0; $i < $len; $i++)
        $result .= $chars[mt_rand(0, $charLen)];

    return $result;
}

$semStr = new core\SemanticString();

echo "Seeding\n";
// generate 100 Locker objects
for ($i = 0; $i < 100; $i++) {

    $locker        = new models\Locker();
    $locker->name  = $semStr->getSemanticString();
    $locker->note  = "--SEED--\n" . $semStr->getSemanticString(50);
    $locker->items = [];

    for ($j = 0; $j < mt_rand(2, 50); $j++)
        $locker->items[] = [
            'accountId' => $account->id,
            'title'     => $semStr->getRandomWord(),
            'url'       => "https://" . $semStr->getRandomWord() . "." . $semStr->getRandomWord(),
            'user'      => $semStr->getRandomWord(),
            'pass'      => randomString(mt_rand(8, 64)),
        ];

    $locker->save();
    echo '.';
}


echo "\nDone!\n";
echo "Memory: " . (memory_get_usage() / 1024 / 1024) . "MB\n";
die(0);