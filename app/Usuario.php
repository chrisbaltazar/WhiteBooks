<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    use SoftDeletes;
    use Userstamps;
    
    protected $table = "usuarios";
    protected $guarded = ['id', 'Password_confirmation'];
    
    public function Entidad(){
        return $this->belongsTo('App\Entidad', 'entidad_id')->withDefault();
    }
    
    public function Rol(){
        return $this->belongsTo('App\Rol', 'rol_id')->withDefault();
    }
    
    public function getValidateAttribute() {
        return in_array($this->rol_id, array("3", "2", "1"));
    }
    
    public function getReviewAttribute() {
        return in_array($this->rol_id, array(4));
    }
}
