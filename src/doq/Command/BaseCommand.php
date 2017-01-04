<?php

namespace doq\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCommand extends Command
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    public function __construct($name, $description = '')
    {
        parent::__construct($name);
        $this->setDescription($description);
    }

    /**
     * @return \Symfony\Component\DependencyInjection\Container
     */
    protected function getContainer()
    {
        return $this->getApplication()->getContainer();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->set('input', $input);
        $this->getContainer()->set('output', $output);
    }
}
