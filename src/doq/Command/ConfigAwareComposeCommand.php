<?php

namespace doq\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use doq\Command\ComposeCommand;
use doq\Compose\Configuration;
use doq\Compose\Configuration\Exception\ConfigNotFoundException;

class ConfigAwareComposeCommand extends ComposeCommand
{
    /**
     * @var \doq\Compose\Configuration;
     */
    protected $configuration;

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
    protected function useComposeConfiguration(InputInterface $input, OutputInterface $output)
    {
        $output->write('<info>Loading configuration...</info> ', OutputInterface::VERBOSITY_VERY_VERBOSE);

        $this->configuration = new Configuration($input->getOption('config'));

        try {
            $this->configuration->assertFileExists();
            $this->dockerCompose = $this->getDockerComposeCommand();
            $this->dockerCompose->setConfiguration($this->configuration);
            return 0;
        } catch (ConfigNotFoundException $e) {
            $output->writeln(PHP_EOL . '<error>Error:</error> ' . $e->getMessage());
            return 1;
        }
    }

}
