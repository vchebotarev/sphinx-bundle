<?php

namespace Chebur\SphinxBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder; //todo

class StopCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('chebur:sphinx:stop')
            ->setDescription('Stop sphinx (searchd)')
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config     = $this->getContainer()->getParameter('chebur_sphinx_config');
        $configFile = $config['config']['destination'];

        //Проверяем на существование файла конфига
        if (!file_exists($configFile)) {
            $output->writeln('<error>Config file not found. Run "chebur:sphinx:generate" first.</error>');
            return;
        }

        $pb = ProcessBuilder::create()
            ->inheritEnvironmentVariables()
            ->setPrefix($config['commands']['bin'] . DIRECTORY_SEPARATOR . 'searchd')
            ->add('--config')
            ->add($configFile)
            ->add('--stop')
        ;

        $process = $pb->getProcess();
        $process->start();
        $output->writeln('<info>executing</info> '.$process->getCommandLine());

        while($process->isRunning()) {
            if (!$process->getOutput()) {
                continue;
            }
            $output->writeln($process->getOutput());
            $process->clearOutput();
        }
        $output->writeln($process->getOutput());

        if (!$process->isSuccessful()) {
            $output->writeln('<error>' . $process->getExitCodeText() . '</error>');
            return;
        }
    }

}
