<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class PriceCast implements CastsAttributes
{
    /** The amount of digits. */
    protected int $digits;

    /** @throws \InvalidArgumentException Thrown on invalid input. */
    public function __construct(int $digits = 2)
    {
        if ($digits < 1) {
            throw new \InvalidArgumentException('Incorrect digits number. Expected grated then zero');
        }

        $this->digits = $digits;
    }

    /**
     * Transform the attribute from the underlying model values.
     *
     * @param Model $model The model object.
     * @param string $key The property name.
     * @param mixed $value The property value.
     * @param array $attributes The model attributes array.
     * @return float
     */
    public function get($model, string $key, $value, array $attributes): ?float
    {
        return $value !== null
            ? round($value / (10 ** $this->digits), $this->digits)
            : null;
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @param Model $model The model object.
     * @param string $key The property name.
     * @param mixed $value The property value.
     * @param array $attributes The model attributes array.
     * @return int
     */
    public function set($model, string $key, $value, array $attributes): int
    {
        return (int)($value * (10 ** $this->digits));
    }
}
