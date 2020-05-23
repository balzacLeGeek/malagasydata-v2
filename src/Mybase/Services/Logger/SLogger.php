<?php

/**
 * Service that can be used to write new log or push message in a specified logfile (/var/log/filename.log)
 */
namespace App\Mybase\Services\Logger;

use App\Mybase\Services\Logger\LoggerInterface;

use Monolog\Logger as MonologLoger;
use Monolog\Handler\StreamHandler;

class SLogger implements LoggerInterface 
{
    /**
     * @var string
     */
    protected $rootDir;

    /**
     * SLogger constructor
     *
     * @param string $rootDir
     */
    public function __construct(string $rootDir = null)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * Write new log or push message to logfile
     *
     * @param string $content
     * @param string $logFile
     * @param string $messageLevel
     * @param boolean $pushMessage
     * @return Logger
     */
    public function writeLog(string $content, string $logFile, string $messageLevel = MonologLoger::NOTICE, bool $pushMessage = false): SLogger
    {
        $logFilePath = $this->rootDir . '/var/log/' . $logFile . '.log';
        $logger = new MonologLoger($logFile);

        $logger->pushHandler(new StreamHandler($logFilePath, $messageLevel));

        // Add header if itsn't a message push
        if (!$pushMessage) {
            $logger->notice('------------------------[' . date("Y-m-d H:i:s") . ']------------------------');
        }

        $logger->notice($content);
        $logger->notice('---------------------------------------------------------------------');

        return $this;
    }
}