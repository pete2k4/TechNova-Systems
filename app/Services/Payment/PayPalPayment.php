<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Contracts\PaymentMethodInterface;

class PayPalPayment implements PaymentMethodInterface
{
    /**
     * @param string $email
     */
    public function __construct(
        private readonly string $email
    ) {}

    /**
     * @param float $amount
     * @return bool
     */
    public function process(float $amount): bool
    {
        // In production, this would connect to PayPal API
        // $paypalApi = new PayPalAPI();
        // return $paypalApi->charge($this->email, $amount);
        
        return true;
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
