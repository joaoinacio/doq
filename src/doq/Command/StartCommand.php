<?php

namespace doq\Command;

use doq\Command\ConfigAwareComposeCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use doq\Compose\Command\Exception\CommandFailedException;

class StartCommand extends ConfigAwareComposeCommand
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (($result = parent::execute($input, $output)) !== 0) {
            return $result;
        }

        $output->writeln('<info>Starting docker service containers...</info> ');

        try {
            $this->dockerCompose->execute('up -d');

            $output->writeln($this->dockerCompose->getOutput(), OutputInterface::VERBOSITY_VERBOSE);
            $output->writeln('<info>Done.</info>');
        } catch (CommandFailedException $e) {
            $output->writeln('<error>Error:</error> Failed to bring up the containers using docker-compose');
            $output->writeln($this->dockerCompose->getOutput());
        }

        return $this->dockerCompose->getResult();
    }
}
