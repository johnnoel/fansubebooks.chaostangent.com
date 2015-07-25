<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use ChaosTangent\FansubEbooks\Entity\Vote;
use ChaosTangent\FansubEbooks\Entity\Repository\VoteRepository;

/**
 * Vote voter
 *
 * Restricts access to voting if an IP address has voted x number of times over
 * a period of time (for anything) or has voted for the same line within a
 * period of time
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class VoteVoter extends AbstractVoter
{
    /**
     * Can vote this many times within an hour
     */
    const VOTES_PER_HOUR = 450;
    /**
     * Must wait this many seconds before voting for the same line
     */
    const TIME_PER_LINE = 14400; // four hours

    /** @var VoteRepository */
    protected $voteRepo;

    public function __construct(VoteRepository $voteRepo)
    {
        $this->voteRepo = $voteRepo;
    }

    protected function getSupportedClasses()
    {
        return [ Vote::class ];
    }

    protected function getSupportedAttributes()
    {
        return [ 'voteup', 'votedown' ];
    }

    protected function isGranted($attribute, $object, $user = null)
    {
        if (!($object instanceof Vote)) {
            return false;
        }

        $ip = $object->getIp();
        $line = $object->getLine();

        $perLineStart = new \DateTime('now');
        $perLineStart->sub(new \DateInterval('PT'.self::TIME_PER_LINE.'S'));

        $voteCount = $this->voteRepo->getVoteCount([
            'ip' => $ip,
            'line' => $line,
            'start' => $perLineStart,
        ]);

        if ($voteCount > 0) {
            return false;
        }

        $hourAgo = new \DateTime('now');
        $hourAgo->sub(new \DateInterval('PT1H'));

        $voteCount = $this->voteRepo->getVoteCount([
            'ip' => $ip,
            'start' => $hourAgo,
        ]);

        if ($voteCount >= self::VOTES_PER_HOUR) {
            return false;
        }

        return true;
    }
}
