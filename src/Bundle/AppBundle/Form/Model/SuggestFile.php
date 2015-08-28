<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use ChaosTangent\FansubEbooks\Validator\Constraint\ASSFile as AssertASSFile;

/**
 * Suggest file form model
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class SuggestFile implements \Serializable
{
    /**
     * @Assert\File(maxSize="2m")
     * @Assert\NotBlank(message="You must provide a file")
     * @AssertASSFile
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
        $this->file = new \SplFileInfo($unserialized['file']);
        $this->uploadedFilename = $unserialized['uploaded_filename'];
    }
}
