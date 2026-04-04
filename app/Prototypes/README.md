# Singleton Pattern Implementation

## Overview

The Singleton pattern is a creational design pattern that restricts the instantiation of a class to a single object and provides a global point of access to that instance. It ensures that a class has only one instance while providing a global way to access it.

## Problem Solved

**Without Singleton:**
- Multiple instances of the same registry are created throughout the app
- State becomes fragmented and inconsistent
- Same setup code runs multiple times (inefficient)
- Risk of out-of-sync data

**With Singleton:**
- Single authoritative instance across the entire application
- Consistent, shared state
- Efficient resource usage
- Guaranteed single source of truth

## Implementation in NovaTech

### ProductPrototypeRegistry

The `ProductPrototypeRegistry` is implemented as a Singleton to maintain a **single global registry of product prototypes** used by the factory throughout your application.

**Location:** [app/Prototypes/ProductPrototypeRegistry.php](app/Prototypes/ProductPrototypeRegistry.php)

**Key Features:**
- Private constructor prevents direct instantiation
- Static `getInstance()` method returns the sole instance
- Static `reset()` method for testing (resets the singleton state)
- Once initialized, all calls to `getInstance()` return the same object

## How It Works

```php
// First call - creates the singleton instance
$registry1 = ProductPrototypeRegistry::getInstance();

// Subsequent calls - returns the SAME instance
$registry2 = ProductPrototypeRegistry::getInstance();

// TRUE - both variables reference the same object
$registry1 === $registry2  // true
```

### Singleton Structure

```
┌────────────────────────────────────┐
│  ProductPrototypeRegistry (Static) │
├────────────────────────────────────┤
│ - private static $instance         │ ← Only instance variable
│ - private __construct()            │ ← Can't instantiate directly
├────────────────────────────────────┤
│ + getInstance(): self              │ ← Access the singleton
│ + reset(): void                    │ ← Reset for testing
│ + register(type, prototype): void  │ ← Add prototypes
│ + has(type): bool                  │ ← Check type exists
│ + getClone(type): ProductInterface │ ← Get cloned product
└────────────────────────────────────┘
```

## Usage Examples

### Basic Singleton Access

```php
// Get the singleton instance
$registry = ProductPrototypeRegistry::getInstance();

// Register a prototype
$registry->register('digital', new DigitalProduct());

// Get a clone
$clone = $registry->getClone('digital');
```

### Integration with ProductFactory

The `ProductFactory` internally uses the Singleton:

```php
// Factory uses singleton behind the scenes
$product1 = ProductFactory::create('digital');
$product2 = ProductFactory::create('digital');

// Both come from the same registry
// But each is a distinct clone
```

### Custom Prototype Registration

```php
// Register custom prototype in the singleton registry
ProductFactory::registerPrototype('subscription', new SubscriptionProduct());

// Now available throughout the application
$product = ProductFactory::create('subscription');
```

### Ensuring Initialization

The Singleton uses lazy initialization—the first call to `getInstance()` or factory methods triggers setup:

```php
// First call initializes default prototypes (digital, physical)
$product = ProductFactory::create('digital');

// Subsequent calls reuse the same initialized registry
$anotherProduct = ProductFactory::create('physical');
```

## Benefits

1. **Global Access**: Single point of access to product prototypes across the app
2. **Consistency**: One registry = one source of truth for all prototypes
3. **Efficiency**: Initialization happens once, then reused
4. **Testability**: `reset()` allows clean test isolation
5. **Thread-Safe**: Lazy initialization pattern is thread-safe in most use cases

## Comparison: With vs Without Singleton

### Without Singleton (Anti-pattern)
```php
class ProductFactory
{
    public static function create($type)
    {
        // PROBLEM: Creates new registry each time
        $registry = new ProductPrototypeRegistry();
        $registry->register('digital', new DigitalProduct());
        
        return $registry->getClone($type);
    }
}

// Result:
$p1 = ProductFactory::create('digital'); // New registry created
$p2 = ProductFactory::create('digital'); // NEW registry created again!
// Now you have 2 separate registries with separate prototypes!
```

### With Singleton (Correct)
```php
class ProductFactory
{
    public static function create($type)
    {
        // CORRECT: Always returns the same registry
        return ProductPrototypeRegistry::getInstance()
            ->getClone($type);
    }
}

// Result:
$p1 = ProductFactory::create('digital'); // Registry initialized
$p2 = ProductFactory::create('digital'); // Same registry reused
// Single registry throughout the app!
```

