<?php

namespace Xgrz\InPost\Enums;

use Illuminate\Support\Collection;
use Xgrz\InPost\Interfaces\ParcelInterface;

enum InPostParcelLocker: string implements ParcelInterface
{
    case S = 'small';
    case M = 'medium';
    case L = 'large';
    case XL = 'xlarge';

    public function getLabel(): string
    {
        return match ($this) {
            InPostParcelLocker::S => __('Paczkomat A'),
            InPostParcelLocker::M => __('Paczkomat B'),
            InPostParcelLocker::L => __('Paczkomat C'),
            InPostParcelLocker::XL => __('Paczkomat D'),
        };
    }

    public function canBeDeliveredToLocker(): bool
    {
        return match ($this) {
            InPostParcelLocker::XL => false,
            default => true,
        };
    }

    public function canBeDeliveredToAddress(): bool
    {
        return true;
    }

    public function getMaxWeight(): int|float
    {
        return 25;
    }

    public function getDimensions(): array
    {
        return match ($this) {
            InPostParcelLocker::S => [8, 38, 64],
            InPostParcelLocker::M => [19, 38, 64],
            InPostParcelLocker::L => [41, 38, 64],
            InPostParcelLocker::XL => [80, 38, 64],
        };
    }

    public function getLength(): int|float
    {
        return $this->getDimensions()[1];
    }

    public function getWidth(): int|float
    {
        return $this->getDimensions()[0];
    }

    public function getHeight(): int|float
    {
        return $this->getDimensions()[2];
    }

    public function toArray(): array
    {
        return [
            'template' => $this->value,
            'width' => $this->getDimensions()[0],
            'length' => $this->getDimensions()[1],
            'height' => $this->getDimensions()[2],
            'weight' => $this->getMaxWeight(),
            'non_standard' => false,
            'quantity' => 1,
        ];
    }

    public function getQuantity(): int
    {
        return 1;
    }


    public static function exists(string $name, bool $locker = false, bool $address = false): InPostParcelLocker|false
    {
        try {
            $template = self::tryFrom($name);
            if (empty($template)) return false;
            if ($locker && $template->canBeDeliveredToLocker()) return $template;
            if ($address && $template->canBeDeliveredToAddress()) return $template;
            if (! $locker && ! $address) return $template;

            return false;
        } catch (\ValueError) {
            return false;
        }
    }

    public function payload(bool $forLocker = true): mixed
    {
        return $forLocker
            ? ['template' => $this->value]
            : [
                'dimensions' => [
                    'length' => $this->getLength() * 10,
                    'height' => $this->getHeight() * 10,
                    'width' => $this->getWidth() * 10,
                    'unit' => 'mm',
                ],
                'weight' => [
                    'amount' => $this->getMaxWeight(),
                    'unit' => 'kg',
                ],
                'non_standard' => false,
            ];
    }


    public static function optionsForLocker(): Collection
    {
        return collect(self::cases())
            ->filter(fn(InPostParcelLocker $item) => $item->canBeDeliveredToLocker())
            ->values();
    }

    public static function optionsForAddress(): Collection
    {
        return collect(self::cases())
            ->filter(fn(InPostParcelLocker $item) => $item->canBeDeliveredToAddress())
            ->values();
    }

}
