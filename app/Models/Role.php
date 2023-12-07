<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


use Spatie\Permission\Models\Role as OriginalRole;
class Role extends OriginalRole
{
    public $guard_name = 'api';
}

