<?php

namespace ChaosTangent\FansubEbooks\Validator\Constraint;

use Symfony\Component\Validator\Constraint,
    Symfony\Component\Validator\ConstraintValidator;

/**
 * Advanced SubStation Alpha constraint validator
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class ASSFileValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!($value instanceof \SplFileInfo)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();

            return;
        }

        // todo integration php-ass here
        $fh = fopen($value->getPathname(), 'r');
        // read the first 13 bytes and...
        $firstFewBytes = trim(fgets($fh, 128));

        // detect bom
        if (substr($firstFewBytes, 0, 3) == (chr(0xEF).chr(0xBB).chr(0xBF))) {
            // strip it off, no need to decode as we're not reading anything
            // that's not ISO-8859-1
            $firstFewBytes = substr($firstFewBytes, 3);
        }

        // check it equals [Script Info]
        if ($firstFewBytes === false || $firstFewBytes != '[Script Info]') {
            var_dump(unpack('H*', $firstFewBytes), unpack('H*', '[Script Info]'));
            $this->context->buildViolation($firstFewBytes)
                ->addViolation();

            return;
        }
    }
}
