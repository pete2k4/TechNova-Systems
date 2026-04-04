<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Services\Payment\Bridge\AbstractPaymentMethod;
use App\Services\Payment\Gateways\PayPalGateway;

class PayPalPayment extends AbstractPaymentMethod
{
    /**
     * @param string $email
     */
    public function __construct(
        private readonly string $email
    ) {
        parent::__construct(new PayPalGateway(), $email);
    }

    /**
     * @param float $amount
     * @return bool
     */
    public function process(float $amount): bool
    {
        return parent::process($amount);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'PayPal';
    }

    /**
     * @return string
     */
    public function getMaskedEmail(): string
    {
        [$username, $domain] = explode('@', $this->email);
        $maskedUsername = substr($username, 0, 2) . str_repeat('*', max(0, strlen($username) - 2));
        return $maskedUsername . '@' . $domain;
    }
}
