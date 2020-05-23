<?php

namespace App\Mybase\Services\Base;

interface SBaseInterface 
{
    public function getEntityManager();

    public function getRepository(string $entityClass);

    public function getParameter(string $name);

    public function getService(string $id);

    public function save($object);

    public function remove($object);

    public function writeLog(string $content, string $logFile, string $messageLevel, bool $pushMessage);
}