<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Documento extends Model
{
    use SoftDeletes;
    use Userstamps;
    
    protected $table = "documentos";
    protected $guarded = ['id'];
    
    public function versiones() {
        return $this->hasMany('App\Version');
    }
  
    public function usuario() {
        return $this->belongsTo('App\Usuario', 'created_by')->withDefault();
    }
    
    public function comentarios() {
        return $this->hasManyThrough('App\Comentario', 'App\Version');
    }
}
