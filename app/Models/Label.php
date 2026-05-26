<?php

namespace App\Models;

use App\Models\Card;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Label extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'workspace_id',
        'name',
        'color'
    ];

    public function cards()
    {
        return $this->belongsToMany(Card::class, 'card_label')
            ->withTimestamps(); // Relasi many-to-many, Label -> Card melalui pivot
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
}
