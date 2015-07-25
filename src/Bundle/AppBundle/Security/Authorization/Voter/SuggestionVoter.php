<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use ChaosTangent\FansubEbooks\Entity\Suggestion;
use ChaosTangent\FansubEbooks\Entity\Repository\SuggestionRepository;

/**
 * Suggestion voter
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class SuggestionVoter extends AbstractVoter
{
    /**
     * Can suggest this many times within an hour
     */
    const SUGGESTIONS_PER_HOUR = 10;

    /** @var SuggestionRepository */
    protected $suggestionRepo;

    public function __construct(SuggestionRepository $suggestionRepo)
    {
        $this->suggestionRepo = $suggestionRepo;
    }

    protected function getSupportedClasses()
    {
        return [ Suggestion::class ];
    }

    protected function getSupportedAttributes()
    {
        return [ 'suggest' ];
    }

    protected function isGranted($attribute, $object, $user = null)
    {
        if (!($object instanceof Suggestion)) {
            return false;
        }

        $ip = $object->getIp();

        $hourAgo = new \DateTime('now');
        $hourAgo->sub(new \DateInterval('PT1H'));

        $suggestionCount = $this->suggestionRepo->getSuggestionCount([
            'ip' => $ip,
            'start' => $hourAgo,
        ]);

        if ($suggestionCount >= self::SUGGESTIONS_PER_HOUR) {
            return false;
        }

        return true;
    }
}
