<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;
use Illuminate\Database\Eloquent\SoftDeletes;

class Version extends Model
{
    use SoftDeletes;
    use Userstamps;
    
    protected $table = "versiones";
    
    public function comentarios() {
        return $this->hasMany('App\Comentario');
    }
    
    public function autor() {
        return $this->belongsTo('App\Usuario', 'created_by');
    }
    
    public function documento() {
        return $this->belongsTo('App\Documento', 'documento_id');
    }
}
