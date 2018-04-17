<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Entidad extends Model
{
    use SoftDeletes;
    use Userstamps;
    
    protected $table = "entidades";
    protected $guarded = ['id'];
}
