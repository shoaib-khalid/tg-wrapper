<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;

class CSVFormatter
{
    /**
     * Customize the given logger instance.
     *
     * @param  \Illuminate\Log\Logger  $logger
     * @return void
     */
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter(new LineFormatter(
                '%datetime%|%level_name%|'.session()->getId().'|'.config('app.name').'|%message%|%context%'."\n"
            ));
        }
    }
}