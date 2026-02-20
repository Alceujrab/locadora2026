<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractLog extends Model
{
    protected $fillable = ['contract_id', 'user_id', 'action', 'old_values', 'new_values', 'ip'];
    protected $casts = ['old_values' => 'array', 'new_values' => 'array'];

    public function contract() { return $this->belongsTo(Contract::class); }
    public function user() { return $this->belongsTo(User::class); }
}
