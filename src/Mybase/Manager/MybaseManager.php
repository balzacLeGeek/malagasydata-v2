<?php

/**
 * MybaseManager - Contains the most used methods inside a Manager
 */
namespace App\Mybase\Manager;

trait MybaseManager 
{
    /**
     * Finds all objects
     *
     * @return array Collection of object if exists, empty array otherwise
     */
    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    /**
     * Finds object by criterias
     *
     * @param array $params The search criterias
     * @return array Collection of object if criterias match, empty array otherwise
     */
    public function findBy(array $params): array
    {
        return $this->repository->findBy($params);
    }

    /**
     * Finds one object by criterias
     *
     * @param array $params The search criterias
     * @return mixed Object if criterias match, null otherwise
     */
    public function findOneBy(array $params)
    {
        return $this->repository->findOneBy($params);
    }
}