<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuditObserver
{
    public function created(Model $model): void
    {
        $this->logAction($model, 'created', [], $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $original = $model->getOriginal();
        $changes = $model->getChanges();

        // Remove timestamp noise
        unset($changes['updated_at'], $original['updated_at']);

        if (! empty($changes)) {
            $oldValues = array_intersect_key($original, $changes);
            $this->logAction($model, 'updated', $oldValues, $changes);
        }
    }

    public function deleted(Model $model): void
    {
        $this->logAction($model, 'deleted', $model->getAttributes(), []);
    }

    private function logAction(Model $model, string $action, array $old, array $new): void
    {
        try {
            DB::table('audit_logs')->insert([
                'model_type' => get_class($model),
                'model_id' => (string) $model->getKey(),
                'action' => $action,
                'old_values' => json_encode($old, JSON_UNESCAPED_UNICODE),
                'new_values' => json_encode($new, JSON_UNESCAPED_UNICODE),
                'user_id' => Auth::id(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Silently fail — audit should never break the main flow
            \Log::warning('AuditObserver failed: '.$e->getMessage());
        }
    }
}
