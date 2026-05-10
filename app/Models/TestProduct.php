<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestProduct extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getSitemapUrl()
    {
        return url('/test-product/' . $this->slug);
    }
}
