<?php

declare(strict_types=1);

namespace App\Services\Payment\Gateways;

use App\Contracts\PaymentGatewayInterface;

class PayPalGateway implements PaymentGatewayInterface
{
    /**
     * @param string $credential
     * @param int $amountInCents
     * @return array{status: string, transaction_id: string}
     */
    public function charge(string $credential, int $amountInCents): array
    {
        return [
            'status' => 'approved',
            'transaction_id' => 'pp_' . substr(md5($credential . ':' . $amountInCents), 0, 12),
        ];
    }
}