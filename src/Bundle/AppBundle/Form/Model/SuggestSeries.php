<?php

namespace ChaosTangent\FansubEbooks\Bundle\AppBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Suggest series form model
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class SuggestSeries implements \Serializable
{
    /**
     * @Assert\NotBlank
     */
    public $name;
    /**
     * @Assert\NotBlank
     */
    public $group;

    public function serialize()
    {
        return serialize([
            'name' => $this->name,
            'group' => $this->group,
        ]);
    }

    public function unserialize($serialized)
    {
        $unserialized = unserialize($serialized);
        $this->name = $unserialized['name'];
        $this->group = $unserialized['group'];
    }
}
