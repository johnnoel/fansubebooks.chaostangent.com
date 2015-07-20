<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Suggest file form model
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class SuggestFile
{
    /**
     * @Assert\File(maxSize="512k")
     * @Assert\NotBlank
     */
    public $file;
}
