<?php

namespace FDevs\Migrations\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InfoCommand extends AbstractCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('fdevs:migrations:info')
            ->setDescription('View the info of a set of migrations.');
    }

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $configuration = $this->getMigrationConfiguration();

        $current = $configuration->getCurrentVersion();
        $output->writeln(sprintf('<info>current version %s.</info>', $current));
        $versions = $configuration->getMigrations();
        $output->writeln('<comment>allowed migrations to execute.</comment>');
        foreach ($versions as $version) {
            $output->writeln(sprintf('<info>%s</info> class <info>%s</info>', $version->getVersion(), $version->getClass()));
        }
    }
}
