<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicketMessage extends Model
{
    protected $fillable = ['ticket_id', 'user_id', 'customer_id', 'message', 'attachments', 'is_internal'];

    protected $casts = ['attachments' => 'array', 'is_internal' => 'boolean'];

    public function ticket() { return $this->belongsTo(SupportTicket::class, 'ticket_id'); }
    public function user() { return $this->belongsTo(User::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
}
