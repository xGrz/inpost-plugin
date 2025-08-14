<?php

namespace Xgrz\InPost\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Xgrz\InPost\Exceptions\ShipXInvalidPostCodeException;

class PostCodeCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (empty($value)) return $value;
        if (! is_numeric($value)) return $value;
        if (str($value)->length() != 5) return $value;

        return str($value)
            ->substr(0, 2)
            ->append('-')
            ->append(str($value)->substr(2, 3));
    }

    /**
     * @throws ShipXInvalidPostCodeException
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        $formattedValue = str($value)->replaceMatches('/\D+/', '');
        if ($formattedValue->length() != 5) {
            throw new ShipXInvalidPostCodeException('Invalid postal code: [' . $value . ']');
        }
        return $formattedValue->toString();
    }
}
