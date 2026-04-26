<?php

declare(strict_types=1);

namespace App\Services\Discount;

use App\Contracts\DiscountInterface;

/**
 * Composite Discount
 *
 * Composite pattern: holds a collection of DiscountInterface objects (leaves or
 * other composites) and treats the whole tree as a single DiscountInterface.
 *
 * Discounts are applied sequentially — each one operates on the price that
 * remains after the previous discount was deducted.  The total amount returned
 * is capped at the original price so the caller never ends up with a negative
 * final price.
 *
 * Example: 10 % off then $20 flat off a $150 item
 *   step 1: 10% of $150 = $15  →  remaining $135
 *   step 2: $20 of $135 = $20  →  remaining $115
 *   total discount returned: $35
 */
class CompositeDiscount implements DiscountInterface
{
    /** @var DiscountInterface[] */
    private array $discounts = [];

    /**
     * Add a discount leaf or another composite to this group.
     */
    public function add(DiscountInterface $discount): static
    {
        $this->discounts[] = $discount;
        return $this;
    }

    /**
     * Remove a discount from this group (by identity).
     */
    public function remove(DiscountInterface $discount): void
    {
        $this->discounts = array_filter(
            $this->discounts,
            fn (DiscountInterface $d) => $d !== $discount
        );
        $this->discounts = array_values($this->discounts);
    }

    /**
     * @return DiscountInterface[]
     */
    public function getDiscounts(): array
    {
        return $this->discounts;
    }

    /**
     * @param float $price
     * @return float The total discount amount (summed across all children,
     *               applied sequentially to the running remainder).
     */
    public function calculate(float $price): float
    {
        $totalDiscount = 0.0;
        $remaining = $price;

        foreach ($this->discounts as $discount) {
            $amount = $discount->calculate($remaining);
            $totalDiscount += $amount;
            $remaining = max(0.0, $remaining - $amount);
        }

        return min($totalDiscount, $price);
    }
}
