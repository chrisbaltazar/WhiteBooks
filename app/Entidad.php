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
    
    public function usuarios() {
        return $this->belongsToMany('App\Usuario', "entidad_usuario");
    }
    
    public function documentos() {
        return $this->hasMany('App\Documento', 'entidad_id');
    }
}
