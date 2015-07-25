<?php

namespace ChaosTangent\FansubEbooks\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Advanced SubStation Alpha constraint
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 * @Annotation
 */
class ASSFile extends Constraint
{
    public $message = 'Doesn\'t look like this is a .ass (Advanced SubStation Alpha) file';
}
