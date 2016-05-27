<?php

namespace FDevs\Migrations\Configuration;

use FDevs\Migrations\Exception\DirectionException;
use FDevs\Migrations\Exception\UnknownVersionException;
use FDevs\Migrations\Migration;
use FDevs\Migrations\Provider\ProviderInterface;

class FilesConfiguration extends AbstractConfiguration
{
    const EXT = 'version';

    /** @var string */
    private $cacheFolder;

    /** @var ProviderInterface */
    private $provider;

    /**
     * init.
     *
     * @param array             $dirs
     * @param string            $cacheFolder
     * @param ProviderInterface $provider
     */
    public function __construct(array $dirs, $cacheFolder, ProviderInterface $provider)
    {
        $this->provider = $provider;
        $this->cacheFolder = $cacheFolder;
        $this->setDirs($dirs);
    }

    /**
     * {@inheritdoc}
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentVersion()
    {
        $current = 0;
        $pattern = '/(\d{14})\.'.self::EXT.'/';

        $files = glob($this->cacheFolder.'/*.'.self::EXT.'');
        foreach ($files as $file) {
            if (preg_match($pattern, $file, $data)) {
                $data = intval($data[1]);
                $current = $current > $data ? $current : $data;
            }
        }

        return $current;
    }

    /**
     * {@inheritdoc}
     */
    public function updateVersion($version, $direction)
    {
        chdir($this->cacheFolder);
        $filename = $version.'.'.self::EXT;
        if ($direction === Migration::DIRECTION_UP) {
            $fp = fopen($filename, 'w');
            fwrite($fp, $version);
            fclose($fp);
        } elseif (!file_exists($filename)) {
            throw UnknownVersionException::create($version);
        } elseif ($direction === Migration::DIRECTION_DOWN) {
            unlink($filename);
        } else {
            throw DirectionException::create($direction);
        }

        return $this;
    }
}
