<?php

// app/Models/TaxSetting.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxSetting extends Model
{
    protected $table = 'tax_settings';
    protected $fillable = ['is_enabled','rate_percent','name'];
}
