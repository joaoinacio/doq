<?php

namespace doq\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use doq\Compose\Configuration;
use doq\Compose\Template;
use doq\Exception\ConfigExistsException;

class InitCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Setup a new environment configuration, optionally using a pre-existing template.')
            ->addArgument(
                'config',
                InputArgument::OPTIONAL,
                'The name of the configuration environment to use',
                'default'
            )
            ->addOption(
                'template',
                't',
                InputOption::VALUE_OPTIONAL,
                'Template source (name, path or url) to use for configuration',
                null
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Initializing new docker-compose config environment...</info> ');

        try {
            $config = new Configuration($input->getArgument('config'));
            $template = new Template($input->getOption('template'));
            $config->createFromTemplate($template);

            $output->writeln('<info>Done.</info>');
        } catch (ConfigExistsException $e) {
            $output->writeln(PHP_EOL . '<error>Error:</error> ' . $e->getMessage());
        } catch (Exception $e) {
            $output->writeln(PHP_EOL . '<error>Error:</error> ' . $e->getMessage());
        }
    }
}
