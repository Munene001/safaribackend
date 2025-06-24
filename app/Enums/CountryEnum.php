<?php declare (strict_types = 1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class CountryEnum extends Enum
{
    public const KENYA = 1;
    public const TANZANIA = 2;
    public const ZANZIBAR = 3;
    public const RWANDA = 4;
    public const BOTSWANA = 5;
    public const SOUTH_AFRICA = 6;
    public const MALDIVES = 7;
    public const SEYCHELLES = 8;
    public const NAMIBIA = 9;
    public const MAURITIUS = 10;
    public const MOZAMBIQUE = 11;

    public function label()
    {
        return match ($this) {
            self::KENYA => 'Kenya',
            self::TANZANIA => 'Tanzania',
            self::ZANZIBAR => 'Zanzibar',
            self::RWANDA => 'Rwanda',
            self::MOZAMBIQUE => 'Mozambique',
            self::BOTSWANA => 'Botswana',
            self::SOUTH_AFRICA => 'South Africa',
            self::SEYCHELLES => 'Seychelles',
            self::MAURITIUS => 'Mauritius',
            self::NAMIBIA => 'Namibia',
            self::MALDIVES => 'Maldives'

        };
    }
    public static function options()
    {
        return array_reduce(self::cases(), function ($carry, $case) {
            $carry[$case->value] = $case->label();
            return $carry;

        }, []);
    }
    public static function values()
    {
        return array_column(self::cases(), 'value');
    }

}
