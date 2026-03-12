<?php

declare(strict_types=1);

namespace App\Factories;

use App\Contracts\DiscountInterface;
use App\Services\Discount\CompositeDiscount;
use App\Services\Discount\FixedAmountDiscount;
use App\Services\Discount\PercentageDiscount;
use InvalidArgumentException;

class DiscountFactory
{
    public const FIXED_AMOUNT = 'fixed';
    public const PERCENTAGE = 'percentage';

    /**
     * @param string $type
     * @param float $value
     * @return DiscountInterface
     * @throws InvalidArgumentException
     */
    public static function create(string $type, float $value): DiscountInterface
    {
        if ($value < 0) {
            throw new InvalidArgumentException('Discount value cannot be negative');
        }

        return match (strtolower($type)) {
            self::FIXED_AMOUNT => self::createFixedAmountDiscount($value),
            self::PERCENTAGE => self::createPercentageDiscount($value),
            default => throw new InvalidArgumentException("Unknown discount type: {$type}"),
        };
    }

    /**
     * @param float $amount
     * @return FixedAmountDiscount
     */
    public static function createFixedAmountDiscount(float $amount): FixedAmountDiscount
    {
        return new FixedAmountDiscount($amount);
    }

    /**
     * @param float $percentage
     * @return PercentageDiscount
     * @throws InvalidArgumentException
     */
    public static function createPercentageDiscount(float $percentage): PercentageDiscount
    {
        if ($percentage < 0 || $percentage > 100) {
            throw new InvalidArgumentException('Percentage discount must be between 0 and 100');
        }

        return new PercentageDiscount($percentage);
    }

    /**
     * @param array $config
     * @return DiscountInterface
     * @throws InvalidArgumentException
     */
    public static function fromConfig(array $config): DiscountInterface
    {
        $type = $config['type'] ?? throw new InvalidArgumentException('Missing discount type');
        $value = $config['value'] ?? throw new InvalidArgumentException('Missing discount value');

        return self::create($type, (float) $value);
    }

    /**
     * Build a CompositeDiscount from an array of individual discount configs.
     *
     * Each element must have 'type' and 'value' keys (same shape as fromConfig).
     *
     * @param array<int, array{type: string, value: float}> $configs
     * @return CompositeDiscount
     * @throws InvalidArgumentException
     */
    public static function createComposite(array $configs): CompositeDiscount
    {
        if (empty($configs)) {
            throw new InvalidArgumentException('Composite discount requires at least one discount');
        }

        $composite = new CompositeDiscount();

        foreach ($configs as $config) {
            $composite->add(self::fromConfig($config));
        }

        return $composite;
    }
}
