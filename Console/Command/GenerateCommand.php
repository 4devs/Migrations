<?php

namespace FDevs\Migrations\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

class GenerateCommand extends AbstractCommand
{
    const TYPE_BASE = 'base';
    const TYPE_MONGODB = 'mongodb';

    /**
     * @var string
     */
    private $dir;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $template =
        '<?php
namespace <namespace>;

<use>
use FDevs\Migrations\Response;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version<version> extends <type>
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        // TODO: Implement up() method.
    }
    
    /**
     * {@inheritdoc}
     */
    public function down()
    {
        // TODO: Implement down() method.
    }
}
';

    private $typeList = [
        self::TYPE_BASE => [
            'extends' => 'AbstractMigration',
            'use' => ['FDevs\Migrations\AbstractMigration'],
        ],
        self::TYPE_MONGODB => [
            'extends' => 'MongodbMigration',
            'use' => ['FDevs\Migrations\Migration\MongodbMigration'],
        ],
    ];

    /**
     * GenerateCommand constructor.
     *
     * @param null|string $name
     * @param string      $dir
     * @param string      $namespace
     */
    public function __construct($name = 'fdevs:migrations:generate', $dir = 'app/Resources/Migrations', $namespace = 'App\Migrations')
    {
        $this->dir = $dir;
        $this->namespace = $namespace;
        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Generate a blank migration class.')
            ->addOption('type', null, InputOption::VALUE_OPTIONAL, 'Open file with this command upon creation.', self::TYPE_BASE)
            ->addOption('namespace', null, InputOption::VALUE_OPTIONAL, 'Set namespace.', $this->namespace)
            ->addOption('dir', null, InputOption::VALUE_OPTIONAL, 'Set base directory.', $this->dir)
            ->setHelp(<<<EOT
The <info>%command.name%</info> command generates a blank migration class:
    <info>%command.full_name%</info>
You can optionally specify a <comment>--type</comment> option to open the generated file in your database:
    <info>%command.full_name% --type=mongodb</info>
You can optionally specify a <comment>--namespace</comment> option to set namespace:
    <info>%command.full_name% --namespace="App\Migrations"</info>
You can optionally specify a <comment>--dir</comment> option to set directory:
    <info>%command.full_name% --dir="app/Resources/Migrations"</info>
EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $version = date('YmdHis');
        $path = $this->generateMigration($version, $input->getOption('namespace'), $input->getOption('dir'), $input->getOption('type'));
        $output->writeln(sprintf('Generated new migration class to "<info>%s</info>"', $path));
    }

    /**
     * @return string
     */
    private function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $version
     * @param string $namespace
     * @param string $dir
     * @param string $type
     *
     * @return string
     */
    private function generateMigration($version, $namespace, $dir, $type = self::TYPE_BASE)
    {
        $type = isset($this->typeList[$type]) ? $type : self::TYPE_BASE;
        $use = array_map(function ($el) {
            return sprintf('use %s;', $el);
        }, $this->typeList[$type]['use']);

        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $code = str_replace(['<namespace>', '<version>', '<use>', '<type>'], [$namespace, $version, implode("\n", $use), $this->typeList[$type]['extends']], $this->getTemplate());
        $path = $dir.'/Version'.$version.'.php';
        file_put_contents($path, $code);

        return $path;
    }
}
