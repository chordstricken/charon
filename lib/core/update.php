<?php
namespace core;

use \Exception;

/**
 * Rudimentary app version controller
 * @author Jason Wright <jason@silvermast.io>
 * @since 1/9/17
 * @package charon
 */
class Update {
    use Singleton;

    /** @var string */
    private $history_file;
    /** @var object */
    private $history;
    /** @var string[] */
    private $scripts = [];

    /**
     * Update constructor.
     */
    public function __construct() {
        $this->history_file = ROOT . '/data/update_history.json';
        $this->history = (object)[];

        if (!is_writable(ROOT . '/data'))       throw new Exception("Data directory is not writable.");

        // load update history
        if (file_exists($this->history_file)) {
            if (!is_readable($this->history_file))  throw new Exception("$this->history_file is not readable");
            if (!is_writable($this->history_file))  throw new Exception("$this->history_file is not writable");
            $this->history = json_decode(file_get_contents($this->history_file));
        }

        // load
        foreach (scandir(ROOT . '/lib/updates') as $script)
            if ($script[0] !== '.') $this->scripts[] = $script;

        sort($this->scripts); // ensure the scripts are in ascending order.
    }

    /**
     * Executes the database upgrade
     */
    public function run() {
        try {

            $scripts_run = 0;

            foreach ($this->scripts as $script) {
                if (isset($this->history->$script))
                    continue;

                echo "Running $script\n";
                include(ROOT . "/lib/updates/$script");
                $scripts_run++;
                $this->history->$script = date(DATE_ATOM); // log the time of completion
            }

            echo $scripts_run ? "Ran $scripts_run scripts.\n" : "No updates were found.\n";

        } catch (\Exception $e) {
            echo $e->getMessage() . "\n";
            echo "Failed completing the update.\n";
        }

        // Ensure that all/any completed scripts are logged
        $this->writeHistory();
    }

    /**
     * Writes the history data
     */
    private function writeHistory() {
        file_put_contents($this->history_file, json_encode($this->history, JSON_PRETTY_PRINT));
    }

}