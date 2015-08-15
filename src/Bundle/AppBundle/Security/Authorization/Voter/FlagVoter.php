<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use ChaosTangent\FansubEbooks\Entity\Flag;
use ChaosTangent\FansubEbooks\Entity\Repository\FlagRepository;

/**
 * Flag voter
 *
 * Restrict access to flagging a line if an IP address has flagged x number of
 * times over a period or has already flagged that line.
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class FlagVoter extends AbstractVoter
{
    /**
     * Can flag this many times within an hour
     */
    const FLAGS_PER_HOUR = 50;

    /** @var FlagRepository */
    protected $flagRepo;

    public function __construct(FlagRepository $flagRepo)
    {
        $this->flagRepo = $flagRepo;
    }

    protected function getSupportedClasses()
    {
        return [ Flag::class ];
    }

    protected function getSupportedAttributes()
    {
        return [ 'flag' ];
    }

    protected function isGranted($attribute, $object, $user = null)
    {
        if (!($object instanceof Flag)) {
            return false;
        }

        $ip = $object->getIp();
        $line = $object->getLine();

        // if you've flagged this line before, stop
        $flagCount = $this->flagRepo->getFlagCount([
            'ip' => $ip,
            'line' => $line,
        ]);

        if ($flagCount > 0) {
            return false;
        }

        $hourAgo = new \DateTime('now');
        $hourAgo->sub(new \DateInterval('PT1H'));

        $flagCount = $this->flagRepo->getFlagCount([
            'ip' => $ip,
            'start' => $hourAgo,
        ]);

        if ($flagCount >= self::FLAGS_PER_HOUR) {
            return false;
        }

        return true;
    }
}
