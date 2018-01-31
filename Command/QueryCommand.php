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
        $this
            ->setName('chebur:sphinx:query')
            ->setDescription('Execute sphinx query')
            ->addArgument(
                'query',
                InputArgument::REQUIRED,
                'Query to execute'
            )
            ->addOption(
                'meta',
                'm',
                InputOption::VALUE_NONE,
                'Execute "SHOW META" after the query'
            )
            ->addOption(
                'connection',
                'c',
                InputOption::VALUE_REQUIRED,
                'Connection name to execute query'
            )
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $query = $input->getArgument('query');
        if (!$query) {
            $output->writeln('<error>No query to execute</error>');
            return;
        }

        $managerName = null;
        if ($input->getOption('connection')) {
            $managerName = $input->getOption('connection');
        }
        $manager = $this
            ->getContainer()
            ->get('chebur.sphinx')
            ->getManager($managerName)
        ;
        if (!$manager) {
            $output->writeln('<error>No connection with name "' . $managerName . '"</error>');
            return;
        }

        try{
            $result = $manager
                ->createQueryBuilder()
                ->query($query)
                ->execute()
            ;
        } catch(\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return;
        }

        $countFetched  = $result->getCount();
        $countAffected = $result->getAffectedRows();

        $output->writeln('<info>Rows affected:</info> ' . $countAffected);
        $output->writeln('<info>Rows fetched:</info> ' . $countFetched);

        if ($countFetched) {
            $rows = $result->fetchAllAssoc();
            $table = new Table($output);
            $table->setHeaders(array_keys($rows[0]));
            $table->setRows($rows);
            $table->render();
        }

        if ($input->getOption('meta')) {
            $result = $manager
                ->createQueryBuilder()
                ->query('SHOW META')
                ->execute()
            ;
            $output->writeln('SHOW META');
            $rows = $result->fetchAllAssoc();
            $table = new Table($output);
            $table->setHeaders(array_keys($rows[0]));
            $table->setRows($rows);
            $table->render();
        }
    }

}
