<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Activity;

use Doctrine\Common\Persistence\ObjectManager;
use ChaosTangent\FansubEbooks\Entity\Series,
    ChaosTangent\FansubEbooks\Entity\Vote,
    ChaosTangent\FansubEbooks\Entity\Flag,
    ChaosTangent\FansubEbooks\Entity\Suggestion;

/**
 * Activity aggregator
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class Aggregator
{
    /** @var ObjectManager */
    protected $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Get activity for the specified time period
     *
     * @param \DateTime $start
     * @param \DateTime $finish
     * @param integer $count
     * @return array An array of Entry objects
     */
    public function getActivity(\DateTime $start = null, \DateTime $finish = null, $count = 30)
    {
        $activity = [];

        $seriesRepo = $this->om->getRepository(Series::class);
        $series = $seriesRepo->getByAdded($start, $finish, $count);
        foreach ($series as $s) {
            $entry = new Entry();
            $entry->setDateTime($s->getAdded())
                ->setType(Entry::ACTIVITY_SERIES_ADD)
                ->setObject($s);

            $activity[] = $entry;
        }

        $voteRepo = $this->om->getRepository(Vote::class);
        $votes = $voteRepo->getByAdded($start, $finish, $count);
        foreach ($votes as $vote) {
            $type = ($vote->isPositive()) ? Entry::ACTIVITY_VOTE_UP : Entry::ACTIVITY_VOTE_DOWN;

            $entry = new Entry();
            $entry->setDateTime($vote->getAdded())
                ->setType($type)
                ->setObject($vote);

            $activity[] = $entry;
        }

        $flagRepo = $this->om->getRepository(Flag::class);
        $flags = $flagRepo->getByAdded($start, $finish, $count);
        foreach ($flags as $flag) {
            $entry = new Entry();
            $entry->setDateTime($flag->getAdded())
                ->setType(Entry::ACTIVITY_FLAG)
                ->setObject($flag);

            $activity[] = $entry;
        }

        $suggestionRepo = $this->om->getRepository(Suggestion::class);
        $suggestions = $suggestionRepo->getByAdded($start, $finish, $count);
        foreach ($suggestions as $suggestion) {
            $type = ($suggestion->getType() == 'series') ?
                Entry::ACTIVITY_SUGGEST_SERIES : Entry::ACTIVITY_SUGGEST_SCRIPT;

            $entry = new Entry();
            $entry->setDateTime($suggestion->getAdded())
                ->setType($type)
                ->setObject($suggestion);

            $activity[] = $entry;
        }

        usort($activity, function(Entry $a, Entry $b) {
            return ($a == $b) ? 0 : ($a > $b) ? -1 : 1;
        });

        if (count($activity) > $count) {
            $activity = array_slice($activity, 0, $count);
        }

        return $activity;
    }
}
