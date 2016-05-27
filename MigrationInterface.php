<?php

namespace FDevs\Migrations;

interface MigrationInterface
{
    /**
     * run pre migration command.
     *
     * @return Response|null
     */
    public function preUp();

    /**
     * run migration.
     *
     * @return Response|null
     */
    public function up();

    /**
     * run post migration command.
     *
     * @return Response|null
     */
    public function postUp();

    /**
     * pre down.
     *
     * @return Response|null
     */
    public function preDown();

    /**
     * down.
     *
     * @return Response|null
     */
    public function down();

    /**
     * post down.
     *
     * @return Response|null
     */
    public function postDown();

    /**
     * set response by method.
     *
     * @param string   $method   example `postDown` or `down` or any method MigrationInterface
     * @param Response $response
     *
     * @return mixed
     */
    public function setResponseByMethod($method, Response $response = null);

    /**
     * get response by method example `postDown` or `down` or any method MigrationInterface.
     *
     * @param string $method
     *
     * @return Response|null
     */
    public function getResponseByMethod($method);
}
