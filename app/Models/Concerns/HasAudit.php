<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Auto-populates created_by / updated_by / deleted_by from the
 * authenticated user. Pair with SoftDeletes so deleted_by lands on
 * the same UPDATE that sets deleted_at.
 */
trait HasAudit
{
    public static function bootHasAudit(): void
    {
        static::creating(function (Model $model): void {
            $userId = auth()->id();

            if ($userId === null) {
                return;
            }

            if (empty($model->getAttribute('created_by'))) {
                $model->setAttribute('created_by', $userId);
            }

            if (empty($model->getAttribute('updated_by'))) {
                $model->setAttribute('updated_by', $userId);
            }
        });

        static::updating(function (Model $model): void {
            $userId = auth()->id();

            if ($userId === null) {
                return;
            }

            $model->setAttribute('updated_by', $userId);
        });

        static::deleting(function (Model $model): void {
            $userId = auth()->id();

            if ($userId === null) {
                return;
            }

            $model->setAttribute('deleted_by', $userId);

            // SoftDeletes::runSoftDelete() writes only deleted_at, so persist
            // deleted_by directly. For hard deletes the row is gone after, so
            // the extra UPDATE is harmless overhead we skip.
            if (in_array(SoftDeletes::class, class_uses_recursive($model), true) && ! $model->isForceDeleting()) {
                $model->newQueryWithoutScopes()
                    ->whereKey($model->getKey())
                    ->update(['deleted_by' => $userId]);
            }
        });
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
