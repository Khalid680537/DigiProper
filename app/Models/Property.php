<?php

namespace App\Models;

use App\Models\Concerns\HasAudit;
use Database\Factories\PropertyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name',
    'title_holder',
    'rera_number',
    'address_line1',
    'address_line2',
    'city',
    'state',
    'pincode',
    'tenure',
    'tenure_authority',
    'occupancy_status',
    'tenant_or_occupant',
    'construction',
    'area_value',
    'area_unit',
    'imputed_value_inr',
    'rent_yearly_inr',
    'yield_percent',
    'keys_location',
    'extra_notes',
    'is_data_complete',
])]
class Property extends Model
{
    /** @use HasFactory<PropertyFactory> */
    use HasAudit, HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::addGlobalScope('owner', function (Builder $query): void {
            if (auth()->check()) {
                $query->where($query->getModel()->getTable().'.created_by', auth()->id());
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_data_complete' => 'boolean',
        ];
    }

    /**
     * @return HasMany<PropertyDocument, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(PropertyDocument::class);
    }

    /**
     * @return HasMany<PropertyContact, $this>
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(PropertyContact::class)->orderBy('position');
    }

    /**
     * Computed yield falls back to rent / value * 100 when no override is stored.
     *
     * @return Attribute<string|null, never>
     */
    protected function effectiveYieldPercent(): Attribute
    {
        return Attribute::get(function (): ?string {
            if ($this->yield_percent !== null) {
                return $this->yield_percent;
            }

            $rent = $this->rent_yearly_inr;
            $value = $this->imputed_value_inr;

            if ($rent === null || $value === null || (float) $value === 0.0) {
                return null;
            }

            return number_format(((float) $rent / (float) $value) * 100, 2, '.', '');
        });
    }
}
