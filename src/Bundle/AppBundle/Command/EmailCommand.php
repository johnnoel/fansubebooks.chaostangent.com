<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;
use ChaosTangent\FansubEbooks\Bundle\AppBundle\Activity\Entry;

/**
 * Construct and send an e-mail with latest activity in it
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class EmailCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('fansubebooks:email')
            ->setDescription('E-mail activity');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $now = new \DateTime('now', new \DateTimezone('UTC'));
        $now->setTime(23, 59, 59);

        $lastWeek = new \DateTime('now', new \DateTimezone('UTC'));
        $lastWeek->sub(new \DateInterval('P1W'));
        $lastWeek->setTime(0, 0, 0);

        $activityAgg = $this->getContainer()->get('fansubebooks.activity.aggregator');
        $activity = $activityAgg->getActivity($lastWeek, $now, 1000);

        // no activity? boooo
        if (count($activity) === 0) {
            return;
        }

        $upVotes = array_filter($activity, function($a) { return $a->getType() == Entry::ACTIVITY_VOTE_UP; });
        $downVotes = array_filter($activity, function($a) { return $a->getType() == Entry::ACTIVITY_VOTE_DOWN; });
        $flags = array_filter($activity, function($a) { return $a->getType() == Entry::ACTIVITY_FLAG; });
        $seriesSuggestions = array_filter($activity, function($a) { return $a->getType() == Entry::ACTIVITY_SUGGEST_SERIES; });
        $scriptSuggestions = array_filter($activity, function($a) { return $a->getType() == Entry::ACTIVITY_SUGGEST_SCRIPT; });

        $viewParams = [
            'start' => $lastWeek,
            'finish' => $now,
            'activity' => $activity,
            'votes_up' => $upVotes,
            'votes_down' => $downVotes,
            'flags' => $flags,
            'series_suggestions' => $seriesSuggestions,
            'script_suggestions' => $scriptSuggestions,
        ];

        $templating = $this->getContainer()->get('templating');
        $html = $templating->render('ChaosTangentFansubEbooksAppBundle:Activity:email.html.twig', $viewParams);
        $text = $templating->render('ChaosTangentFansubEbooksAppBundle:Activity:email.txt.twig', $viewParams);

        $aws = $this->getContainer()->get('fansubebooks.aws.sdk');
        $ses = $aws->createSes([ 'version' => '2010-12-01' ]);
        $ses->sendEmail([
            'Destination' => [
                'ToAddresses' => [ 'john.noel@chaostangent.com' ],
            ],
            'Message' => [
                'Body' => [
                    'Html' => [
                        'Charset' => 'utf-8',
                        'Data' => $html,
                    ],
                    'Text' => [
                        'Charset' => 'utf-8',
                        'Data' => $text,
                    ],
                ],
                'Subject' => [
                    'Charset' => 'utf-8',
                    'Data' => 'Activity summary for fansubebooks.chaostangent.com',
                ],
            ],
            'Source' => 'fansub_ebooks@chaostangent.com',
        ]);
    }
}
