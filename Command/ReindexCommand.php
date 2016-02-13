<?php

namespace Chebur\SphinxBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class ReindexCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('chebur:sphinx:reindex')
            ->setDescription('Reindex sphinx')
            ->addOption(
                'index',
                'i',
                InputOption::VALUE_OPTIONAL,
                'One index to be regenerated'
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Force rendering new destination config file from template'
            )
            ->addOption(
                'rotate',
                'r',
                InputOption::VALUE_NONE,
                'Rotate or not during reindexing'
            )
            ->addOption(
                'print_queries',
                'p',
                InputOption::VALUE_NONE,
                'Print SQL queries or not during reindexing'
            )
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config      = $this->getContainer()->getParameter('chebur_sphinx_config');
        $config_file = $config['config']['destination'];

        $force_generate = $input->getOption('force');

        //Проверяем на существование файла конфига
        if ($force_generate || !file_exists($config_file)) { //todo дублирование кода
            //Выводим диалог только если просто нет сгенерированного конфига
            if (!$force_generate) {
                $output->writeln('<error>Config file does not exist!</error>');
                /** @var DialogHelper $dialog */
                $dialog = $this->getHelper('dialog');
                if (!$dialog->askConfirmation(
                    $output,
                    '<question>Generate config file from template?</question>',
                    true
                )) {
                    return;
                }
            }

            $process_generate = ProcessBuilder::create()
                ->inheritEnvironmentVariables()
                ->setPrefix('php')
                ->setArguments(array(
                    'app' . DIRECTORY_SEPARATOR . 'console',
                    'chebur:sphinx:generate'
                ))
                ->getProcess();
            $process_generate->run();
            $output->writeln($process_generate->getOutput());
            if (!$process_generate->isSuccessful()) {
                return;
            }
        }

        $pb = ProcessBuilder::create()
            ->inheritEnvironmentVariables()
            ->setPrefix($config['bin'] . DIRECTORY_SEPARATOR . 'indexer')
            ->add($input->getOption('index') ? $input->getOption('index') : '--all')
            ->add('--config')
            ->add($config_file)
        ;

        if ($input->getOption('rotate')) {
            $pb->add('--rotate');
        }

        if ($input->getOption('print_queries')) {
            $pb->add('--print-queries');
        }

        $process = $pb->getProcess();

        $output->writeln('<question>executing</question> '.$process->getCommandLine());

        $process->start();

        while($process->isRunning()) {
            if (!$process->getOutput()) {
                continue;
            }
            $output->writeln($process->getOutput());
            $process->clearOutput();
        }
        $output->writeln($process->getOutput());

        if (!$process->isSuccessful()) {
            $output->writeln('<error>ERROR</error>');
            $output->writeln($process->getExitCodeText());
            return;
        }

        $queriesPost = $config['commands']['reindex']['query_post'];
        foreach($queriesPost as $qp) {
            $output->writeln('<info>Query post:</info> ' . $qp);
            $this->getContainer()
                ->get('chebur.sphinx')
                ->getManager()
                ->createQueryBuilder()
                ->query($qp)
                ->execute()
            ;
        }

        $output->writeln('<info>SUCCESS</info>');
    }

}


