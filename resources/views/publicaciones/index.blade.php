@extends ('base')

@section ('title', 'Publicación de documentos')

@section ('style') 
<style type="text/css">
    .file-selected {color: #3333ff;}
</style>

@endsection

@section ('content')
<div id ="publisher">
   
    
    <div class="row" >
        <div class="col-12">
            <div class="form-group">
                <v-select v-model = "entity" label = "Nombre" :options="entities"></v-select>
            </div>
            
            <div class="card">
                <div class="card-header bg-dark text-white"><h5>Lista de documentos validados</h5></div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead class="thead-dark">
                        <th>#</th>
                        <th>Documento</th>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Estatus</th>
                        <th>Ver</th>
                        <th>Resumen</th>
                        <th>Publicar</th>
                        </thead>
                        <tr v-for="(doc, index) in documents" :class="{'table-warning': Number.isInteger(selected) && index == selected}">
                            <td>@{{ index+1 }}</td>
                            <td>@{{ doc.nombre }}</td>
                            <td>@{{ doc.created_at | dateFormat}}</td>
                            <td>@{{ doc.usuario.Nombre }}</td>
                            <td><h5><span class="badge" :class = "{'badge-primary': doc.estatus.id == 1, 'badge-success': doc.estatus.id == 2}">@{{ doc.estatus.nombre }}</span></h5></td>
                            <td><button class="btn btn-warning" @click="view(doc.id, index)">Ver</button></td>
                            <td >
                                <template v-if="doc.estatus_id == 1">
                                     <i class="fa fa-file-pdf-o fa-2x" @click="uploadResume(index)" :class ="{'file-selected': doc.resumen}"></i>
                                     <input type="file" class ="select-file" accept=".pdf" @change="fileSelected(index)" style="display: none">
                                </template>
                                <template v-else>
                                    <a class="btn btn-link" :href="'/publicacion/download/' + doc.id" ><i class="fa fa-download"></i> Descargar</a>
                                </template>
                            </td>
                            <td >
                                <button class="btn btn-success publish" @click="publishDocument(doc.id, index)" v-if="doc.estatus_id == 1" :disabled = "!doc.resumen"><i class="fa fa-send"></i> Publicar</button>
                                <i class="fa fa-check-circle fa-2x" v-else></i>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row" v-if="current_version.id">
        <div class="col-6 offset-3">
            <a class="btn btn-link btn-lg float_left" :href="'/documentos/download/' + current_version.id" target="_blank"><i class="fa fa-download"></i> Descargar documento</a>
            <iframe id ="viewer" :src="document_path" style="width:100%; height:500px;" frameborder="0"></iframe>
        </div>
    </div>
</div>

@endsection 

@section ('script')

<script>
    const v = new Vue({
        el: '#publisher', 
        data: {
            entity: '', 
            entities: [],
            documents: [], 
            selected: '',
            current_version: {ruta: '' },
            baseUrl: '{{  url("/") }}',
            viewerUrl : "http://docs.google.com/gview?embedded=true&url="
        }, 
        watch: {
          entity () {
              if(this.entity)
                this.loadDocuments();
              else
                this.documents = [];
          }  
        },
        methods: { 
            loadDocuments () {
                this.$http.get('/publicacion/' + this.entity.id).then(response => {
                    this.documents = response.data.documents;
                }, error => {
                   Wrong(DisplayErrors(error));
                });
            },
            view(id, index) {
               this.$http.get('/documentos/' + id).then(response => {
                   this.current_version = response.data.version;
                   this.selected = index;
               }, error => {
                   Wrong(DisplayErrors(error));
               });
            }, 
            uploadResume(index){
                $('.select-file').eq(index).click();
            }, 
            fileSelected(index){
                this.documents[index].resumen = true;
            },
            publishDocument(id, index){
               LoadButton($('.publish').eq(index));
               const file = document.getElementsByClassName('select-file').item(index);
               const formData = new FormData();
               formData.append("id", id);
               formData.append("file", file.files[0]);
               this.$http.post('/publicacion', formData).then(response => {
                   OK("Publicado");
                   this.loadDocuments();
                   this.selected = "";
               }, error => {
                   Ready();
                   Wrong(DisplayErrors(error));
               })
            }
        },
        computed: {
            document_path () {
                return this.viewerUrl + this.baseUrl + this.current_version.ruta;
            }
        },
        created() {
            this.$http.get('/historial/create').then(response => {
                this.entities = response.data.entities;
                if(this.entities.length == 1)
                    this.entity = this.entities[0];
            }, error => {
                Wrong(DisplayErrors(error));
            })
        }
    })
</script>

@endsection 
