<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Usuario;

class TestController extends Controller
{
    public function index() {
        
//        insertMail("Libros Blancos", "cbaltazarc@guanajuato.gob.mx", "Test from white books", "some text here...", "Christian");
        $super = Usuario::where('rol_id', 3)->whereHas('entidades', function($query){
            $query->where('entidad_id', 1);
        })->get();
        
        $rev = Usuario::where('rol_id', 4)->whereIn('padre_id', $super->pluck('id'))->get();
        
        $mails = $super->merge($rev)->pluck('Correo');
        
        dd($mails);
        
    }
}
