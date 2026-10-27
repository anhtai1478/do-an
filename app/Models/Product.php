<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'name',
    'price',
    'category',
    'brand',
    'is_on_sale',
    'discount_percent',
    'company_profile',
    'description',
    'image',
])]
class Product extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'image' => 'array',
            'price' => 'decimal:2',
            'is_on_sale' => 'boolean',
            'discount_percent' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function primaryImage(): ?string
    {
        $primaryImage = $this->imagePaths()[0] ?? null;

        return is_string($primaryImage) && $primaryImage !== '' ? $primaryImage : null;
    }

    public function discountedPrice(): ?string
    {
        $discountPercent = $this->discount_percent;

        if (! $this->is_on_sale || $discountPercent < 1 || $discountPercent > 100) {
            return null;
        }

        $priceInCents = (int) round((float) $this->price * 100);
        $discountedPriceInCents = intdiv(
            $priceInCents * (100 - $discountPercent) + 50,
            100,
        );

        return sprintf(
            '%d.%02d',
            intdiv($discountedPriceInCents, 100),
            $discountedPriceInCents % 100,
        );
    }

    /**
     * @return array<int, string>
     */
    public function imagePaths(): array
    {
        $storedImage = $this->getRawOriginal('image');

        if (! is_string($storedImage) || $storedImage === '') {
            return [];
        }

        $decodedImages = json_decode($storedImage, true);

        if (is_array($decodedImages)) {
            return array_values(array_filter($decodedImages, 'is_string'));
        }

        return is_string($decodedImages) ? [$decodedImages] : [$storedImage];
    }
}
