<?php

namespace FDevs\Migrations\Provider;

use Doctrine\ODM\MongoDB\DocumentManager;
use FDevs\Migrations\Migration\MongodbMigration;
use FDevs\Migrations\MigrationInterface;

class MongodbProvider implements ProviderInterface
{
    /** @var DocumentManager */
    private $manager;

    /**
     * init.
     *
     * @param DocumentManager $manager
     */
    public function __construct(DocumentManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare(MigrationInterface $migration)
    {
        if ($this->isSupport($migration)) {
            $migration->setDocumentManager($this->manager);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isSupport(MigrationInterface $migration)
    {
        return $migration instanceof MongodbMigration;
    }
}
