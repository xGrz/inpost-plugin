<?php

namespace Xgrz\InPost\Enums;

use Illuminate\Support\Collection;

enum ParcelLockerTemplate: string
{
    case S = 'small';
    case M = 'medium';
    case L = 'large';
    case XL = 'xlarge';

    public function getLabel(): string
    {
        return match ($this) {
            ParcelLockerTemplate::S => __('Paczkomat A'),
            ParcelLockerTemplate::M => __('Paczkomat B'),
            ParcelLockerTemplate::L => __('Paczkomat C'),
            ParcelLockerTemplate::XL => __('Paczkomat D'),
        };
    }

    public function canBeDeliveredToLocker(): bool
    {
        return match ($this) {
            ParcelLockerTemplate::XL => false,
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
            ParcelLockerTemplate::S => [8, 38, 64],
            ParcelLockerTemplate::M => [19, 38, 64],
            ParcelLockerTemplate::L => [41, 38, 64],
            ParcelLockerTemplate::XL => [80, 38, 64],
        };
    }

    public function getLength(): int|float
    {
        return $this->getDimensions()[0];
    }

    public function getWidth(): int|float
    {
        return $this->getDimensions()[1];
    }

    public function getHeight(): int|float
    {
        return $this->getDimensions()[2];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->value,
            'label' => $this->getLabel(),
            'width' => $this->getDimensions()[0],
            'length' => $this->getDimensions()[1],
            'height' => $this->getDimensions()[2],
            'max_weight' => $this->getMaxWeight(),
        ];
    }

    public static function optionsForLocker(): Collection
    {
        return collect(self::cases())
            ->filter(fn(ParcelLockerTemplate $item) => $item->canBeDeliveredToLocker())
            ->values();
    }

    public static function optionsForAddress(): Collection
    {
        return collect(self::cases())
            ->filter(fn(ParcelLockerTemplate $item) => $item->canBeDeliveredToAddress())
            ->values();
    }

    public static function exists(string $name, bool $locker = false, bool $address = false): ParcelLockerTemplate|false
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

}
