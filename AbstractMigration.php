<?php
namespace FDevs\Migrations;

abstract class AbstractMigration implements MigrationInterface
{
    /**
     * @var array|Response[]
     */
    private $response = [];

    /**
     * {@inheritDoc}
     */
    public function postUp()
    {
        return;
    }

    /**
     * {@inheritDoc}
     */
    public function preUp()
    {
        return;
    }

    /**
     * {@inheritDoc}
     */
    public function preDown()
    {
        return;
    }

    /**
     * {@inheritDoc}
     */
    public function postDown()
    {
        return;
    }

    /**
     *{@inheritDoc}
     */
    public function setResponseByMethod($method, Response $response = null)
    {
        $this->response[$method] = $response;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getResponseByMethod($method)
    {
        return isset($this->response[$method]) ? $this->response[$method] : null;
    }
}
