<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;

use App\Documento;
use App\Version;
use App\Entidad;
use Illuminate\Support\Facades\App;

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
            $documents = Documento::where('entidad_id', auth()->user()->entidad_id)->where('created_by', auth()->user()->id)->orderBy('created_at', 'desc')->with(['usuario', 'estatus'])->get();
        }else{
            $documents = Documento::where('entidad_id', $id)->orderBy('created_at', 'desc')->with(['usuario', 'estatus'])->get();
        }
        
        $documents->each(function($doc){
            $doc->hasResume = $doc->versiones()->where('tipo', 'executive')->count();
        });
        
        return response()->json([
            'documents' => $documents
        ]);
    }
    
    public function download($id) {
        
        $file = Version::findOrFail($id);
        
        $ext =  (explode(".", $file->ruta));
        
        $name = $file->documento->nombre . "_Ver_" . ($file->tipo == "extended" ? "Extendida_" : "Ejecutiva_") . "$file->version" . "." . end($ext);
        
        return Storage::disk('public')->download($file->ruta, $name);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        return view('documentos.index')->with('type', 'extended');
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
        $document->save();
        
        $version = new Version();
        $version->ruta = $path;
        $version->tipo = $request->type;
        $version->version = Version::where('documento_id', $document->id)->where('tipo', $request->type)->count() + 1;
        
        $document->versiones()->save($version);
        
        sleep(1);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $type)
    {
        
        $version = Documento::findOrFail($id)->versiones()->where('tipo', $type)->orderBy('version', 'desc')->first();
        
        $comments = Documento::find($id)->comentarios()->with(['autor', 'version'])->orderBy('id', 'desc')->get();
        
        
        $comments->each(function($com){
           $com->autorRol = $com->autor->Rol->NombreRol; 
        });
        
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
        
        return view('documentos.index')
                ->with('document', $document)
                ->with('type', $document->estatus_id < 2 ? 'extended' : 'executive');

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
        $ver = Version::findOrFail($id);
        $doc = Documento::find($ver->documento_id);
        $convert = asset("storage/$ver->ruta");
        
        if(!App::environment('local')){
        
            $parameters = array(
                'Secret' => 'RT4gLUKVsIFmoEYq',
                'StoreFile' => 'true'
            );
            $result = $this->convert_api('docx', 'pdf', $convert, $parameters);
    //        print(json_encode($result));
            $url = $result->Files[0]->Url;
            $contents = file_get_contents($url);
            
            if($ver->documento->estatus_id == 2){
                $path = "documents/LB" . $ver->documento->id . "_Final_" . $ver->documento->nombre . ".pdf";
                Storage::disk('public')->put($path, $contents);
                $doc->final = $path;
                $doc->save();
            }elseif($ver->documento->estatus_id == 3){
                $path = "documents/LB" . $ver->documento->id . "_Resumen_" . $ver->documento->nombre . ".pdf";
                Storage::disk('public')->put($path, $contents);
                $doc->resumen = $path;
                $doc->save();
            }
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $doc = Documento::where('id', $id)->where('entidad_id', auth()->user()->entidad_id)->firstOrFail();
        $doc->delete();
    }
    
    // Function converts file format. More info about formats on http://www.convertapi.com/
    // src_format (string) - conversion source file format ('pdf', 'jpg', 'tif', etc.)
    // dst_format (string) - conversion result file format ('pdf', 'jpg', 'tif', etc.)
    // files (string or array) - path to local ore remote file ('C:\myfile.doc', '/home/jon/myfile.doc', 'http://mydomain.com/myfile.doc')
    // parameters (array) - key-value array of additional parameters (array('FileName' => 'myfile', 'StoreFile' => true) more information on http://www.convertapi.com/)
    function convert_api($src_format, $dst_format, $files, $parameters) {
        $parameters = array_change_key_case($parameters);
        $auth_param = array_key_exists('secret', $parameters) ? 'secret='.$parameters['secret'] : 'token='.$parameters['token'];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_URL, "https://v2.convertapi.com/{$src_format}/to/{$dst_format}?{$auth_param}");

        if (is_array($files)) {
            foreach ($files as $index=>$file) {
                $parameters["files[$index]"] = file_exists($file) ? new CurlFile($file) : $file;
            }    
        } else {
                $parameters['file'] = file_exists($files) ? new CurlFile($files) : $files;
        }    

        curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters);
        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);
        if ($response && $httpcode >= 200 && $httpcode <= 299) {
            return json_decode($response);
        } else {
            abort(500, "Error conviertiendo versión final para publicación");
        }  
    }
}
