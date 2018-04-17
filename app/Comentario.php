<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comentario extends Model
{
    use SoftDeletes;
    use Userstamps;
    
    protected $table ="comentarios";
    
    protected $guarded = ['id'];

    public function version() {
        return $this->belongsTo('App\Version', 'version_id');
    }
    
    public function autor() {
        return $this->belongsTo('App\Usuario', 'created_by');
    }
}
