<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * SOLID Principle: Single Responsibility Principle (SRP)
 * 
 * This class has ONE responsibility: validating product data.
 * It doesn't handle persistence, business logic, or formatting - just validation.
 * 
 * ✅ Good: Focuses only on validation
 * ❌ Bad would be: Mixing validation + saving + email notifications in one class
 */
class ProductValidator
{
    /**
     * Validates product data.
     * 
     * @param array $data
     * @return array Validation errors (empty if valid)
     */
    public function validate(array $data): array
    {
        $errors = [];
        
        // Validation logic would go here
        // if (empty($data['name'])) { $errors[] = 'Name is required'; }
        // if ($data['price'] <= 0) { $errors[] = 'Price must be positive'; }
        
        return $errors;
    }
}
