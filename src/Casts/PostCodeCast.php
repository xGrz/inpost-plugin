<?php

namespace Xgrz\InPost\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Xgrz\InPost\Exceptions\ShipXInvalidPostCodeException;

class PostCodeCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (empty($value)) return $value;
        if (! is_numeric($value)) return $value;
        if (str($value)->length() != 5) return $value;

        return self::format($value);
    }

    /**
     * @throws ShipXInvalidPostCodeException
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (! self::isPostCode($value)) {
            throw new ShipXInvalidPostCodeException('Invalid postal code: [' . $value . ']');
        }
        return self::toNumeric($value);
    }

    public static function isPostCode(?string $value): bool
    {
        if (empty($value)) return false;
        return Str::of($value)->replaceMatches('/\D+/', '')->length() == 5;
    }

    public static function toNumeric(string $value): ?string
    {
        $numericValue = str($value)->replaceMatches('/\D+/', '');
        return $numericValue->length() == 5
            ? $numericValue->toString()
            : NULL;
    }

    public static function format(string $value): ?string
    {
        if (! self::isPostCode($value)) {
            return NULL;
        }

        $value = self::toNumeric($value);

        return str(self::toNumeric($value))
            ->substr(0, 2)
            ->append('-')
            ->append(str($value)->substr(2, 3));
    }
}
