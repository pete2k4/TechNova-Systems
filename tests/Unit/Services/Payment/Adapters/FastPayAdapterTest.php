<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Payment\Adapters;

use App\Contracts\PaymentMethodInterface;
use App\Services\Payment\Adapters\FastPayAdapter;
use App\Services\Payment\Gateways\FastPayGateway;
use PHPUnit\Framework\TestCase;

class FastPayAdapterTest extends TestCase
{
    /**
     * Test adapter maps app contract to gateway API
     */
    public function testAdapterProcessesGatewayChargeInCents(): void
    {
        $gateway = new class extends FastPayGateway
        {
            public ?string $capturedToken = null;
            public ?int $capturedAmount = null;

            public function charge(string $customerToken, int $amountInCents): array
            {
                $this->capturedToken = $customerToken;
                $this->capturedAmount = $amountInCents;

                return [
                    'status' => 'approved',
                    'transaction_id' => 'fp_test_1234',
                ];
            }
        };

        $adapter = new FastPayAdapter($gateway, 'fp_customer_12345678');

        $this->assertInstanceOf(PaymentMethodInterface::class, $adapter);
        $this->assertTrue($adapter->process(149.99));
        $this->assertSame('fp_customer_12345678', $gateway->capturedToken);
        $this->assertSame(14999, $gateway->capturedAmount);
    }

    /**
     * Test adapter returns false when gateway rejects payment
     */
    public function testAdapterReturnsFalseForRejectedGatewayResponse(): void
    {
        $gateway = new class extends FastPayGateway
        {
            public function charge(string $customerToken, int $amountInCents): array
            {
                return [
                    'status' => 'rejected',
                    'transaction_id' => 'fp_test_5678',
                ];
            }
        };

        $adapter = new FastPayAdapter($gateway, 'fp_customer_12345678');

        $this->assertFalse($adapter->process(25.50));
        $this->assertSame('FastPay', $adapter->getName());
    }
}
