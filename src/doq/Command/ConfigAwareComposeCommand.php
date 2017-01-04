<?php

namespace doq\Command;

use doq\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use doq\Compose\Configuration\Exception\ConfigNotFoundException;

class ConfigAwareComposeCommand extends BaseCommand
{
    /**
     * @var \doq\Compose\Command;
     */
    protected $dockerCompose;

    protected function configure()
    {
        $this->addOption(
            'config',
            'c',
            InputOption::VALUE_OPTIONAL,
            'The name of the configuration environment to use',
            'default'
        );
    }

    /**
     * Prepare configuration-aware docker-compose command.
     *
     * @param InputInterface  $input  console input
     * @param OutputInterface $output console output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $output->write('<info>Loading configuration...</info> ', OutputInterface::VERBOSITY_VERY_VERBOSE);

        $this->getContainer()->setParameter('configname', $input->getOption('config'));

        $configuration = $this->getContainer()->get('doq.compose.configuration');
        try {
            $configuration->assertFileExists();
            $this->dockerCompose = $this->getContainer()->get('doq.compose.command');
            return 0;
        } catch (ConfigNotFoundException $e) {
            $output->writeln(PHP_EOL . '<error>Error:</error> ' . $e->getMessage());
            return 1;
        }
    }

}
