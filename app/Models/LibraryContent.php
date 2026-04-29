<?php

namespace App\Models;

use Database\Factories\LibraryContentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'type', 'description', 'access_level'])]
class LibraryContent extends Model
{
    /** @use HasFactory<LibraryContentFactory> */
    use HasFactory;
}
