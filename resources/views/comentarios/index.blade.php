@extends ('base')

@section ('title', 'Seguimiento de documentos')

@section ('style') 
<style type="text/css">
    #comment-list {
        height: 450px; 
        overflow: auto;
    }
</style>

@endsection

@section ('content')
<div id ="history">
    
    @include('modal')
    
    <div class="row" >
        <div class="col-12">
            <div class="form-group" v-show="{{ auth()->user()->isUser() ? "false" : "true" }}">
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
                        <th>Estatus</th>
                        <th>Versión extendida</th>
                        <th>Versión ejecutiva</th>
                        <th>Archivo</th>
                        <th>Borrar</th>
                        </thead>
                        <tr v-for="(doc, index) in documents" :class="{'table-warning': Number.isInteger(selected) && index == selected}">
                            <td>@{{ index+1 }}</td>
                            <td>@{{ doc.nombre }}</td>
                            <td>@{{ doc.created_at | dateFormat(true) }}</td>
                            <td><h3><span class="badge" :class = "getEstatusDocument(doc.estatus_id)">@{{ doc.estatus.nombre }}</span></h3></td>
                            <td>
                                <button class="btn btn-warning" @click="viewDoc(doc, index, 'extended')">Ver docto.</button>
                            </td>
                            <td>
                                <button class="btn" :class="getButtonClass(doc)" v-text="getButtonText(doc)" @click="viewDoc(doc, index, 'executive')"></button>
                            </td>
                            <td>
                                <a class="btn btn-danger" :class = "{'disabled': doc.estatus_id == 3}" :href ="'/documentos/change/' + doc.id + '/edit'" v-text="getAction(doc)"></a>
                            </td>
                            <td>
                                <i v-if="doc.created_by == user.id" class="fa fa-trash-o fa-2x" @click="deleteDoc(doc.id, index)"></i>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row" v-if="Number.isInteger(selected)">
        <div class="col-12 text-center">
            <h3 class="mb-5"><span class="badge badge-pill badge-danger">
                Detalles de versión @{{ this.current_version.tipo == 'extended' ? "extendida" : 'ejecutiva' }}
                "@{{ documents[selected].nombre }}"
            </span></h3>
        </div>
        
        <div class="col-5">
            <a class="btn btn-link btn-lg float_left" :href="'/documentos/download/' + current_version.id" target="_blank"><i class="fa fa-download"></i> Descargar documento</a>
            <button class="btn btn-info btn-sm float_right" @click="explore" v-if="current_version.version > 1">Explorar subversiones</button>

            <iframe id ="viewer" :src="document_path" v-if="current_version.id" style="width:100%; height:450px;" frameborder="0"></iframe>
            <div class="text-center alert" :class ="document_class">
                <h4 v-text="document_status" class="text-center"></h4>
                
                <button v-if="documents[selected].estatus_id == 0 && user.isReviewer && current_version.tipo == 'extended'" class="btn btn-primary btn-lg mt-3" @click="manageDoc('review')" ><i class="fa fa-check"></i> Vo.Bo. revisor</button>
                <button v-if="documents[selected].estatus_id == 1 && user.isSuper && current_version.tipo == 'extended'" class="btn btn-success btn-lg mt-3" @click="manageDoc('validate')" ><i class="fa fa-check-square-o"></i> Validar contenido</button>
                <button v-if="documents[selected].estatus_id == 2 && user.isPublisher && current_version.tipo == 'executive'" class="btn btn-danger btn-lg mt-3" @click="manageDoc('publish')" ><i class="fa fa-check-circle-o"></i> Validar formato final</button>
                
            </div>
        </div>
        <div class="col-7">
            <div class="form-group" v-if="canMakeComment">
                <h4><span class="badge badge-light">Agregar nuevo comentario</span></h4>
                <textarea class="form-control" placeholder="Observaciones al documento" v-model="newComment.contenido"></textarea>
                <button class="btn btn-primary btn-sm mt-2" @click="addComment"><i class="fa fa-plus"></i> Guardar</button>
            </div>
               
             <div class="card" :class = "{'mt-5': !canMakeComment}">
                 <div class="card-header bg-success text-white"><h5>Historial de comentarios</h5></div>
                <div class="card-body" id ="comment-list">
                    <table class="table table-condensed table-bordered">
                        <template  v-for="(comment, i) in comments" v-if="comment.version.tipo == current_version.tipo">
                            <tr  :class = "{'table-primary': comment.autor.rol_id == 5, 'table-danger': comment.autor.rol_id < 5}"> 
                                <td><strong>@{{ comment.autor.Nombre }}</strong> (<small>@{{ comment.autorRol }}</small>)</td>
                                <td width = "130">@{{ comment.created_at | dateFormat }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-justify">@{{ comment.contenido }}</td>
                            </tr>
                        </template>
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
            user: {
                id: {{ auth()->user()->id }},
                isReviewer: {{ auth()->user()->isReviewer() ? "1" : "0" }}, 
                isSuper: {{ auth()->user()->isSuper() ? "1" : "0" }}, 
                isPublisher: {{ auth()->user()->isPublisher() ? "1" : "0" }}
            },
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
            baseUrl: '{{  asset("/storage/") }}',
            viewerUrl : "http://docs.google.com/gview?embedded=true&url="
        }, 
        watch: {
          entity () {
              this.selected = "";
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
            getEstatusDocument(stat){
                switch(stat){
                    case 0:
                        return "badge-secondary"; 
                        break;
                    case 1:
                        return "badge-info"; 
                        break;
                    case 2:
                        return "badge-primary"; 
                        break;
                    case 3:
                        return "badge-success"; 
                        break;
                        
                }
            },
            getButtonText(doc){
                if(doc.hasResume){
                    return "Ver docto.";
                }else if(doc.estatus_id <= 2){
                    return "Pendiente";
                }else{
                    return "Cargar";
                }
            },
            getButtonClass(doc) {
                if(doc.estatus_id < 2){
                    return "btn-light";
                }else if(!doc.hasResume){
                    return "btn-secondary";
                }else{
                    return "btn-warning";
                }
            },
            getAction(doc) {
              if(doc.estatus_id >= 2 && !doc.hasResume){
                  return "Cargar"; 
              }else{
                  return "Cambiar";
              }
            },
            viewDoc(doc, index, type) {
               if(type == 'extended' || doc.estatus_id >= 2 || doc.hasResume){
                    this.$http.get('/documentos/show/' + doc.id + '/' + type).then(response => {
                        this.selected = index;
                        this.current_version = response.data.version;
                        this.comments = response.data.comments;    
                        this.newComment = {
                            version_id: this.current_version.id, 
                            contenido: ""
                        }
                    }, error => {
                        Wrong(DisplayErrors(error));
                    });
                }else{
                    Warning("Espere a cargar este archivo durante el proceso");
                }
               
            }, 
            addComment() {
                this.$http.post('/historial', this.newComment).then(response => {
                    Ready();
                    this.loadDocuments();
                    this.viewDoc(this.documents[this.selected], this.selected, this.current_version.tipo);
                }, error => {
                    Wrong(DisplayErrors(error));
                })
            }, 
            explore () {
                this.component = "app-versions";
                this.modalTitle = "Versiones del documento";
                this.modalData = this.current_version;
                ModalComponent(this);
            }, 
            deleteDoc(id, index){
                Ask("¿Seguro de eliminar este documento?", () => {
                   this.$http.delete('/documentos/' + id).then(response => {
                       this.documents.splice(index, 1);
                       if(index == this.selected){
                           this.current_version = {}
                           this.selected = "";
                       }
                       OK("Borrado");
                   }, error => {
                       Wrong(DisplayErrors(error));
                   }) 
                });
            }, 
            manageDoc(action) {
              let question, message;
              switch(action){
                  case "review":
                      question = "¿Desear emitir el Vo.Bo. de este documento?";
                      message = "Revisado";
                      break;
                  case "validate":
                      question = "¿Desea validar este documento?";
                      message = "Validado";
                      break;
                  case "publish":
                      question = "¿Desea concluir con este documento?";
                      message = "Concluido";
                      break;
              }  
              Ask(question, () => {
                Loading();
                this.$http.put('/historial/' + this.current_version.documento_id + '/' + action).then(response => {
                    this.loadDocuments();
                    if(action != 'review'){
                        this.$http.put('/documentos/update/' + this.current_version.id).then(response => {
                            Ready();
                            OK(message);
                        }, error => {
                            Wrong(DisplayErrors(error));
                        })
                    }else{
                        Ready();
                        OK(message);
                    }
                }, error => {
                    Wrong(DisplayErrors(error));
                })
              })
            }
        },
        computed: {
            document_path () {
                return this.viewerUrl + this.baseUrl + "/" + this.current_version.ruta;
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
                    case 3: 
                        return "alert-danger";
                        break;
                    
                }
            }, 
            document_status () {
                switch (this.documents[this.selected].estatus_id){
                    case 0: 
                        return "Documento en revisión";
                        break;
                    case 1: 
                        return "Documento en espera de validación de contenido";
                        break;
                    case 2: 
                        return "Documento en espera de validación de formato";
                        break;
                    case 3: 
                        return "Documento concluido";
                        break;
                }
            }, 
            canMakeComment () {
                return (this.documents[this.selected].estatus_id < 2 || (this.current_version.tipo == 'executive' && this.documents[this.selected].estatus_id < 3));
            }
        },
        created() {
            this.$http.get('/entidades/create').then(response => {
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
