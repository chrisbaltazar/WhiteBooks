<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;

use App\Documento;
use App\Version;
use App\Entidad;

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
    
    public function load($id) {

        if(auth()->user()->rol_id == 5){
            $documents = Documento::where('estatus_id', '<', 2)->where('entidad_id', auth()->user()->entidad_id)->where('created_by', auth()->user()->id)->orderBy('created_at', 'desc')->with(['usuario', 'estatus'])->get();
        }else{
            $documents = Documento::where('estatus_id', '<', 2)->where('entidad_id', $id)->orderBy('created_at', 'desc')->with(['usuario', 'estatus'])->get();
        }
        
        return response()->json([
            'documents' => $documents
        ]);
    }
    
    public function download($id) {
        
        $file = Version::findOrFail($id);
        
        $ext =  (explode(".", $file->ruta));
        
        $name = $file->documento->nombre . "_Ver_$file->version" . "." . end($ext);
        
        return Storage::disk('public')->download($file->ruta, $name);
        
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
        
        if($request->id){
            $document = Documento::findOrFail($request->id);
        }else{
            $document = new Documento();
            $document->entidad_id = auth()->user()->entidad_id;
        }
        $document->nombre = $request->name;
        $document->estatus_id = 0;
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
        
        if(auth()->user()->rol_id == 5)
            $document = Documento::where('id', $id)->where('entidad_id', auth()->user()->entidad_id)->firstOrFail();
        else 
            $document = Documento::findOrFail($id);
        
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
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $doc = Documento::where('id', $id)->where('entidad_id', auth()->user()->entidad_id)->first();
        $doc->delete();
    }
}
