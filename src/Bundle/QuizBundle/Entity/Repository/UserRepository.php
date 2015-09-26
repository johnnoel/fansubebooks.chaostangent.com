<?php

namespace ChaosTangent\FansubEbooks\Bundle\QuizBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\UserProviderInterface,
    Symfony\Component\Security\Core\User\UserInterface;
use League\OAuth1\Client\Server\User as OAuthUser;
use ChaosTangent\FansubEbooks\Bundle\QuizBundle\Entity\User;

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
}
