<?php

namespace App\Mybase\Services\Logger;

interface LoggerInterface 
{
    public function writeLog(string $content, string $logFile, string $messageLevel, bool $pushMessage);
}
