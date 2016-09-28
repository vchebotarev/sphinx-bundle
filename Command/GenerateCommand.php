<?php

namespace Chebur\SphinxBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('chebur:sphinx:generate')
            ->setDescription('Renders config template to destination file')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sphinxConfig = $this->getContainer()->getParameter('chebur_sphinx_config')['config'];

        //Все необходимые данные для постановки
        $config_params = array(
            'sources'     => $sphinxConfig['sources'],
            'searchd'     => $sphinxConfig['searchd'],
            'parameters'  => $sphinxConfig['parameters'],
        );
        $sphinxConfigTemplate = $sphinxConfig['template'];

        //Добавляем в твиг путь возможного расположения шаблона
        /** @var \Twig_Loader_Filesystem $loader */
        $loader = $this->getContainer()
            ->get('twig')
            ->getLoader()
        ;
        $loader->addPath(dirname($sphinxConfigTemplate));

        //Рендерим шаблон конфига
        $configContent = $this
            ->getContainer()
            ->get('templating')
            ->renderResponse(basename($sphinxConfigTemplate), $config_params)
            ->getContent()
        ;

        try { //Записываем в указанный файл
            $dir = pathinfo($sphinxConfig['destination'])['dirname'];
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            $file              = fopen($sphinxConfig['destination'], 'w+');
            $configContentSize = fwrite($file, $configContent);
            fclose($file);
        } catch(\Exception $e) {
            $output->writeln('<error>Error generating config file</error> ' . $e->getMessage());
            return;
        }

        $output->writeln('<info>Config file generated successfully (size ' . $configContentSize . ' b)</info>');
        $output->writeln('<info>Destination: </info>'.$sphinxConfig['destination']);
    }

}
