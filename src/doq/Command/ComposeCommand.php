<?php

namespace doq\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;
use doq\Compose\Command;

class ComposeCommand extends BaseCommand
{
    /**
     * @var \doq\Compose\Command;
     */
    protected $dockerCompose;

    /**
     *
     * @return \doq\Compose\Command
     */
    protected function getDockerComposeCommand()
    {
        return new Command();
    }
}
