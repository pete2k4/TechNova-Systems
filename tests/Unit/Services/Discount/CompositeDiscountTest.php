<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Discount;

use App\Contracts\DiscountInterface;
use App\Services\Discount\CompositeDiscount;
use App\Services\Discount\FixedAmountDiscount;
use App\Services\Discount\PercentageDiscount;
use PHPUnit\Framework\TestCase;

class CompositeDiscountTest extends TestCase
{
    /**
     * Composite implements the same interface as its leaf children.
     */
    public function testImplementsDiscountInterface(): void
    {
        $this->assertInstanceOf(DiscountInterface::class, new CompositeDiscount());
    }

    /**
     * A composite with a single leaf behaves identically to that leaf.
     */
    public function testSingleLeafBehavesLikeThatLeaf(): void
    {
        $composite = (new CompositeDiscount())->add(new FixedAmountDiscount(20.0));

        $this->assertSame(20.0, $composite->calculate(100.0));
    }

    /**
     * Two fixed discounts are summed sequentially:
     * $15 off → remaining $85, then $10 off $85 → total $25 off.
     */
    public function testMultipleFixedDiscountsAreAddedSequentially(): void
    {
        $composite = (new CompositeDiscount())
            ->add(new FixedAmountDiscount(15.0))
            ->add(new FixedAmountDiscount(10.0));

        $this->assertSame(25.0, $composite->calculate(100.0));
    }

    /**
     * Percentage then fixed: sequential application gives different result than
     * applying both to the original price.
     *
     * price=$200, 10%=$20 → remaining=$180, then $30 off $180=$30 → total=$50
     */
    public function testPercentageThenFixedAppliedSequentially(): void
    {
        $composite = (new CompositeDiscount())
            ->add(new PercentageDiscount(10.0))
            ->add(new FixedAmountDiscount(30.0));

        $this->assertEqualsWithDelta(50.0, $composite->calculate(200.0), 0.001);
    }

    /**
     * Total discount is capped at the original price — can never exceed it.
     */
    public function testTotalDiscountCappedAtOriginalPrice(): void
    {
        $composite = (new CompositeDiscount())
            ->add(new FixedAmountDiscount(200.0))
            ->add(new FixedAmountDiscount(200.0));

        // discount cannot exceed $50
        $this->assertSame(50.0, $composite->calculate(50.0));
    }

    /**
     * An empty composite applies zero discount.
     */
    public function testEmptyCompositeAppliesZeroDiscount(): void
    {
        $this->assertSame(0.0, (new CompositeDiscount())->calculate(100.0));
    }

    /**
     * Composite can be nested inside another composite (tree structure).
     *
     * inner: 10% of $100 = $10, remaining $90, $5 off = $5  → inner returns $15
     * outer: inner($15) + $8 flat off remaining $85 = $8  → total = $23
     */
    public function testNestedCompositeTreeIsHandledUniformly(): void
    {
        $inner = (new CompositeDiscount())
            ->add(new PercentageDiscount(10.0))
            ->add(new FixedAmountDiscount(5.0));

        $outer = (new CompositeDiscount())
            ->add($inner)
            ->add(new FixedAmountDiscount(8.0));

        $this->assertEqualsWithDelta(23.0, $outer->calculate(100.0), 0.001);
    }

    /**
     * remove() eliminates a previously added leaf by identity.
     */
    public function testRemoveDropsLeafByIdentity(): void
    {
        $leaf = new FixedAmountDiscount(50.0);

        $composite = (new CompositeDiscount())
            ->add($leaf)
            ->add(new FixedAmountDiscount(10.0));

        $composite->remove($leaf);

        $this->assertCount(1, $composite->getDiscounts());
        $this->assertSame(10.0, $composite->calculate(100.0));
    }

    /**
     * getDiscounts() returns all added children.
     */
    public function testGetDiscountsReturnsAllChildren(): void
    {
        $a = new FixedAmountDiscount(5.0);
        $b = new PercentageDiscount(10.0);

        $composite = (new CompositeDiscount())->add($a)->add($b);

        $this->assertSame([$a, $b], $composite->getDiscounts());
    }

    /**
     * PriceCalculator::applyDiscount works with composite without any changes
     * — proves uniform treatment of leaves and composites from the outside.
     */
    public function testCallerNeedNotKnowAboutCompositeStructure(): void
    {
        $composite = (new CompositeDiscount())
            ->add(new PercentageDiscount(20.0))   // 20% of $100 = $20
            ->add(new FixedAmountDiscount(5.0));  // $5 of remaining $80 = $5

        // Any code that calls calculate($price) works identically with a leaf
        // or a composite — no type-check needed.
        $discountAmount = $composite->calculate(100.0);
        $finalPrice = max(0.0, 100.0 - $discountAmount);

        $this->assertEqualsWithDelta(75.0, $finalPrice, 0.001);
    }
}