## Testing the Singleton

The pattern is validated with comprehensive tests:

```php
public function testGetInstanceReturnsSameInstance(): void
{
    $instance1 = ProductPrototypeRegistry::getInstance();
    $instance2 = ProductPrototypeRegistry::getInstance();
    
    $this->assertSame($instance1, $instance2);
}

public function testResetDestroysInstance(): void
{
    $instance1 = ProductPrototypeRegistry::getInstance();
    ProductPrototypeRegistry::reset();
    $instance2 = ProductPrototypeRegistry::getInstance();
    
    // After reset, new instance is created
    $this->assertNotSame($instance1, $instance2);
}
```

Run tests:
```bash
./vendor/bin/sail test --filter=ProductPrototypeRegistryTest
```

## Singleton Guarantees

1. **Only One Instance**
   - Private constructor prevents `new ProductPrototypeRegistry()`
   - Static `$instance` variable holds the sole instance
   - First call creates it, subsequent calls return it

2. **Global Access**
   - Static `getInstance()` accessible from anywhere
   - No need to pass the registry around as a dependency

3. **Controlled Initialization**
   - `ProductFactory::ensureInitialized()` registers default prototypes once
   - Subsequent factory calls reuse the initialized registry

4. **Test Isolation**
   - `reset()` method allows tests to get a clean singleton
   - Each test calls `reset()` in `tearDown()` to prevent test pollution

## When to Use Singleton

Use Singleton for:
- ✅ Global registries or caches that should have one instance
- ✅ Configuration managers
- ✅ Database connection pools
- ✅ Logger instances
- ✅ Resource factories

**Don't use Singleton for:**
- ❌ Service classes that can have multiple instances
- ❌ Data models or entities
- ❌ Dependencies that need testing/mocking variations
- ❌ Stateful objects that shouldn't be global

## Integration Points

- **ProductPrototypeRegistry** (Singleton)
  - ↑ Used by
- **ProductFactory** (Factory Pattern)
  - ↑ Used by
- **Controllers/Services** that call ProductFactory

## Design Pattern Combinations

**Singleton + Factory + Prototype:**
```
ProductFactory (Factory Pattern)
    ↓ uses
ProductPrototypeRegistry (Singleton Pattern)
    ↓ stores
Product Prototypes (Prototype Pattern)
    ↓ when cloned
Individual Product Instances
```

This combination gives you:
- **Factory**: Easy object creation API
- **Singleton**: One global registry of prototypes
- **Prototype**: Fast cloning instead of heavy instantiation

## Related Patterns

- **Multiton**: Multiple named instances (Singleton variant)
- **Object Pool**: Reusing pre-created instances
- **Service Locator**: Similar global access (but more flexible)
- **Dependency Injection**: Alternative to global access patterns

## Potential Issues & Solutions

### Issue: Thread Safety
**In PHP's typical synchronous execution**, this isn't a concern. However, if using async extensions:
```php
// Could use double-check locking, but PHP doesn't need it typically
public static function getInstance(): self
{
    if (self::$instance === null) {
        self::$instance = new self();
    }
    return self::$instance;
}
```

### Issue: Testing Difficulty
**Solution**: Implement `reset()` for test isolation
```php
protected function tearDown(): void
{
    ProductPrototypeRegistry::reset();
    parent::tearDown();
}
```

### Issue: Hard to Replace with Mocks
**Solution**: Provide a test-friendly reset and re-register method
```php
// In tests
ProductPrototypeRegistry::reset();
ProductFactory::registerPrototype('digital', $mockProduct);
```

## File Manifest

- [app/Prototypes/ProductPrototypeRegistry.php](app/Prototypes/ProductPrototypeRegistry.php) - Singleton implementation
- [app/Factories/ProductFactory.php](app/Factories/ProductFactory.php) - Factory using the Singleton
- [tests/Unit/Prototypes/ProductPrototypeRegistryTest.php](tests/Unit/Prototypes/ProductPrototypeRegistryTest.php) - Comprehensive test suite

## Further Reading

- [Refactoring Guru: Singleton Pattern](https://refactoring.guru/design-patterns/singleton)
- [PHP Singleton Pattern](https://www.digitalocean.com/community/tutorials/php-design-pattern-singleton)
- [Java Design Patterns: Singleton](https://www.digitalocean.com/community/tutorials/java-singleton-pattern-example-code)
