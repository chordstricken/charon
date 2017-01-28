<?php
namespace core;

use \SQLite3;

/**
 *
 * @author Jason Wright <jason@silvermast.io>
 * @since 1/4/17
 * @package charon
 */
class SQLite extends SQLite3 {

    const FILE = 'main.db';

    /** Byte length max is 1,000,000,000 */
    const STRING_LENGTH_MAX = 500000000;

    private static $_dbRead;
    private static $_dbWrite;

    /**
     * SQLite constructor.
     * @param null $filename
     * @param null $flags
     * @param null $encryption_key
     */
    public function __construct($filename = null, $flags = null, $encryption_key = null) {
        parent::__construct(ROOT . '/data/' . basename($filename ?? self::FILE), $flags, CRYPT_KEY);
        $this->enableExceptions(true);
    }

    /**
     * On teardown
     */
    public function __destruct() {
        $this->close();
    }

    public static function initRead($filename = null) {
        return self::$_dbRead ?? self::$_dbRead = new self($filename, SQLITE3_OPEN_READONLY);
    }

    public static function initWrite($filename = null) {
        return self::$_dbWrite ?? self::$_dbWrite = new self($filename, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
    }

    public function prepare($query) {
        Debug::info(__METHOD__ . ": $query");
        return parent::prepare($query);
    }

    public function query($query) {
        Debug::info(__METHOD__ . ": $query");
        return parent::query($query);
    }

    public function querySingle($query, $entire_row = false) {
        Debug::info(__METHOD__ . ": $query");
        return parent::querySingle($query, $entire_row);
    }

    public function exec($query) {
        Debug::info(__METHOD__ . ": $query");
        return parent::exec($query);
    }

    /**
     * Builds an AND SQL query statement using the provided array
     * @param array $params - associative array of parameters
     * @return string $sql
     */
    public function prepare_and_statement(array $params) {
        if (!is_array($params) || !count($params))
            return 1;

        $sql = [];
        foreach ($params as $key => $value) {
            if ($value === null) {
                $sql[] = "$key IS NULL";

            } elseif (is_array($value)) {

                if (isset($value['LIKE'])) {
                    $value['LIKE'] = $this->escapeString($value['LIKE']);
                    $sql[]         = "$key LIKE '$value[LIKE]'";

                } else if (isset($value['NOT'])) {
                    $value['NOT'] = $this->escapeString($value['NOT']);
                    $sql[]        = "$key != '$value[NOT]'";

                } else if (isset($value['BETWEEN']) && is_array($value['BETWEEN'])) {
                    $values_escaped = array_map([$this, 'escapeString'], $value['BETWEEN']);
                    $sql[]          = "$key BETWEEN '$values_escaped[0]' AND '$values_escaped[1]'";

                } else {
                    $values_escaped = array_map([$this, 'escapeString'], $value);
                    $sql[]          = "$key IN('" . implode("','", $values_escaped) . "')";

                }

            } else {
                $value_escaped = $this->escapeString($value);
                $sql[]         = "$key = '$value_escaped'";

            }
        }

        return implode(' AND ', $sql);
    }

}