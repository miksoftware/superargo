<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    protected $fillable = ['user_id', 'filename', 'total_cedulas', 'processed', 'status', 'export_path'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function results()
    {
        return $this->hasMany(ConsultaResult::class);
    }

    public function pendingResults()
    {
        return $this->results()->where('processed', false);
    }

    public function processedResults()
    {
        return $this->results()->where('processed', true);
    }
}
