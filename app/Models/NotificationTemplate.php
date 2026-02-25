<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    protected $fillable = ['type', 'channel', 'subject', 'content', 'variables', 'is_active'];

    protected $casts = ['variables' => 'array', 'is_active' => 'boolean'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeEmail($query)
    {
        return $query->where('channel', 'email');
    }

    public function scopeWhatsapp($query)
    {
        return $query->where('channel', 'whatsapp');
    }

    public static function render(string $type, string $channel, array $data): ?string
    {
        $template = static::where('type', $type)->where('channel', $channel)->active()->first();
        if (! $template) {
            return null;
        }

        $content = $template->content;
        foreach ($data as $key => $value) {
            $content = str_replace('{{'.$key.'}}', $value, $content);
        }

        return $content;
    }
}
