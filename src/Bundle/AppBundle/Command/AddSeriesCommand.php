<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Output\OutputInterface;
use ChaosTangent\FansubEbooks\Entity\Series;

/**
 * Add series command
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class AddSeriesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('fansubebooks:series:add')
            ->setDescription('Add a series to Fansub Ebooks')
            ->addArgument('title', InputArgument::REQUIRED, 'Title')
            ->addArgument('image', InputArgument::REQUIRED, 'Image (400x400)')
            ->addArgument('thumbnail', InputArgument::REQUIRED, 'Thumbnail (150x150)')
            ->addOption('alias', 'a', InputOption::VALUE_REQUIRED, 'If set, the alias of the series');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $title = $input->getArgument('title');
        $image = $input->getArgument('image');
        $thumbnail = $input->getArgument('thumbnail');
        $alias = $input->getOption('alias');

        $series = new Series();
        $series->setTitle($title)
            ->setImage($image)
            ->setThumbnail($thumbnail);

        if ($alias) {
            $series->setAlias($alias);
        }

        $om = $this->getContainer()->get('doctrine')->getManager();
        $repo = $om->getRepository(Series::class);

        $repo->create($series);

        $serializer = $this->getContainer()->get('jms_serializer');

        $output->write($serializer->serialize($series, 'json'));
    }
}
