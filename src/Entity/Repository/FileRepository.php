<?php

namespace ChaosTangent\FansubEbooks\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * File entity repository
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class FileRepository extends EntityRepository
{
    /**
     * Get a file with lines pre-populated in it
     *
     * @param integer $id The file ID
     * @return File
     */
    public function getFile($id)
    {
        $file = $this->find($id);

        if ($file === null) {
            return $file;
        }

        $lineRepo = $this->_em->getRepository('Entity:Line');
        $lines = $lineRepo->getLinesByFile($file);

        $file->setLines($lines);

        return $file;
    }

    /**
     * Check whether a file with the supplied name already exists
     *
     * @param string $name The filename
     * @return boolean
     */
    public function fileWithNameExists($name)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select([ 'COUNT(f.id)' ])
            ->from('Entity:File', 'f')
            ->where($qb->expr()->eq('f.name', ':name'))
            ->setParameter('name', $name);

        $count = $qb->getQuery()->getSingleScalarResult();

        return (intval($count) > 0);
    }

    /**
     * Check whether a file with the supplied hash already exists
     *
     * @param string $hash The SHA256 hash to check
     * @return boolean
     */
    public function fileWithHashExists($hash)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select([ 'COUNT(f.id)' ])
            ->from('Entity:File', 'f')
            ->where($qb->expr()->eq('f.hash', ':hash'))
            ->setParameter('hash', $hash);

        $count = $qb->getQuery()->getSingleScalarResult();

        return (intval($count) > 0);
    }
}
