# Discount Service Implementation

## Overview

The Discount Service implements multiple design patterns to provide flexible, composable discount strategies:

1. **Strategy Pattern** - Different discount types (Fixed, Percentage)
2. **Composite Pattern** - Combine discounts into trees
3. **Decorator Pattern** - Add cross-cutting concerns to any discount

## Core Discount Types

### FixedAmountDiscount

Fixed dollar amount off the price.

```php
$discount = new FixedAmountDiscount(20.00);
$discount->calculate(100.00);  // Returns 20.00
```

### PercentageDiscount

Percentage off the price.

```php
$discount = new PercentageDiscount(15.0);  // 15%
$discount->calculate(100.00);  // Returns 15.00
```

## Composite Pattern

Combine multiple discounts, applied sequentially to the remaining price.

```php
$composite = (new CompositeDiscount())
    ->add(new PercentageDiscount(10.0))     // 10% off
    ->add(new FixedAmountDiscount(5.00));   // then $5 off

$composite->calculate(100.00);
// Step 1: 10% of 100 = 10, remaining 90
// Step 2: $5 of 90 = 5, remaining 85
// Total discount: 15.00
```

## Decorator Pattern - Adding Cross-Cutting Concerns

Decorators wrap any `DiscountInterface` and add behavior without modifying the original.

### Available Decorators

#### LoyaltyBonusDecorator

Adds extra loyalty percentage on top of any discount.

```php
$baseDiscount = new PercentageDiscount(10.0);
$withLoyalty = new LoyaltyBonusDecorator(
    loyaltyBonusPercentage: 5.0,  // Extra 5%
    wrappedDiscount: $baseDiscount
);

$withLoyalty->calculate(100.00);
// Base: 10% = $10
// Loyalty: 5% of $10 = $0.50
// Total: $10.50
```

#### MinimumPurchaseDecorator

Only applies discount if purchase meets minimum threshold.

```php
$baseDiscount = new FixedAmountDiscount(20.00);
$withMinimum = new MinimumPurchaseDecorator(
    minimumPrice: 100.00,
    wrappedDiscount: $baseDiscount
);

$withMinimum->calculate(150.00);  // Returns 20.00 (meets minimum)
$withMinimum->calculate(50.00);   // Returns 0.00 (below minimum)
```

#### CappedDiscountDecorator

Ensures discount never exceeds maximum cap.

```php
$baseDiscount = new PercentageDiscount(50.0);
$capped = new CappedDiscountDecorator(
    maximumDiscount: 35.00,
    wrappedDiscount: $baseDiscount
);

$capped->calculate(200.00);  // 50% would be 100, capped at 35.00
```

#### LoggingDecorator

Logs all discount calculations.

```php
$baseDiscount = new PercentageDiscount(10.0);
$logged = new LoggingDecorator(
    logger: Log::channel('discounts'),
    wrappedDiscount: $baseDiscount
);

$logged->calculate(100.00);
// Logs: "Discount [PercentageDiscount] applied: 10.00 on price 100.00"
```

## Stacking Decorators

Decorators can be stacked to build complex behavior.

```php
$discount = new PercentageDiscount(10.0);

// Add loyalty bonus
$discount = new LoyaltyBonusDecorator(5.0, $discount);

// Enforce minimum purchase
$discount = new MinimumPurchaseDecorator(100.00, $discount);

// Cap the total
$discount = new CappedDiscountDecorator(25.00, $discount);

// Add logging
$discount = new LoggingDecorator(Log::channel('discounts'), $discount);

// Now use it!
$finalDiscount = $discount->calculate(200.00);
```

## Combining Patterns

You can mix Composite and Decorator patterns:

```php
// Composite: Two strategies
$composite = (new CompositeDiscount())
    ->add(new PercentageDiscount(10.0))
    ->add(new FixedAmountDiscount(5.00));

// Decorate the composite
$decorated = new LoyaltyBonusDecorator(3.0, $composite);
$decorated = new MinimumPurchaseDecorator(50.00, $decorated);
$decorated = new LoggingDecorator(Log::channel('discounts'), $decorated);

// All patterns work together seamlessly
$totalDiscount = $decorated->calculate(150.00);
```

## Benefits

- **Open/Closed Principle**: Add new discount types or decorators without modifying existing code
- **Single Responsibility**: Each class has one reason to change
- **Composability**: Mix and match decorators and composites
- **DRY**: Decorator logic is reusable across all discount types
- **Testing**: Each decorator is independently testable

## File Structure

- `FixedAmountDiscount.php` - Fixed dollar discount
- `PercentageDiscount.php` - Percentage discount
- `CompositeDiscount.php` - Composite pattern (combine multiple)
- `Decorators/AbstractDiscountDecorator.php` - Base class for decorators
- `Decorators/LoyaltyBonusDecorator.php` - Add loyalty percentage
- `Decorators/MinimumPurchaseDecorator.php` - Require minimum purchase
- `Decorators/CappedDiscountDecorator.php` - Cap maximum discount
- `Decorators/LoggingDecorator.php` - Log discount calculations
