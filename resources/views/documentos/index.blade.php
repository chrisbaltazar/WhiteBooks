@extends ('base')

@section ('title', 'Cargar nuevo documento')


@section ('content')


<form id ="uploader" @submit.prevent="uploadFile($event)"> 
    <div class="alert alert-{{ isset($document) ? 'danger' : 'dark' }}">
        @if(isset($document)) 
        <h3 class="">Usted esta cargando una nueva versión del archivo: <br>
            <i class="fa fa-quote-left"></i> {{$document->nombre}} <i class="fa fa-quote-right"></i>
        </h3>
        @else 
        <h3 class="text-center">Usted esta cargando un nuevo archivo</h3>
        @endif
   </div>
    
    <div class="card">
        <div class="card-header bg-success text-white">Datos del documento</div>
        <div class="card-body">
            <div class="form-control">
                <label>Nombre</label>
                <input type="text" class="form-control" v-model="documentName" placeholder="Nombre del documento" {{ isset($document) ? 'disabled' : 'required' }}>
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

<!--<a href ="{{asset('storage/uploaded/eN6nHsMitzPivZgX4AwoN7owQrZlPMHdwNyPD7Q6.pdf')}}">Link</a>-->


@endsection

@section ('script')

<script> 
    var v = new Vue({
        el: '#uploader', 
        data: {
            documentId: '{{ isset($document) ? $document->id : '' }}',
            documentName: '{{ isset($document) ? $document->nombre : "" }}'
        }, 
        methods: {
            uploadFile(event){
               const file = document.querySelector('#file');
               const formData = new FormData();
               formData.append("id", this.documentId);
               formData.append("name", this.documentName);
               formData.append("file", file.files[0]);
               LoadButton($(event.target).find('button'));
               this.$http.post('{{ url('documentos') }}', formData).then(response => {
                   Ready();
                   OK("Guardado");
                   this.documentName = "";
                   this.documentId = "";
                   $('#file').val('');
               }, error => {
                   Ready();
                   Wrong(DisplayErrors(error));
               });
            }
        }
    })
    
</script>

@endsection