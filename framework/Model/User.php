<?php
declare(strict_types=1);

namespace Delos\Model;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = "users";
    protected $fillable = ["username","email","password"];
}