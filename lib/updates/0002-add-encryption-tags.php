<?php
/**
 * Migrates old User data structure to the new data structure
 * @author Jason Wright <jason@silvermast.io>
 * @since 1/9/17
 * @package charon
 */
use models\Locker;

foreach (Locker::findMulti([]) as $locker)
    $locker->save();
