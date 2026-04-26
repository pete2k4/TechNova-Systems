<?php

declare(strict_types=1);

namespace App\Services\Payment\Gateways;

use App\Contracts\PaymentGatewayInterface;

class FastPayGateway implements PaymentGatewayInterface
{
    /**
     * @param string $customerToken
     * @param int $amountInCents
     * @return array{status: string, transaction_id: string}
     */
    public function charge(string $customerToken, int $amountInCents): array
    {
        return [
            'status' => 'approved',
            'transaction_id' => 'fp_' . substr(md5($customerToken . ':' . $amountInCents), 0, 12),
        ];
    }
}
