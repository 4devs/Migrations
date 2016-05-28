<?php

namespace FDevs\Migrations\Configuration;

use FDevs\Migrations\Exception\DirectionException;
use FDevs\Migrations\Migration;
use Symfony\Component\Finder\Finder;
use FDevs\Migrations\Version;

abstract class AbstractConfiguration implements ConfigurationInterface
{
    /** @var array|Version[] */
    private $migrations = [];

    /**
     * set dirs.
     *
     * @param array $dirs
     */
    public function setDirs(array $dirs)
    {
        if (count($dirs)) {
            $this->load($dirs);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMigrationsToExecute($direction, $to)
    {
        $versions = $this->getMigrations();
        $current = $this->getCurrentVersion();

        if ($direction === Migration::DIRECTION_DOWN) {
            krsort($versions);
            $result = array_filter($versions, function (Version $version) use ($to, $current) {
                $v = $version->getVersion();

                return $v >= $to && $v <= $current;
            });
        } elseif ($direction === Migration::DIRECTION_UP) {
            $result = array_filter($versions, function (Version $version) use ($to, $current) {
                $v = $version->getVersion();

                return $v <= $to && $v > $current;
            });
        } else {
            throw DirectionException::create($direction);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getLatestVersion()
    {
        end($this->migrations);
        $key = key($this->migrations);

        return $key === null ? 0 : $key;
    }

    /**
     * {@inheritdoc}
     */
    public function getMigrations()
    {
        return $this->migrations;
    }

    /**
     * @param string $className
     *
     * @return $this
     */
    protected function addMigration($className)
    {
        if (!$this->isTransient($className)) {
            $version = new Version($this, $className, $this->getProvider());
            $this->migrations[$version->getVersion()] = $version;
        }

        return $this;
    }

    /**
     * load migrations.
     *
     * @param array $dirs
     *
     * @return self
     */
    protected function load(array $dirs)
    {
        $finder = Finder::create()
            ->files()
            ->filter(function (\SplFileInfo $file) {
                return 1 === substr_count($file->getBasename(), '.') && preg_match('/.+\d{14}\.php$/', $file->getBasename());
            })
            ->in($dirs);
        $classes = get_declared_classes();
        foreach ($finder as $file) {
            $sourceFile = realpath($file->getPathName());
            require_once $sourceFile;
        }

        $declared = array_diff(get_declared_classes(), $classes);
        foreach ($declared as $className) {
            $this->addMigration($className);
        }
        ksort($this->migrations);

        return $this;
    }

    /**
     * Check if a given fixture is transient and should not be considered a data fixtures
     * class.
     *
     * @return bool
     */
    private function isTransient($className)
    {
        $rc = new \ReflectionClass($className);
        if ($rc->isAbstract()) {
            return true;
        }
        $interfaces = class_implements($rc->name);

        return in_array('FDevs\Migrations\MigrationInterface', $interfaces) ? false : true;
    }
}
