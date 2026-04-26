<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * SOLID Principle: Interface Segregation Principle (ISP)
 * 
 * ✅ GOOD - Segregated interface only for downloadable products.
 * Only digital products implement this.
 * 
 * Physical products don't need to implement these methods.
 */
interface DownloadableInterface
{
    public function getDownloadUrl(): string;
    public function getFileSize(): int;
    public function getLicenseKey(): ?string;
}
