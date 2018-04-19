@extends ('base')

@section ('title', 'Seguimiento de documentos')

@section ('style') 
<style type="text/css">
    #comment-list {
        height: 500px; 
        overflow: auto;
    }
</style>

@endsection

@section ('content')
<div id ="history">
    
    @include('modal')
    
    <div class="row" >
        <div class="col-12">
            <div class="form-group">
                <v-select v-model = "entity" label = "Nombre" :options="entities"></v-select>
            </div>
            
            <div class="card">
                <div class="card-header bg-dark text-white"><h5>Lista de documentos en revisión</h5></div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead class="thead-dark">
                        <th>#</th>
                        <th>Documento</th>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Estatus</th>
                        <th>Ver</th>
                        <th>Cambiar</th>
                        <th>Borrar</th>
                        </thead>
                        <tr v-for="(doc, index) in documents" :class="{'table-warning': Number.isInteger(selected) && index == selected}">
                            <td>@{{ index+1 }}</td>
                            <td>@{{ doc.nombre }}</td>
                            <td>@{{ doc.created_at | dateFormat}}</td>
                            <td>@{{ doc.usuario.Nombre }}</td>
                            <td><h5><span class="badge" :class = "{'badge-secondary': doc.estatus.id == 0, 'badge-primary': doc.estatus.id == 1, 'badge-success': doc.estatus.id == 2}">@{{ doc.estatus.nombre }}</span></h5></td>
                            <td><button class="btn btn-warning" @click="view(doc.id, index)">Ver</button></td>
                            <td><a class="btn btn-danger" :href ="'/documentos/' + doc.id + '/edit'">Cambiar</a></td>
                            <td><i class="fa fa-trash-o fa-2x" @click="deleteDoc(doc.id, index)"></i></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row" v-if="current_version.id">
        <div class="col-6">
            <a class="btn btn-link btn-lg float_left" :href="'/documentos/download/' + current_version.id" target="_blank"><i class="fa fa-download"></i> Descargar documento</a>
            <button class="btn btn-info btn-sm float_right" @click="explore" v-if="current_version.version > 1">Explorar versiones</button>

            <iframe id ="viewer" :src="document_path" v-if="current_version.id" data-section-id="1" style="width:100%; height:500px;" frameborder="0"></iframe>
            <div class="alert" :class ="document_class">
                <h3 v-text="document_status"></h3>
                @if (auth()->user()->validate)
                <button class="btn btn-success btn-lg" @click="validate" v-if="documents[selected].estatus_id == 0"><i class="fa fa-check"></i> Validar documento</button>
                @endif 
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <h4><span class="badge badge-light">Agregar nuevo comentario</span></h4>
                <textarea class="form-control" placeholder="Observaciones al documento" v-model="newComment.contenido"></textarea>
                <button class="btn btn-primary btn-sm" @click="addComment"><i class="fa fa-plus"></i> Guardar</button>
            </div>
               
             <div class="card">
                <div class="card-header bg-success text-white">Comentarios</div>
                <div class="card-body" id ="comment-list">
                    <table class="table table-condensed table-bordered">
                        <thead class="thead-light">
                        <th width = "100">Fecha</th>
                        <th width = "200">Usuario</th>
                        <th>Comentario</th>
                        </thead>
                        <tr v-for="(comment, i) in comments" :class = "{'table-primary': comment.autor.rol_id == 5, 'table-danger': comment.autor.rol_id < 5}">
                            <td>@{{ comment.created_at | dateFormat }}</td>
                            <td>@{{ comment.autor.Nombre }}</td>
                            <td>@{{ comment.contenido }}</td>
                        </tr>
                    </table>
                </div>
             </div>
        </div>
    </div>
</div>

@endsection 

@section ('script')

<script>
    const v = new Vue({
        el: '#history', 
        data: {
            component: '', 
            modalData: '', 
            modalTitle: '',
            entity: '', 
            entities: [],
            documents: [], 
            selected: '',
            current_version: {ruta: '' },
            newComment: { },
            comments: [],
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
                this.$http.get('/documentos/load/' + this.entity.id).then(response => {
                    this.documents = response.data.documents;
                }, error => {
                   Wrong(DisplayErrors(error));
                });
            },
            view(id, index) {
               this.$http.get('/documentos/' + id).then(response => {
                   this.current_version = response.data.version;
                   this.comments = response.data.comments;
                   this.selected = index;
                   this.newComment = {
                       version_id: this.current_version.id, 
                       contenido: ""
                   }
               }, error => {
                   Wrong(DisplayErrors(error));
               });
               
            }, 
            addComment(event) {
                LoadButton($(event.target).find('button'));
                this.$http.post('/historial', this.newComment).then(response => {
                    Ready();
                    this.loadDocuments();
                    this.comments.unshift({
                        created_at: moment().format('L'), 
                        autor: {Nombre: '{{ auth()->user()->Nombre }}'}, 
                        contenido: this.newComment.contenido 
                    });
                    this.newComment.contenido = "";
                }, error => {
                    Wrong(DisplayErrors(error));
                })
            }, 
            explore () {
                this.component = "app-versions";
                this.modalTitle = "Versiones del documento";
                this.modalData = this.current_version.documento_id;
                ModalComponent(this);
            }, 
            deleteDoc(id, index){
                Ask("¿Seguro de eliminar este documento?", () => {
                   this.$http.delete('/documentos/' + id).then(response => {
                       this.documents.splice(index, 1);
                       if(index == this.selected) 
                           this.current_version = {}
                       OK("Borrado");
                   }, error => {
                       Wrong(DisplayErrors(error));
                   }) 
                });
            }, 
            validate() {
                Ask("¿Desea validar este documento?", () => {
                    this.$http.put('/historial/' + this.current_version.documento_id).then(response => {
                        this.loadDocuments();
                        OK("Validado");
                    }, error => {
                        Wrong(DisplayErrors(error));
                    })
                })
            }
        },
        computed: {
            document_path () {
                return this.viewerUrl + this.baseUrl + this.current_version.ruta;
            }, 
            document_class () {
                switch (this.documents[this.selected].estatus_id){
                    case 0: 
                        return "alert-secondary";
                        break;
                    case 1: 
                        return "alert-info";
                        break;
                    case 2: 
                        return "alert-success";
                        break;
                    
                }
            }, 
            document_status () {
                switch (this.documents[this.selected].estatus_id){
                    case 0: 
                        return "Documento en proceso de validación";
                        break;
                    case 1: 
                        return "Documento validado";
                        break;
                }
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
        }, 
        mounted() {
//            DoSelectVue($('.select2'), (e) => {
//                console.log(e);
//                this.entity = e.val;
////                alert("CHANGED");
//            });
        }
        
    })
</script>

@endsection 
