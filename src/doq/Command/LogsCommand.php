<?php

namespace doq\Command;

use doq\Command\ConfigAwareComposeCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class LogsCommand extends ConfigAwareComposeCommand
{
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('logs')
            ->setDescription('Builds, (re)creates and starts service containers using docker-compose.')
            ->addArgument(
                'service',
                InputArgument::OPTIONAL,
                'The docker service name to use',
                ''
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($result = parent::execute($input, $output) != 0) {
            return $result;
        }

        try {
            $this->dockerCompose->execute('logs', [], [$input->getArgument('service')]);

            $output->writeln(PHP_EOL . $this->dockerCompose->getOutput());
        } catch (\Exception $e) {
            $output->writeln(PHP_EOL . '<error>Error:</error> Failed to get status from docker-compose');
        }

        return $this->dockerCompose->getResult();
    }
}
