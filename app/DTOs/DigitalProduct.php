<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Contracts\ProductInterface;
use App\Contracts\DownloadableInterface;

/**
 * Digital product - implements ProductInterface + DownloadableInterface
 */
class DigitalProduct implements ProductInterface, DownloadableInterface
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'Windows 11 Pro License';
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return 199.99;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Digital software license';
    }

    /**
     * @return string
     */
    public function getDownloadUrl(): string
    {
        return 'https://example.com/download/win11';
    }

    /**
     * @return int
     */
    public function getFileSize(): int
    {
        return 5368709120; // 5GB in bytes
    }

    /**
     * @return string|null
     */
    public function getLicenseKey(): ?string
    {
        return 'XXXXX-XXXXX-XXXXX-XXXXX';
    }
}
