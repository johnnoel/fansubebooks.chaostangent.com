<?php

namespace ChaosTangent\FansubEbooks\Extension\Doctrine\DBAL;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException,
    Doctrine\DBAL\Types\DateTimeType;

/**
 * Doctrine DBAL UTC datetime type
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */
class UTCDateTimeType extends DateTimeType
{
    static private $utc = null;

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!($value instanceof \DateTime)) {
            return null;
        }

        if (self::$utc === null) {
            self::$utc = new \DateTimeZone('UTC');
        }

        $value->setTimeZone(self::$utc);

        return $value->format($platform->getDateTimeFormatString());
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        if (self::$utc === null) {
            self::$utc = new \DateTimeZone('UTC');
        }

        $val = \DateTime::createFromFormat($platform->getDateTimeFormatString(), $value, self::$utc);

        if (!$val) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        return $val;
    }
}
