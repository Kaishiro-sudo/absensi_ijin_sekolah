<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perizinan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'alasan',
        'jam_keluar',
        'jam_kembali',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'jam_keluar' => 'datetime',
            'jam_kembali' => 'datetime',
        ];
    }

    // Relasi ke tabel User (Siswa yang mengajukan)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logGerbangs(): HasMany
    {
        return $this->hasMany(LogGerbang::class);
    }
}
