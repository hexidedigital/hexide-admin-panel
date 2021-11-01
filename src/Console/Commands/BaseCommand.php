<?php

namespace HexideDigital\HexideAdmin\Console\Commands;

use Illuminate\Console\Command;

abstract class BaseCommand extends Command
{

    /**
     * Log console message
     *
     * @param string $message
     * @param string $status
     * @param null|int|string $verbosity
     */
    public function log($message, $status = 'info', $verbosity = null)
    {
        $message = '[' . now() . '] ' . $message;
        $this->line($message, $status, $verbosity);
    }

    /**
     * @param string $message
     * @param null|int|string $verbosity
     */
    public function info($message, $verbosity = null)
    {
        $this->log($message, 'info', $verbosity);
    }

    /**
     * @param string $message
     * @param null|int|string $verbosity
     */
    public function error($message, $verbosity = null)
    {
        $this->log($message, 'error', $verbosity);
    }

    /**
     * @param string $message
     * @param null|int|string $verbosity
     */
    public function comment($message, $verbosity = null)
    {
        $this->log($message, 'comment', $verbosity);
    }

    /**
     * @param string $message
     * @param null|int|string $verbosity
     */
    public function warn($message, $verbosity = null)
    {
        $message = '[' . now() . '] ' . $message;
        parent::warn($message, $verbosity);
    }

    /**
     * @param string $message
     * @param null|int|string $verbosity
     */
    public function question($message, $verbosity = null)
    {
        $this->log($message, 'question', $verbosity);
    }

    /**
     * Start command log
     */
    public function start()
    {
        $this->log('Start: ' . $this->getDescription());
    }

    /**
     * Finish command log
     */
    public function end()
    {
        $this->log('Finish: ' . $this->getDescription() . "\n\n");
    }
}
