<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Documento;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;

class PublicacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('publicaciones.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }
    
    public function download($id, $document) {
        
        $file = Documento::findOrFail($id);
        
        if($document == "resumen"){
        
            $ext =  (explode(".", $file->resumen));

            $name = "Resumen_" . $file->nombre . "." . end($ext);
            
            $path = $file->resumen;
            
        }else{
            
            $name = str_replace("documents/", "", $file->final);
            
            $path = $file->final;
            
        }
        
        return Storage::disk('public')->download($path, $name);
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        
        
        sleep(1);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $documents = Documento::where('estatus_id', '>', 0)
                                ->where('entidad_id', $id)
                                ->orderBy('created_at', 'desc')
                                ->with(['usuario', 'estatus'])->get();
        
        return response()->json([
            'documents' => $documents
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $doc = Documento::findOrFail($id);
        $doc->estatus_id = 1;
        $doc->resumen = null; 
        $doc->final = null; 
        $doc->published_at = null; 
        $doc->published_by = null; 
        $doc->save();
    }
    
    

    
}
