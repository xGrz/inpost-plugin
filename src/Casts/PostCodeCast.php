<?php

namespace Xgrz\InPost\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Xgrz\InPost\Exceptions\ShipXInvalidPostCodeException;

class PostCodeCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        if (empty($value)) return $value;
        if (! is_numeric($value)) return $value;
        if (str($value)->length() != 5) return $value;
        return self::formatToPostCode($value);
    }

    /**
     * @throws ShipXInvalidPostCodeException
     */
    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        if (! self::isPostCode($value)) {
            throw new \Xgrz\InPost\Exceptions\ShipXInvalidPostCodeException('Invalid postal code: [' . $value . ']');
        }
        return self::toNumeric($value);
    }

    private static function stripNonNumeric($value): Stringable
    {
        return str($value)->replaceMatches('/\D+/', '');
    }

    public static function formatToPostCode(?string $value): ?string
    {
        $value = self::stripNonNumeric($value);
        if ($value->isEmpty() || $value->length() != 5) return $value;

        return $value
            ->substr(0, 2)
            ->append('-')
            ->append(str($value)->substr(2, 3));
    }

    public static function isPostCode(?string $value): bool
    {
        $value = self::stripNonNumeric($value);
        if ($value->isEmpty()) return true;
        return $value->length() == 5;
    }


    public static function toNumeric(?string $value): ?string
    {
        if (empty($value)) return $value;
        $value = self::stripNonNumeric($value);
        return $value->isNotEmpty()
            ? $value->toString()
            : null;
    }
}
