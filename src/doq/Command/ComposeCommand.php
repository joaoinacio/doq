<?php

namespace doq\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use doq\Command\BaseCommand;
use doq\Compose\Command;

class ComposeCommand extends BaseCommand
{
    protected $dockerCompose;

    protected function getDockerComposeCommand()
    {
        return new Command();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->dockerCompose = $this->getDockerComposeCommand();
    }

}
