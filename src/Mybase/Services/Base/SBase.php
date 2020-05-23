<?php

/**
 * Base service
 */
namespace App\Mybase\Services\Base;

use App\Mybase\Services\Base\SBaseInterface;
use App\Mybase\Services\Logger\SLogger;

use Monolog\Logger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class SBase implements SBaseInterface {

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Base service constructor
     *
     * @param EntityManagerInterface $entityManager
     * @param ContainerInterface $container
     */
    public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container)
    {
        $this->entityManager    = $entityManager;
        $this->container        = $container;
    }

    /**
     * Gets the default Entity Manager
     *
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * Gets the related Repository for given class name
     *
     * @param string $entityClass The entity class name
     * 
     * @return mixed The repository
     * 
     * @throws InvalidArgumentException if the repository is not defined
     */
    public function getRepository(string $entityClass)
    {
        return $this->entityManager->getRepository($entityClass);
    }

    /**
     * Gets the parameter value for the given name from Container
     *
     * @param string $name The parameter
     * 
     * @return mixed The parameter value
     * 
     * @throws InvalidArgumentException if the parameter is not defined
     */
    public function getParameter(string $name)
    {
        return $this->container->getParameter($name);
    }

    /**
     * Gets the service class for the given id from Container
     *
     * @param string $id The service Id
     * 
     * @return mixed The service class
     * 
     * @throws InvalidArgumentException if the service is not defined
     */
    public function getService(string $id)
    {
        return $this->container->get($id);
    }

    /**
     * Saves given object using the default entity manager
     *
     * @param object $object The object to save
     * @return object The saved object, throws otherwise
     * @throws ORMException if an error has occurred
     */
    public function save($object)
    {
        $em = $this->entityManager;

        try {
            $em->persist($object);
            $em->flush();

            return $object;

        } catch (ORMException $ORMex) {
            throw $ORMex;
        }
    }

    /**
     * Removes given object using the default entity manager
     *
     * @param object $object The object to save
     * @return bool True if object was successfuly removed, throws otherwise
     * @throws ORMException if an error has occurred
     */
    public function remove($object)
    {
        $em = $this->entityManager;

        try {
            $em->remove($object);
            $em->flush();

            return true;

        } catch (ORMException $ORMex) {
            throw $ORMex;
        }
    }

    /**
     * Writes new log or push message to the given logfile using SLogger
     *
     * @param string $content The log content
     * @param string $logFile The log filename without ".log" extension
     * @param string $messageLevel The log message level. "NOTICE" by default
     * @param boolean $pushMessage If true, creates new log message with current date as header, push message otherwise
     */
    public function writeLog(string $content, string $logFile, string $messageLevel = Logger::NOTICE, bool $pushMessage = false)
    {
        $rootDir = $this->getParameter('root_dir');

        $logger = new SLogger($rootDir);

        $logger->writeLog($content, $logFile, $messageLevel, $pushMessage);
    }
}