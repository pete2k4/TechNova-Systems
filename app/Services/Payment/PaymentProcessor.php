<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Contracts\PaymentMethodInterface;
use App\Factories\PaymentMethodFactory;

class PaymentProcessor
{
    /**
     * @param string $paymentType
     * @param string $credential
     * @param float $amount
     * @return bool
     */
    public function processPaymentByType(string $paymentType, string $credential, float $amount): bool
    {
        $method = PaymentMethodFactory::create($paymentType, $credential);
        return $this->processPayment($method, $amount);
    }

    /**
     * @param array $paymentConfig
     * @param float $amount
     * @return bool
     */
    public function processPaymentFromConfig(array $paymentConfig, float $amount): bool
    {
        $method = PaymentMethodFactory::fromConfig($paymentConfig);
        return $this->processPayment($method, $amount);
    }

    /**
     * @param PaymentMethodInterface $method
     * @param float $amount
     * @return bool
     */
    public function processPayment(PaymentMethodInterface $method, float $amount): bool
    {
        $success = $method->process($amount);
        
        if ($success) {
            // Log::info("Payment of {$amount} processed via {$method->getName()}");
        }
        
        return $success;
    }
}
