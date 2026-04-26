<?php

declare(strict_types=1);

namespace App\Factories;

use App\Contracts\PaymentMethodInterface;
use App\Services\Payment\CreditCardPayment;
use App\Services\Payment\PayPalPayment;
use InvalidArgumentException;

class PaymentMethodFactory
{
    public const CREDIT_CARD = 'credit_card';
    public const PAYPAL = 'paypal';

    /**
     * @param string $type
     * @param string $credential
     * @return PaymentMethodInterface
     * @throws InvalidArgumentException
     */
    public static function create(string $type, string $credential): PaymentMethodInterface
    public static function create(string $type, string $credential): PaymentMethodInterface
    {
        if (empty($credential)) {
            throw new InvalidArgumentException('Payment credential cannot be empty');
        }

        return match (strtolower($type)) {
            self::CREDIT_CARD => self::createCreditCardPayment($credential),
            self::PAYPAL => self::createPayPalPayment($credential),
            default => throw new InvalidArgumentException("Unknown payment method: {$type}"),
        };
    }

    /**
     * @param string $cardNumber
     * @return CreditCardPayment
     * @throws InvalidArgumentException
     */
    public static function createCreditCardPayment(string $cardNumber): CreditCardPayment
    {
        if (!self::isValidCardNumber($cardNumber)) {
            throw new InvalidArgumentException('Invalid credit card number format');
        }

        return new CreditCardPayment($cardNumber);
    }

    /**
     * @param string $email
     * @return PayPalPayment
     * @throws InvalidArgumentException
     */
    public static function createPayPalPayment(string $email): PayPalPayment
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid PayPal email address');
        }

        return new PayPalPayment($email);
    }

    /**
     * @param array $config
     * @return PaymentMethodInterface
     * @throws InvalidArgumentException
     */
    public static function fromConfig(array $config): PaymentMethodInterface
    {
        $type = $config['type'] ?? throw new InvalidArgumentException('Missing payment method type');
        $credential = $config['credential'] ?? throw new InvalidArgumentException('Missing payment credential');

        return self::create($type, $credential);
    }

    /**
     * @param string $cardNumber
     * @return bool
     */
    private static function isValidCardNumber(string $cardNumber): bool
    {
        // Remove spaces and dashes
        $cardNumber = preg_replace('/[\s\-]/', '', $cardNumber);

        // Check if it's numeric and length is reasonable
        return preg_match('/^\d{13,19}$/', $cardNumber) === 1;
    }
}
