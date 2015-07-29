<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Helper\ProgressBar;

/**
 * Populates the existing tweets with their tweet IDs from a Twitter archive
 * download
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class PopulateTweetIdsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('fansubebooks:tweets:populate')
            ->setDescription('Populate Tweet IDs from a Twitter archive')
            ->addArgument('csv', InputArgument::REQUIRED, 'Twitter archive CSV file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $csvFilename = $input->getArgument('csv');

        if (!file_exists($csvFilename) || !is_readable($csvFilename)) {
            $output->writeln('<error>Could not read file "'.$csvFilename.'"</error>');
            return;
        }

        $fh = fopen($csvFilename, 'r');
        $headers = fgetcsv($fh, 8192);

        if (count($headers) != 10) {
            $output->writeln('<error>Header line doesn\'t contain the expected 10 fields</error>');
            return;
        }

        $om = $this->getContainer()->get('doctrine')->getManager();
        $tweetRepo = $om->getRepository('Entity:Tweet');

        $progress = new ProgressBar($output, $tweetRepo->getTotal());

        $sql = 'SELECT t.id
            FROM tweets t
            JOIN lines l ON l.id = t.line_id
            WHERE (l.line = :line)
                AND (t.tweeted >= :start)
                AND (t.tweeted <= :end)
            LIMIT 1';

        $dbal = $om->getConnection();
        $selectStmt = $dbal->prepare($sql);

        $sql = 'UPDATE tweets SET tweet_id = :tweet_id WHERE id = :id';
        $updateStmt = $dbal->prepare($sql);

        $poolCount = 0;
        $poolLimit = 100;

        $progress->start();

        while (!feof($fh)) {
            $line = fgetcsv($fh, 8192);
            if ($line === false || is_array($line) && count($line) == 0) {
                continue;
            }

            $tweetId = $line[0];
            $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s O', $line[3]);
            $dateTime->setTimezone(new \DateTimeZone('UTC'));
            $text = html_entity_decode($line[5]);

            $start = clone $dateTime;
            $start->sub(new \DateInterval('PT15M'));

            $end = clone $dateTime;
            $end->add(new \DateInterval('PT15M'));

            $selectStmt->execute([
                'line' => $text,
                'start' => $start->format('Y-m-d H:i:s'),
                'end' => $end->format('Y-m-d H:i:s'),
            ]);

            $id = $selectStmt->fetchColumn(0);

            if ($id === false) {
                $output->writeln('Couldn\'t find tweet for line <comment>'.$text.'</comment>');
                // create one?
                continue;
            }

            $success = $updateStmt->execute([
                'tweet_id' => $tweetId,
                'id' => $id,
            ]);

            if (!$success) {
                $output->writeln('<error>ERRORORORORO</error>');
                return;
            }

            $progress->advance();
        }

        $progress->finish();
    }
}
