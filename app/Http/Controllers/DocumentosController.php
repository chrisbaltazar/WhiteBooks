<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Documento;
use App\Version;

class DocumentosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect()->route('documentos.create');
    }
    
    public function load() {

        $documents = Documento::where('entidad_id', auth()->user()->entidad_id)->orderBy('created_at', 'desc')->with('usuario')->get();
        
        return response()->json(['documents' => $documents]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('documentos.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|max:255', 
            'file'      => 'required'
        ]);
        
        $file = $request->file('file');
      
        $nombre = $file->getClientOriginalName();

        $path = Storage::disk('public')->putFile('uploaded', $file);
        
        if($request->id)
            $document = Documento::findOrFail($request->id);
        else
            $document = new Documento();
        
        $document->nombre = $request->name;
        $document->entidad_id = auth()->user()->entidad_id;
        $document->save();
        
        $version = new Version();
        $version->ruta = $path;
        $version->version = Version::where('documento_id', $document->id)->count() + 1;
        
        $document->versiones()->save($version);
        
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
        
        $version = Documento::findOrFail($id)->versiones()->orderBy('id', 'desc')->first();
        
        $comments = Documento::find($id)->comentarios()->with('autor')->orderBy('id', 'desc')->get();
        
        return response()->json([
            'version' => $version, 
            'comments' => $comments 
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
        $document = Documento::where('id', $id)->where('entidad_id', auth()->user()->entidad_id)->firstOrFail();
        
        return view('documentos.index')->with('document', $document);

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
