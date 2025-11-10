<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        "google_id",
        'title',
        'authors',
        "publisher",
        "publishedDate",
        "description",
        'imageLinks',
        "categories",
        "pageCount",
        "amount"
    ];
}
