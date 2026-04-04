# Flyweight Pattern Implementation

## Overview

The Flyweight pattern reduces memory usage by sharing immutable (intrinsic) state across many objects and passing changing (extrinsic) state at runtime.

## Implementation in NovaTech

### ProductCatalogFlyweight

**Location:** `app/Flyweights/ProductCatalogFlyweight.php`

Stores shared product catalog attributes used repeatedly in order creation:
- `productId`
- `name`
- `description`
- `type`
- `basePrice`

These values are intrinsic state and are reused instead of duplicated for each order line.

### ProductCatalogFlyweightFactory

**Location:** `app/Flyweights/ProductCatalogFlyweightFactory.php`

Maintains a flyweight pool keyed by `productId`. When the same product is requested multiple times, the factory returns the same flyweight instance.

## Integration Point

The Builder now supports:

- `OrderBuilder::addItemFromCatalog(int $productId, int $quantity, ?float $overridePrice = null)`

This method:
1. Gets a shared flyweight from the pool.
2. Combines intrinsic state with extrinsic state (`quantity`, optional override price).
3. Adds the line item to the order using the resulting price.

## Why This Fits

Orders often contain repeated references to the same products across requests. The flyweight pool avoids repeatedly re-hydrating the same immutable product metadata in memory.
