<?php
declare(strict_types=1);

namespace Delos\Model\Session;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $table = "sessions";
    protected $guarded = [];
}