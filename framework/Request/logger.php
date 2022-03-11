<?php
declare(strict_types=1);

namespace Delos\Request;

final class Logger
{
    public static $NONE = 0;
    public static $DEBUG = 1;
    public static $INFO = 2;
    public static $WARNING = 3;
    public static $ERROR = 4;

    private $log_level;

    private function __construct($log_level)
    {
        $this->log_level = $log_level;
    }

    private static $instance;
    public static function getInstance($log_level = null)
    {
        if ($log_level === null) {
            $log_level = self::$DEBUG;
        }

        if (self::$instance === null) {
            self::$instance = new self($log_level);
        }

        return self::$instance;
    }

    public function debug($message) {
        if ($this->log_level >= self::$DEBUG) {
            $this->log('DEBUG: ' . $message);
        }
    }

    public function info($message) {
        if ($this->log_level >= self::$INFO) {
            $this->log('INFO: ' . $message);
        }
    }

    public function log($message) {
        if (isset($_SERVER)) {
            $line_break = "<br>";
        } else {
            $line_break = "\n";
        }

        echo $message . $line_break;

    }
}