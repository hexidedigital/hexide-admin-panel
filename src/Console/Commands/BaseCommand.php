<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Console\Commands;

use Illuminate\Console\Command;

abstract class BaseCommand extends Command
{
    /**
     * Log console message
     *
     * @param string $string
     * @param string $status
     * @param null|int|string $verbosity
     */
    public function log($string, string $status = 'info', $verbosity = null)
    {
        $string = '[' . now() . '] ' . $string;
        $this->line($string, $status, $verbosity);
    }

    /**
     * @param string $string
     * @param null|int|string $verbosity
     */
    public function info($string, $verbosity = null)
    {
        $this->log($string, 'info', $verbosity);
    }

    /**
     * @param string $string
     * @param null|int|string $verbosity
     */
    public function error($string, $verbosity = null)
    {
        $this->log($string, 'error', $verbosity);
    }

    /**
     * @param string $string
     * @param null|int|string $verbosity
     */
    public function comment($string, $verbosity = null)
    {
        $this->log($string, 'comment', $verbosity);
    }

    /**
     * @param string $string
     * @param null|int|string $verbosity
     */
    public function warn($string, $verbosity = null)
    {
        $string = '[' . now() . '] ' . $string;
        parent::warn($string, $verbosity);
    }

    /**
     * @param string $string
     * @param null|int|string $verbosity
     */
    public function question($string, $verbosity = null)
    {
        $this->log($string, 'question', $verbosity);
    }

    /** Start command log */
    public function start()
    {
        $this->log('Start: ' . $this->getDescription());
    }

    /** Finish command log */
    public function end()
    {
        $this->log('Finish: ' . $this->getDescription() . "\n\n");
    }
}
