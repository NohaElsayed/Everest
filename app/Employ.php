<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Employ extends Authenticatable
{
    use HasFactory;
    use HasRoles;
/**
* The attributes that are mass assignable.
*
* @var array
*/
protected $guard_name = 'employs';
protected $fillable = [
'name', 'email', 'password','roles_name','Status','added'
];
}
