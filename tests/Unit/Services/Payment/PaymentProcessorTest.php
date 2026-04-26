<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Payment;

use App\Services\Payment\PaymentProcessor;
use PHPUnit\Framework\TestCase;

class PaymentProcessorTest extends TestCase
{
    /**
     * Test processor can use adapter-backed payment methods via the factory
     */
    public function testProcessPaymentByTypeWithFastPayAdapter(): void
    {
        $processor = new PaymentProcessor();

        $success = $processor->processPaymentByType('fast_pay', 'fp_customer_12345678', 75.25);

        $this->assertTrue($success);
    }
}
