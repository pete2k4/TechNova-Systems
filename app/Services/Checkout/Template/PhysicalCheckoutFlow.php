<?php

declare(strict_types=1);

namespace App\Services\Checkout\Template;

class PhysicalCheckoutFlow extends BaseCheckoutFlow
{
    protected function productType(): string
    {
        return 'physical';
    }
}
