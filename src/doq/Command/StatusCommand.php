<?php

namespace doq\Command;

use doq\Command\ConfigAwareComposeCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use doq\Compose\Command\Exception\CommandFailedException;

class StatusCommand extends ConfigAwareComposeCommand
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (($result = parent::execute($input, $output)) !== 0) {
            return $result;
        }

        try {
            $this->dockerCompose->execute('ps');

            $output->writeln(PHP_EOL . $this->dockerCompose->getOutput());
        } catch (CommandFailedException $e) {
            $output->writeln(PHP_EOL . '<error>Error:</error> Failed to get status from docker-compose');
        }

        return $this->dockerCompose->getResult();
    }
}
