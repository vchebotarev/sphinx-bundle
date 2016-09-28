<?php

namespace Chebur\SphinxBundle\Command;

use Chebur\SphinxBundle\Sphinx\Manager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
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
                InputOption::VALUE_REQUIRED,
                'Query to execute'
            )
            //todo add option to choose Sphinx manager
            //todo add option to execute 'SHOW META'
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $query = $input->getArgument('query');
        if (!$query) {
            $output->writeln('<error>No query to execute</error>');
            return;
        }

        $managerName = Manager::DEFAULT_NAME; //$input->getArgument('manager'); //todo
        $manager = $this
            ->getContainer()
            ->get('chebur.sphinx')
            ->getManager($managerName)
        ;
        if (!$manager) {
            $output->writeln('<error>No sphinx manager with name "' . $managerName . '"</error>');
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

        if (!$countFetched) {
            return;
        }

        $rows = $result->fetchAllAssoc();
        $table = new Table($output);
        $table->setHeaders(array_keys($rows[0]));
        $table->setRows($rows);
        $table->render();
    }

}
