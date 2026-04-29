<?php

namespace App\Models;

use Database\Factories\DealFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    /** @use HasFactory<DealFactory> */
    use HasFactory;
}
