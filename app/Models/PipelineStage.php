<?php

namespace App\Models;

use Database\Factories\PipelineStageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PipelineStage extends Model
{
    /** @use HasFactory<PipelineStageFactory> */
    use HasFactory;
}
