<?php

namespace doq\Command;

use doq\Command\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use doq\Compose\Template;
use doq\Compose\Configuration\Exception\ConfigExistsException;
use doq\Compose\Command\Exception\CommandFailedException;

class InitCommand extends BaseCommand
{
    protected function configure()
    {
        $this
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
            $this->getContainer()->setParameter('configname', $input->getArgument('config'));
            $this->getContainer()->setParameter('templateSource', $input->getOption('template'));

            $configuration = $this->getContainer()->get('doq.compose.configuration');
            $template = $this->getContainer()->get('doq.compose.template');

            $configuration->createFromTemplate($template);

            $output->writeln('<info>Done.</info>');
        } catch (ConfigExistsException $e) {
            $output->writeln(PHP_EOL . '<error>Error:</error> ' . $e->getMessage());
        } catch (CommandFailedException $e) {
            $output->writeln(PHP_EOL . '<error>Error:</error> ' . $e->getMessage());
        }
    }
}
