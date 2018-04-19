<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Documento;
use Illuminate\Support\Facades\Storage;

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
        //
    }
    
    public function download($id) {
        
        $file = Documento::findOrFail($id);
        
        $ext =  (explode(".", $file->resumen));
        
        $name = "Resumen_" . $file->nombre . "." . end($ext);
        
        return Storage::disk('public')->download($file->resumen, $name);
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $doc = Documento::findOrFail($request->id);
        
        $file = $request->file('file');
      
        $nombre = $file->getClientOriginalName();

        $path = Storage::disk('public')->putFile('uploaded', $file);
        
        $doc->resumen = $path;
        $doc->estatus_id = 2;
        $doc->save();
        
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
        //
    }
}
