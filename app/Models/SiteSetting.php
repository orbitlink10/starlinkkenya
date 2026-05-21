<?php

namespace App\Models;

use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    public static function value(string $key, ?string $default = null): ?string
    {
        if (! static::tableExists()) {
            return $default;
        }

        try {
            $value = static::query()->where('key', $key)->value('value');
        } catch (QueryException) {
            return $default;
        }

        return is_string($value) ? $value : $default;
    }

    public static function setValue(string $key, ?string $value): self
    {
        if (! static::tableExists()) {
            throw new \RuntimeException('Run the latest database migration to enable site settings.');
        }

        return static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public static function tableExists(): bool
    {
        try {
            return Schema::hasTable('site_settings');
        } catch (QueryException) {
            return false;
        }
    }
}
