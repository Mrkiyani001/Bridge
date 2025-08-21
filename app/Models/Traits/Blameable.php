<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait Blameable
{
    protected static array $__blameableColumnsCache = [];

    public static function bootBlameable(): void
    {
        static::creating(function ($model) {
            if (!Auth::check()) {
                return;
            }

            if (self::hasColumnCached($model, 'created_by') && is_null($model->created_by)) {
                $model->created_by = Auth::id();
            }

            if (self::hasColumnCached($model, 'updated_by') && is_null($model->updated_by)) {
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (!Auth::check()) {
                return;
            }

            if (self::hasColumnCached($model, 'updated_by')) {
                $model->updated_by = Auth::id();
            }
        });
    }

    protected static function hasColumnCached($model, string $column): bool
    {
        try {
            $table = $model->getTable();

            // Ensure table exists (prevents errors during migrations/early boot)
            if (!Schema::hasTable($table)) {
                return false;
            }

            // Cache per-table column listing to avoid repeated DB calls
            if (!isset(self::$__blameableColumnsCache[$table])) {
                self::$__blameableColumnsCache[$table] = Schema::getColumnListing($table);
            }

            return in_array($column, self::$__blameableColumnsCache[$table], true);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
