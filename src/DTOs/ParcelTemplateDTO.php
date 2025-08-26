<?php

namespace Xgrz\InPost\DTOs;

use Xgrz\InPost\Enums\ParcelLockerTemplate;
use Xgrz\InPost\Models\ParcelTemplate;

readonly class ParcelTemplateDTO
{
    public string $label; // label for display in options
    public string $name; // origin name
    public ?string $template; // template name for standard parcels
    public int $width;
    public int $height;
    public int $length;
    public float $weight;
    public bool $non_standard;
    public ParcelTemplate|ParcelLockerTemplate $source;

    private function __construct(ParcelTemplate|ParcelLockerTemplate $template)
    {
        $this->source = $template;

        $reflection = new \ReflectionClass($template);
        if ($reflection->isEnum()) {
            $this->non_standard = false;
            $this->name = $template->getLabel();
            foreach ($template->toArray() as $key => $value) {
                $this->{$key} = $value;
            }
        } else {
            $this->template = NULL;
            foreach ($template->only(['name', 'width', 'height', 'length', 'weight', 'non_standard']) as $key => $value) {
                $this->{$key} = $value;
            }
        }

        $this->label = $this->name . ' (' . $this->width . 'x' . $this->height . 'x' . $this->length . ' ' . $this->weight . 'kg)';
    }

    public static function make(ParcelTemplate|ParcelLockerTemplate $template): static
    {
        return new static($template);
    }
}