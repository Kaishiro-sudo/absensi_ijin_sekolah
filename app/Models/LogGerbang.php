<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogGerbang extends Model
{
    protected $fillable = [
        'perizinan_id',
        'satpam_id',
        'tipe_scan',
        'waktu_scan',
    ];

    protected function casts(): array
    {
        return [
            'waktu_scan' => 'datetime',
        ];
    }

    public function perizinan(): BelongsTo
    {
        return $this->belongsTo(Perizinan::class);
    }

    public function satpam(): BelongsTo
    {
        return $this->belongsTo(User::class, 'satpam_id');
    }
}
