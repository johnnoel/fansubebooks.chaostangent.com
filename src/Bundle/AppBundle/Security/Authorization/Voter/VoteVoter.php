<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\HttpFoundation\Request;
use ChaosTangent\FansubEbooks\Entity\Repository\VoteRepository;

/**
 * Vote voter
 *
 * Restricts access to voting if an IP address has voted x number of times over
 * a period of time
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class VoteVoter extends AbstractVoter
{
    const PER_HOUR = 450;

    /** @var VoteRepository */
    protected $voteRepo;

    public function __construct(VoteRepository $voteRepo)
    {
        $this->voteRepo = $voteRepo;
    }

    protected function getSupportedClasses()
    {
        return [ Request::class ];
    }

    protected function getSupportedAttributes()
    {
        return [ 'voteup', 'votedown' ];
    }

    protected function isGranted($attribute, $object, $user = null)
    {
        if (!($object instanceof Request)) {
            return false;
        }

        $ip = $object->getClientIp();
        $hourAgo = new \DateTime('now');
        $hourAgo->sub(new \DateInterval('PT1H'));

        $ipCount = $this->voteRepo->getVoteCount([
            'ip' => $ip,
            'start' => $hourAgo,
        ]);

        if ($ipCount >= self::PER_HOUR) {
            return false;
        }

        return true;
    }
}
