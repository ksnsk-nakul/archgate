<?php

namespace App\Models;

use Database\Factories\DealFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deal extends Model
{
    /** @use HasFactory<DealFactory> */
    use HasFactory;

    protected $fillable = ['title', 'value', 'notes', 'stage_id', 'contact_id', 'owner_id'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['value' => 'decimal:2'];
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class, 'stage_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
