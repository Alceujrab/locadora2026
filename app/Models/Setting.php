<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'branch_id', 'group', 'key', 'value', 'type', 'description',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // Cast value based on type
    public function getCastedValueAttribute()
    {
        return match ($this->type) {
            'integer' => (int) $this->value,
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    // Static helpers
    public static function get(string $key, mixed $default = null, ?int $branchId = null): mixed
    {
        $setting = static::where('key', $key)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->first();

        return $setting ? $setting->casted_value : $default;
    }

    public static function set(string $key, mixed $value, string $group = 'general', ?int $branchId = null): void
    {
        static::updateOrCreate(
            ['key' => $key, 'branch_id' => $branchId],
            ['value' => is_array($value) ? json_encode($value) : (string) $value, 'group' => $group]
        );
    }
}
