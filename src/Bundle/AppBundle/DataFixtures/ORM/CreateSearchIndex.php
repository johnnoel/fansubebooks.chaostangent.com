<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Platforms\PostgreSqlPlatform;

/**
 * Create search index
 *
 * Because we can't define our own index statements when creating a table, this
 * kind of fills in the gaps. Kind of janky as it relies on scrying the DB
 * platform
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class CreateSearchIndex implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        if (!($manager instanceof EntityManager)) {
            var_dump(get_class($manager));
            return;
        }

        $conn = $manager->getConnection();
        if ($conn->getDatabasePlatform() instanceof PostgreSqlPlatform) {
            // note the "english", this must be used elsewhere when searching to
            // use the index
            $sql = 'CREATE INDEX lines_idx_ft ON lines USING gin(to_tsvector(\'english\', line))';
            $conn->executeQuery($sql);
        } else {
            var_dump(get_class($conn->getDatabasePlatform()));
        }
    }
}
