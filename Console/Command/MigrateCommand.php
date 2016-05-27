<?php

namespace FDevs\Migrations\Console\Command;

use FDevs\Migrations\Migration;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class MigrateCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('fdevs:migrations:migrate')
            ->setDescription('Execute a migration to a specified version or the latest available version.')
            ->addArgument('version', InputArgument::OPTIONAL, 'The version number (YYYYMMDDHHMMSS)', 0)
            ->addArgument(
                'direction',
                InputArgument::OPTIONAL,
                sprintf('The direction migrate use "%s" or "%s"', Migration::DIRECTION_UP, Migration::DIRECTION_DOWN),
                Migration::DIRECTION_UP);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $configuration = $this->getMigrationConfiguration();
        $migration = new Migration($configuration);
        $current = $configuration->getCurrentVersion();

        $version = $input->getArgument('version');
        $direction = $input->getArgument('direction');
        $results = $migration->migrate($version, $direction);
        $count = count($results);
        if (!$count) {
            $output->writeln('<comment>No migrations to execute.</comment>');
        } else {
            $output->writeln(sprintf('<comment>"%s" migrations to execute.</comment>', $count));
            /** @var \FDevs\Migrations\Response $result */
            foreach ($results as $result) {
                $message = $result->getMessage();
                $output->writeln(sprintf(
                    '<info>%s:%s</info> migrations to execute by time <info>%s</info>sec.',
                    $result->getClassName(),
                    $result->getMethod(),
                    $result->getTime()
                ));
                if ($message) {
                    $output->writeln(sprintf('with message: <comment>%s</comment>.', $message));
                }
            }
        }
    }
}
