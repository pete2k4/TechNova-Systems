<?php

declare(strict_types=1);

namespace App\Validators;

use App\Contracts\ProductInterface;
use App\Factories\ProductFactory;
use InvalidArgumentException;

class ProductValidator
{
    /**
     * @param array $data
     * @return array
     */
    public function validate(array $data): array
    {
        $errors = [];
        
        // Validate required fields
        if (empty($data['type'])) {
            $errors[] = 'Product type is required';
        } elseif (!in_array($data['type'], ['digital', 'physical'], true)) {
            $errors[] = 'Product type must be either "digital" or "physical"';
        }
        
        if (empty($data['name'])) {
            $errors[] = 'Product name is required';
        }
        
        if (empty($data['price']) || (float) $data['price'] <= 0) {
            $errors[] = 'Product price must be greater than zero';
        }
        
        return $errors;
    }

    /**
     * @param array $data
     * @return ProductInterface
     * @throws InvalidArgumentException
     */
    public function validateAndCreate(array $data): ProductInterface
    {
        $errors = $this->validate($data);
        
        if (!empty($errors)) {
            throw new InvalidArgumentException('Product validation failed: ' . implode(', ', $errors));
        }
        
        return ProductFactory::fromArray($data);
    }
}
