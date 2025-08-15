<?php

namespace Xgrz\InPost\DTOs\Address;

use Xgrz\InPost\Casts\PostCodeCast;
use Xgrz\InPost\Enums\AddressType;

/**
 *
 * @property ?string $company_name    Company name
 * @property ?string $name            Name (divided to first and last name) - virtual field
 * @property ?string $first_name      First name when $name not provided
 * @property ?string $last_name       Last name when $name not provided
 * @property ?string $city            City name
 * @property ?string $post_code       Postal code (formatted 00-001 or numeric (string) 00000)
 * @property ?string $street          Street name
 * @property ?string $building_number House number
 * @property ?string $country_code    Country code (default = PL)
 * @property ?string $email           Email
 * @property ?string $phone           Phone
 */
abstract class BaseAddress
{
    protected AddressType $type;
    protected array $meta = [
        'company_name' => NULL,
        'first_name' => NULL,
        'last_name' => NULL,
        'phone' => NULL,
        'email' => NULL,
    ];
    protected array $address = [
        'city' => NULL,
        'post_code' => NULL,
        'street' => NULL,
        'building_number' => NULL,
        'country_code' => NULL,
    ];

    public function __set(string $name, $value): void
    {
        match ($name) {
            'company_name' => $this->meta['company_name'] = $value,
            'name' => self::divideName($value),
            'first_name' => $this->meta['first_name'] = $value,
            'last_name' => $this->meta['last_name'] = $value,
            'phone' => $this->meta['phone'] = $value,
            'email' => $this->meta['email'] = $value,
            'city' => $this->address['city'] = $value,
            'post_code' => $this->address['post_code'] = PostCodeCast::toNumeric($value),
            'street' => $this->address['street'] = $value,
            'building_number' => $this->address['building_number'] = $value,
            'country_code' => $this->address['country_code'] = $value,
            default => NULL,
        };
    }

    public function __get(string $name): ?string
    {
        return match ($name) {
            'company_name' => $this->meta['company_name'],
            'name' => self::getName(),
            'first_name' => $this->meta['first_name'],
            'last_name' => $this->meta['last_name'],
            'phone' => $this->meta['phone'],
            'email' => $this->meta['email'],
            'city' => $this->address['city'],
            'post_code' => PostCodeCast::format($this->address['post_code']),
            'street' => $this->address['street'],
            'building_number' => $this->address['building_number'],
            'country_code' => $this->address['country_code'],
            default => NULL,
        };
    }

    private function divideName(string $name): void
    {
        $nameArr = str($name)->explode(' ');
        $this->meta['first_name'] = $nameArr->first();
        $this->meta['last_name'] = str($name)->replace($nameArr->first(), '')->trim()->toString();
    }

    private function getName(): string
    {
        $name = join(' ', [
            $this->meta['first_name'],
            $this->meta['last_name'],
        ]);
        return str($name)->trim()->toString();
    }

    public function payload(): array
    {
        $payload = array_filter($this->meta, fn($value) => ! is_null($value));
        $address = array_filter($this->address, fn($value) => ! is_null($value));
        if ($address) {
            if (! isset($address['country_code'])) {
                $address['country_code'] = 'PL'; // fill default country code
            }
            if (isset($address['post_code'])) {
                $address['post_code'] = PostCodeCast::format($address['post_code']);
            }
            $payload['address'] = $address;
        }
        return $payload;
    }

}
