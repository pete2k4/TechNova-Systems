<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * Bridge implementor contract for payment gateways.
 */
interface PaymentGatewayInterface
{
    /**
     * Charge a credential-based payment source.
     *
     * @param string $credential Card number, email, token, or provider-specific identifier
     * @param int $amountInCents Amount to charge in minor currency units
     * @return array{status: string, transaction_id: string}
     */
    public function charge(string $credential, int $amountInCents): array;
}