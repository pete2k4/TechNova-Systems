<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Contracts\ProductInterface;
use App\Contracts\DownloadableInterface;

/**
 * SOLID Principle: Interface Segregation Principle (ISP)
 * 
 * Digital product implements ProductInterface + DownloadableInterface.
 * It doesn't need shipping methods - ISP allows it to avoid implementing them.
 * 
 * ✅ Respects ISP - only implements relevant interfaces
 */
class DigitalProduct implements ProductInterface, DownloadableInterface
{
    public function getName(): string
    {
        return 'Windows 11 Pro License';
    }

    public function getPrice(): float
    {
        return 199.99;
    }

    public function getDescription(): string
    {
        return 'Digital software license';
    }

    public function getDownloadUrl(): string
    {
        return 'https://example.com/download/win11';
    }

    public function getFileSize(): int
    {
        return 5368709120; // 5GB in bytes
    }

    public function getLicenseKey(): ?string
    {
        return 'XXXXX-XXXXX-XXXXX-XXXXX';
    }
}
