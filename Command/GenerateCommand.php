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
        $sphinx_config = $this->getContainer()->getParameter('chebur_sphinx_config')['config'];

        //Все необходимые данные для постановки
        $config_params = array(
            'sources'     => $sphinx_config['sources'],
            'searchd'     => $sphinx_config['searchd'],
            'parameters'  => $sphinx_config['parameters'],
        );
        $sphinx_config_template = $sphinx_config['template'];

        //Добавляем в твиг путь возможного расположения шаблона
        /** @var \Twig_Loader_Filesystem $loader */
        $loader = $this->getContainer()
            ->get('twig')
            ->getLoader()
        ;
        $loader->addPath(dirname($sphinx_config_template));

        //Рендерим шаблон конфига
        $config_content = $this
            ->getContainer()
            ->get('templating')
            ->renderResponse(basename($sphinx_config_template), $config_params)
            ->getContent()
        ;

        //todo test
        try { //Записываем в указанный файл
            $dir = pathinfo($sphinx_config['destination'])['dirname'];
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            $file                = fopen($sphinx_config['destination'], 'w+');
            $config_content_size = fwrite($file, $config_content);
            fclose($file);
        } catch(\Exception $e) {
            $output->writeln('<error>Error generating config file</error> ' . $e->getMessage());
            return;
        }

        $output->writeln('<info>Config file generated successfully (size ' . $config_content_size . ' b)</info>');
    }

}
