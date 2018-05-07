@extends ('base')

@section ('title', 'Cargar nuevo documento')


@section ('content')


<form id ="uploader" @submit.prevent="uploadFile($event)"> 
    
    <div v-if="documentId" class="alert alert-danger">
        <h3 class="">Usted esta cargando una nueva versión del archivo: <br>
            <i class="fa fa-quote-left"></i> @{{ documentName }} <i class="fa fa-quote-right"></i>
            <br> <small>(versión @{{ documentType == 'extended' ? "extendida" : "ejecutiva" }})</small>
        </h3>
    </div>
    <div v-else class="alert alert-dark">
         <h3 class="text-center">Usted esta cargando un nuevo archivo 
         <br> <small>(versión extendida)</small></h3>
    </div>
    
    <div class="card">
        <div class="card-header bg-success text-white">Datos del documento</div>
        <div class="card-body">
            <div class="form-control">
                <label>Nombre</label>
                <input type="text" class="form-control" v-model="documentName" placeholder="Nombre del documento" required >
            </div>
        </div>
    </div>
    
    <div class="form-control text-center">
        <h4><span class="badge badge-primary">Por favor seleccione y cargue su documento</span></h4>
        <input type="file" id ="file" accept=".docx" required>
    </div>
    <br>
    <button class="btn btn-success btn-lg mx-auto"><i class="fa fa-upload"></i> Subir archivo</button>
       
</form>


@endsection

@section ('script')

<script> 
    var v = new Vue({
        el: '#uploader', 
        data: {
            documentType: '{{ $type }}',
            documentId: '{{ isset($document) ? $document->id : '' }}',
            documentName: '{{ isset($document) ? $document->nombre : "" }}'
        }, 
        methods: {
            uploadFile(event){
               const file = document.querySelector('#file');
               const formData = new FormData();
               formData.append("id", this.documentId);
               formData.append("name", this.documentName);
               formData.append("type", this.documentType);
               formData.append("file", file.files[0]);
               LoadButton($(event.target).find('button'));
               this.$http.post('{{ url('documentos') }}', formData).then(response => {
                   Ready();
                   OK("Guardado");
                   setTimeout(function(){
                       location.href = "{{url('/historial')}}";
                   }, 2000);
               }, error => {
                   Ready();
                   Wrong(DisplayErrors(error));
               });
            }
        }
    })
    
</script>

@endsection