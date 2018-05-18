<?php

namespace Chebur\SphinxBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class QueryCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('chebur:sphinx:query');
        $this->setDescription('Execute sphinx query');
        $this->addArgument(
            'query',
            InputArgument::REQUIRED,
            'Query to execute'
        );
        $this->addOption(
            'meta',
            'm',
            InputOption::VALUE_NONE,
            'Execute "SHOW META" after the query'
        );
        $this->addOption(
            'connection',
            'c',
            InputOption::VALUE_REQUIRED,
            'Connection name to execute query'
        );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $query = $input->getArgument('query');

        $managerName = null;
        if ($input->getOption('connection')) {
            $managerName = $input->getOption('connection');
        }
        $manager = $this->getContainer()->get('chebur.sphinx')->getManager($managerName);
        if (!$manager) {
            $output->writeln('<error>No connection with name "' . $managerName . '" found</error>');
            return;
        }

        try {
            $result = $manager->createQueryBuilder()->query($query)->execute();
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return;
        }

        $countFetched  = $result->getCount();
        $countAffected = $result->getAffectedRows();

        if (!$countAffected && !$countFetched) {
            $output->writeln('<info>No result</info>');
        } else {
            if ($countFetched) {
                $output->writeln('<info>Rows fetched:</info> ' . $countFetched);
            }
            if ($countAffected) {
                $output->writeln('<info>Rows affected:</info> ' . $countAffected);
            }
        }

        if ($countFetched) {
            $rows = $result->fetchAllAssoc();
            $table = new Table($output);
            $table->setHeaders(array_keys($rows[0]));
            $table->setRows($rows);
            $table->render();
        }

        if ($input->getOption('meta')) {
            $result = $manager->getHelper()->showMeta()->execute();
            $rows = $result->fetchAllAssoc();
            $table = new Table($output);
            $table->setHeaders(array_keys($rows[0]));
            $table->setRows($rows);
            $table->render();
        }
    }

}
