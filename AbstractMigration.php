<?php

namespace FDevs\Migrations;

abstract class AbstractMigration implements MigrationInterface
{
    /**
     * @var array|Response[]
     */
    private $response = [];

    /**
     * {@inheritdoc}
     */
    public function postUp()
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function preUp()
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function preDown()
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function postDown()
    {
        return;
    }

    /**
     *{@inheritdoc}
     */
    public function setResponseByMethod($method, Response $response = null)
    {
        $this->response[$method] = $response;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseByMethod($method)
    {
        return isset($this->response[$method]) ? $this->response[$method] : null;
    }
}
