<?php

declare(strict_types=1);

namespace Tests\Feature\Checkout;

use Tests\TestCase;

class PaymentPlaceholderPageTest extends TestCase
{
    public function test_payment_placeholder_page_is_accessible(): void
    {
        $response = $this->get('/checkout/payment-placeholder/123');

        $response->assertOk();
        $response->assertSee('Still working on payment integration');
        $response->assertSee('Order Ref: 123');
    }
}
