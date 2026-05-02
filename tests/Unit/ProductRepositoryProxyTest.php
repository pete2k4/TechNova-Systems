<?php

use PHPUnit\Framework\TestCase;
use App\Repositories\ProductRepositoryProxy as ProxyStandalone;
use App\Repositories\ProductRepository as RealRepo;
use App\Contracts\OrderRepositoryInterface;
use App\DTOs\PhysicalProduct;
use App\DTOs\DigitalProduct;

class FakeProductRepo extends RealRepo
{
    private $map;

    public function __construct(array $map = [])
    {
        $this->map = $map;
    }

    public function findById(int $id)
    {
        return $this->map[$id] ?? null;
    }
}

class FakeOrderRepo implements OrderRepositoryInterface
{
    public function save($order): bool { return true; }
    public function findById(int $id) { return null; }
    public function findByUserId(int $userId): array { return []; }
    public function delete(int $id): bool { return true; }
}

class ProductRepositoryProxyTest extends TestCase
{
    public function test_non_downloadable_product_passes_through()
    {
        $physical = new PhysicalProduct();
        $real = new FakeProductRepo([1 => $physical]);
        $orders = new FakeOrderRepo();

        $proxy = new ProxyStandalone($real, $orders);

        $result = $proxy->findById(1);

        $this->assertSame($physical, $result);
    }

    public function test_downloadable_product_blocked_for_unauthenticated_users()
    {
        $digital = new DigitalProduct();
        $real = new FakeProductRepo([2 => $digital]);
        $orders = new FakeOrderRepo();

        $proxy = new ProxyStandalone($real, $orders);

        $result = $proxy->findById(2);

        $this->assertNull($result);
    }
}
