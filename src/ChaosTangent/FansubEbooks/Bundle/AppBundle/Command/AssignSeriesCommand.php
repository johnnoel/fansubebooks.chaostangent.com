<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

/**
 * Assign series command
 *
 * On initial import of data files won't have a series assigned to them, this
 * command allows you to assign a series (from ID or alias) to a range of
 * file IDs
 *
 * File IDs can be assigned using range syntax, e.g. 1-5,10,12-15
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class AssignSeriesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('fansubebooks:series:assign')
            ->setDescription('Assign series to files')
            ->addArgument('file_ids', InputArgument::REQUIRED, 'File IDs')
            ->addArgument('series', InputArgument::REQUIRED, 'Series alias or ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileIds = $this->getFileIdsFromRange($input->getArgument('file_ids'));
        $seriesId = $input->getArgument('series');

        if (empty($fileIds)) {
            $output->writeln('<error>No valid file IDs passed</error>');
            return;
        }

        $om = $this->getContainer()->get('doctrine')->getManager();
        $seriesRepo = $om->getRepository('Entity:Series');

        $series = $seriesRepo->findOneByAlias($seriesId);
        if ($series === null) {
            $series = $seriesRepo->find($seriesId);

            if ($series === null) {
                $output->writeln('<error>Could not find series with alias/ID: '.$seriesId.'</error>');
                return;
            }
        }

        $fileRepo = $om->getRepository('Entity:File');
        $files = $fileRepo->findById($fileIds);

        if (empty($files)) {
            $output->writeln('<error>Could not find any files with IDs: '.implode(', ', $fileIds).'</error>');
            return;
        }

        foreach ($files as $file) {
            $file->setSeries($series);
            $om->persist($file);
        }

        $om->flush();

        $output->writeln('Updated <comment>'.count($files).'</comment> files with series <comment>'.$series->getTitle().'</comment>');
    }

    /**
     * @param string $range
     * @return array An array of integers
     */
    protected function getFileIdsFromRange($range)
    {
        if (empty($range)) {
            return [];
        }

        $ids = [];
        $parts = explode(',', $range);
        foreach ($parts as $part) {
            if (strpos($part, '-') !== false) {
                $ids = array_merge($ids, $this->expandRange($part));
            } else {
                $ids[] = intval($part);
            }
        }

        sort($ids, SORT_NUMERIC);
        return $ids;
    }

    /**
     * Expand a 10-15 style range into an array of numbers
     *
     * @param string $range
     * @return array An array of integers
     */
    protected function expandRange($range)
    {
        $parts = explode('-', $range, 2);
        return range(intval($parts[0]), intval($parts[1]));
    }
}
