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
}
