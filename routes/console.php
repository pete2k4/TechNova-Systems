<?php

use App\Models\Discount;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('discounts:activate-scheduled', function (): void {
    $now = now();

    $activated = Discount::query()
        ->where('is_automatic', true)
        ->where('is_active', false)
        ->where(function ($query) use ($now): void {
            $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
        })
        ->where(function ($query) use ($now): void {
            $query->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
        })
        ->update(['is_active' => true]);

    $deactivated = Discount::query()
        ->where('is_automatic', true)
        ->where('is_active', true)
        ->whereNotNull('ends_at')
        ->where('ends_at', '<', $now)
        ->update(['is_active' => false]);

    $this->comment('Automatic discounts updated.');
    $this->comment("Activated: {$activated}");
    $this->comment("Deactivated: {$deactivated}");
})->purpose('Activate/deactivate automatic discounts based on schedule');
