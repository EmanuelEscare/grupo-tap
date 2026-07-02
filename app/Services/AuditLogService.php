<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Product;
use App\Models\User;

class AuditLogService
{
    /**
     * @return array<string, mixed>
     */
    public function productSnapshot(Product $product): array
    {
        return [
            'code' => $product->code,
            'name' => $product->name,
            'brand' => $product->brand,
            'price' => $product->price,
        ];
    }

    /**
     * @param  array<string, mixed>|null  $before
     * @param  array<string, mixed>|null  $after
     */
    public function recordProductChange(Product $product, string $action, ?array $before, ?array $after, User $user): void
    {
        AuditLog::query()->create([
            'collection' => 'products',
            'document_id' => (string) $product->getKey(),
            'action' => $action,
            'before' => $before,
            'after' => $after,
            'user_id' => (string) $user->getKey(),
            'created_at' => now(),
        ]);
    }
}
