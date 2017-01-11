<?php
namespace models;

use core;
use \Exception;

/**
 *
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 1/5/17
 * @package charon
 */
class Locker extends Base {

    const TABLE = 'locker';

    public $id;
    public $name;
    public $items;
    public $note;

    /**
     * Locker constructor.
     * @param array $params
     */
    public function __construct($params) {
        parent::__construct($params);
        $this->items = is_scalar($this->items) ? core\Crypt::dec($this->items) : $this->items;
    }

    /**
     * @throws Exception
     * @return self
     */
    public function validate(): self {
        $errors = [];

        if (mb_strlen($this->id) > 1024)
            $errors[] = 'Invalid ID';

        if (empty($this->name))
            $errors[] = 'Invalid Name';

        if (mb_strlen($this->note) > core\SQLite::STRING_LENGTH_MAX)
            $errors[] = 'Note string is too long.';

        if (mb_strlen(core\Crypt::enc($this->items)) > core\SQLite::STRING_LENGTH_MAX)
            $errors[] = 'Items list is too long. Please shorten or create a new Locker.';

        if (count($errors))
            throw new Exception(implode("\n", $errors));

        return $this;
    }

    /**
     * Overrides parent
     */
    public function save(): self {
        $items = $this->items;
        $this->items = core\Crypt::enc($this->items);
        parent::save();
        $this->items = $items;
        return $this;
    }
}