<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkFeed extends Model
{
    use HasFactory;
    protected $fillable = ['zip_url', 'status', 'log', 'reimport_status','local_file_url'];
}
