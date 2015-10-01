<?php

namespace ChaosTangent\FansubEbooks\Bundle\QuizBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\UserProviderInterface,
    Symfony\Component\Security\Core\User\UserInterface;
use League\OAuth1\Client\Server\User as OAuthUser;
use ChaosTangent\FansubEbooks\Bundle\QuizBundle\Entity\User,
    ChaosTangent\FansubEbooks\Bundle\QuizBundle\Entity\Answer;

/**
 * User repository
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class UserRepository extends EntityRepository implements UserProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($username)
    {
        // ohoho, $user is actually an object, SURPRISE
        if (!($username instanceof OAuthUser)) {
            throw new UsernameNotFoundException(
                sprintf(
                    'loadUserByUsername expects $username to be an instance of "%s"',
                    OAuthUser::class
                )
            );
        }

        $user = $this->findOneBy([ 'twitterId' => $username->uid ]);

        if ($user === null) {
            // create our user
            $user = new User();
            $user->setTwitterId($username->uid)
                ->setDisplayName($username->nickname)
                ->setAvatar($username->imageUrl);

            $this->_em->persist($user);
            $this->_em->flush();
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    $class
                )
            );
        }

        return $this->find($user->getId());
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return $class == User::class || is_subclass_of($class, User::class);
    }

    /**
     * Populate the user scores
     *
     * @param User $user
     */
    public function hydrateUserScores(User $user)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select([
            'SUM(CASE WHEN a.correct = true AND a.answer IS NOT NULL THEN 1 ELSE 0 END) AS correct',
            'SUM(CASE WHEN a.correct = false AND a.answer IS NOT NULL THEN 1 ELSE 0 END) AS incorrect',
            'SUM(CASE WHEN a.skipped = true THEN 1 ELSE 0 END) AS skipped'
        ])->from(Answer::class, 'a')
            ->where($qb->expr()->eq('a.user', ':user'))
            ->groupBy('a.user')
            ->setParameter('user', $user);

        $res = $qb->getQuery()->getOneOrNullResult();

        if ($res === null) {
            return;
        }

        $user->setCorrect(intval($res['correct']))
            ->setIncorrect(intval($res['incorrect']))
            ->setSkipped(intval($res['skipped']));
    }
}
