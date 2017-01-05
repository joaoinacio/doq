<?php

namespace doq\Command;

use doq\Command\ConfigAwareComposeCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use doq\Compose\Command\Exception\CommandFailedException;

class StopCommand extends ConfigAwareComposeCommand
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (($result = parent::execute($input, $output)) !== 0) {
            return $result;
        }

        $output->writeln('<info>Stopping docker service containers...</info> ');

        try {
            $this->dockerCompose->execute('stop');

            $output->writeln($this->dockerCompose->getOutput(), OutputInterface::VERBOSITY_VERBOSE);
            $output->writeln('<info>Done.</info>');
        } catch (CommandFailedException $e) {
            $output->writeln(PHP_EOL . '<error>Error:</error> Failed to stop the containers using docker-compose');
            $output->writeln($this->dockerCompose->getOutput());
        }

        return $this->dockerCompose->getResult();
    }
}
