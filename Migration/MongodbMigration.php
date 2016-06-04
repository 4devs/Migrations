<?php

namespace FDevs\Migrations\Migration;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use FDevs\Migrations\AbstractMigration;

abstract class MongodbMigration extends AbstractMigration
{
    /**
     * @var DocumentManager
     */
    private $manager;

    /**
     * {@inheritdoc}
     */
    public function setDocumentManager(DocumentManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return DocumentManager
     */
    public function getDocumentManager()
    {
        return $this->manager;
    }

    /**
     * @param string $repositoryNotation
     *
     * @return \Doctrine\ODM\MongoDB\DocumentRepository
     */
    protected function getRepository($repositoryNotation)
    {
        return $this->manager->getRepository($repositoryNotation);
    }

    /**
     * @param DocumentRepository $repository
     *
     * @return \MongoCollection
     */
    protected function selectCollection(DocumentRepository $repository)
    {
        $collectionName = $repository->getClassMetadata()->getCollection();
        $database = $this->manager->getConfiguration()->getDefaultDB();
        $connection = $this->manager->getConnection();
        $client = $connection->getMongoClient();

        return $client->selectCollection($database, $collectionName);
    }

    /**
     * @param string $className
     *
     * @return \Doctrine\MongoDB\Collection
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    protected function getCollection($className)
    {
        return $this->getDocumentManager()->getDocumentCollection($className);
    }
}
