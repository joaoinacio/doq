<?php

namespace doq\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use doq\Compose\Configuration\Services;
use Exception;

class ServiceListCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('service:list')
            ->setDescription('Lists the services and basic information defined in the docker compose configuration.')
            ->addOption(
                'config',
                'c',
                InputOption::VALUE_OPTIONAL,
                'The name of the configuration environment to use',
                'default'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $configName = $input->getOption('config');
            $configServices = new Services($configName);

            $output->writeln('');
            $output->writeln(sprintf(
                '<info>Docker service definitions for config <comment>"%s"</comment>.</info>',
                $configName
            ));

            $serviceList = $configServices->getServicesDefinition();
            foreach ($serviceList as $name => $service) {
                $output->writeln(sprintf("  service:  <info>%s</info>", $name));
                $output->writeln(sprintf("    image:  <comment>%s</comment>", $service['image']));
                $output->writeln(sprintf("    ports:  <comment>%s</comment>", $service['ports']));
                $output->writeln(sprintf("    links:  <comment>%s</comment>", $service['links']));
                $output->writeln(sprintf("   mounts:  <comment>%s</comment>", $service['mounts']));
                $output->writeln('');
            }
        } catch (Exception $e) {
            $output->writeln('<error>Error:</error> ' . $e->getMessage());
        }
    }
}
