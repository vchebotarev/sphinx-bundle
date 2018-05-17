<?php

namespace Chebur\SphinxBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder; //todo

class ReindexCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('chebur:sphinx:reindex')
            ->setDescription('Reindex sphinx')
            ->addArgument(
                'index',
                InputArgument::IS_ARRAY
            )
            ->addOption(
                'rotate',
                'r',
                InputOption::VALUE_NONE,
                'Rotate or not during reindexing'
            )
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
            ->setPrefix($config['commands']['bin'] . DIRECTORY_SEPARATOR . 'indexer')
            ->add('--config')
            ->add($configFile)
        ;
        if (!empty($input->getArgument('index'))) {
            foreach($input->getArgument('index') as $arg) {
                $pb->add($arg);
            }
        } else {
            $pb->add('--all');
        }

        if ($input->getOption('rotate')) {
            $pb->add('--rotate');
        }

        $process = $pb->getProcess();
        $process->start();
        $output->writeln('<info>executing</info> ' . $process->getCommandLine());

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
