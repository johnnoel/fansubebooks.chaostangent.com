<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Helper\Table,
    Symfony\Component\Console\Question\Question;

/**
 * Manager flagged lines command
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class ManageFlaggedLinesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('fansubebooks:lines:flagged')
            ->setDescription('Manage flagged lines in Fansub Ebooks');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $om = $this->getContainer()->get('doctrine')->getManager();
        $flagRepo = $om->getRepository('Entity:Flag');
        $flags = $flagRepo->getByAdded(); // get all flags

        if (empty($flags)) {
            $output->writeln('<comment>No lines flagged!</comment>');
            return;
        }

        $table = new Table($output);
        $table->setHeaders([ 'ID', 'Line', 'Flagged', 'IP' ]);

        foreach ($flags as $flag) {
            $table->addRow([
                $flag->getId(),
                $flag->getLine()->getLine(),
                $flag->getAdded()->format('Y-m-d H:i:s'),
                $flag->getIp()
            ]);
        }

        $table->render();

        // delete
        $questionHelper = $this->getHelper('question');
        $question = new Question('Lines to delete (range or "all"): ');

        $toDeleteRaw = $questionHelper->ask($input, $output, $question);

        if (!empty($toDeleteRaw)) {
            $toDelete = [];

            if (trim($toDeleteRaw) == 'all') {
                $toDelete = $flags;
            } else {
                $ids = $this->getNumbersFromRangeString($toDeleteRaw);
                $toDelete = array_filter($flags, function($a) use ($ids) {
                    return in_array($a->getId(), $ids);
                });
            }

            foreach ($toDelete as $flag) {
                $om->remove($flag->getLine());
            }

            $om->flush();

            $output->writeln('Deleted <error>'.count($toDelete).'</error> lines');
        }

        // ignore
        /*$question = new Question('Lines to ignore (range or "all")');

        $toIgnoreRaw = $questionHelper->ask($input, $output, $question);

        if (!empty($toIgnoreRaw)) {
        }*/
    }

    /**
     * @param string $range A range string
     * @return array An array of integers
     */
    protected function getNumbersFromRangeString($range)
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
