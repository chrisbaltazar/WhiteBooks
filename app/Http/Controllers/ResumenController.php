<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Usuario;
use App\Documento;
use Illuminate\Support\Facades\Storage;

class ResumenController extends Controller
{
    public function index() {
        return view('resumen.index');
    }
    
    public function load() {
        
        if(auth()->user()->isPublisher()){
            $documents = Documento::with(['entidad' => function($query){
                $query->orderBy('Nombre');
            }, 'usuario', 'estatus'])->orderBy('created_at')->get();
        }elseif(auth()->user()->isSuper()){
            $entities = Usuario::find(auth()->user()->id)->entidades()->toArray();
            $documents = Documento::with(['entidad' => function($query){
                $query->orderBy('Nombre');
            }, 'usuario', 'estatus'])
            ->whereIn('entidad_id', $entities)
            ->orderBy('created_at')->get();
        }elseif(auth()->user()->isReviewer()){
            $entities = Usuario::find(auth()->user()->padre_id)->entidades()->toArray();
            $documents = Documento::with(['entidad' => function($query){
                $query->orderBy('Nombre');
            }, 'usuario', 'estatus'])
            ->whereIn('entidad_id', $entities)
            ->orderBy('created_at')->get();
        }elseif(auth()->user()->isUser()){
           $documents = Documento::with(['entidad' => function($query){
                $query->orderBy('Nombre');
            }, 'usuario', 'estatus'])
            ->where('entidad_id', auth()->user()->entidad_id)
            ->orderBy('created_at')->get();
        }

        $documents->each (function($doc){
            $doc->extended = $doc->versiones()->where('tipo', 'extended')->orderBy('version', 'desc')->first();
            $doc->executive = $doc->versiones()->where('tipo', 'executive')->orderBy('version', 'desc')->first();
        });
        
        return response()->json([
            'documents' => $documents
        ]);
    }
    
    public function download($id, $type) {
        
        $file = Documento::findOrFail($id);
        
        if($type == "extended"){
            return Storage::disk('public')->download($file->final);
        }else{
            return Storage::disk('public')->download($file->resumen);
        }
        
    }
    
    public function reset(Request $request) {
        $document = Documento::findOrFail($request->id);
        $document->estatus_id = 0;
        $document->reviewed_at = null;
        $document->reviewed_by = null;
        $document->validated_at = null;
        $document->validated_by = null;
        $document->published_at = null;
        $document->published_by = null;
        $document->final = null;
        $document->resumen = null;
        
        $document->save();
        
        $document->versiones()->where('tipo', 'executive')->delete();
    }
}
