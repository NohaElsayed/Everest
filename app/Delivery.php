<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Delivery extends Authenticatable
{
    use HasFactory;
    protected $guard_name = 'delivery';
    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
    ];
}
