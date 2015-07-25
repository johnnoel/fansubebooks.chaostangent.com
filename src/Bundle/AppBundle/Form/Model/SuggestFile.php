<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Suggest file form model
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class SuggestFile implements \Serializable
{
    /**
     * @Assert\File(maxSize="2mb")
     * @Assert\NotBlank
     */
    public $file;
    public $uploadedFilename;

    public function serialize()
    {
        return serialize([
            'file' => $this->file->getPathname(),
            'uploaded_filename' => $this->uploadedFilename,
        ]);
    }

    public function unserialize($serialized)
    {
        $unserialized = unserialize($serialized);
        $this->file = new \SplFileInfo($serialized['file']);
        $this->uploadedFilename = $serialized['uploaded_filename'];
    }
}
