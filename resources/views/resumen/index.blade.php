
@extends ('base')

@section ('title', 'Resumen de documentos')

@section ('style') 

<style type="text/css">
 
</style>

@endsection

@section ('content')
<div id ="resume">
    <table class="table table-striped" id="tbl-docs">
        <thead class="">
        <th colspan="4"></th>
        <th colspan="3" class="text-center table-primary">Versión extendida</th>
        <th colspan="3" class="text-center table-success">Versión ejecutiva</th>
        <th></th>
        </thead>
        <thead class="thead-dark">
        <th>#</th>
        <th>Entidad</th>
        <th>Documento</th>
        <th>Estatus</th>
        <th>Subversión</th>
        <th>Word</th>
        <th>PDF</th>
        <th>Subversión</th>
        <th>Word</th>
        <th>PDF</th>
        <th>Reiniciar</th>
        </thead>
        <tr v-for="(doc, index) in documents">
            <td>@{{ index + 1 }}</td>
            <td>@{{ doc.entidad.Nombre }}</td>
            <td>@{{ doc.nombre }}</td>
            <td>@{{ doc.estatus.nombre }}</td>
            <td class="text-center table-primary">@{{ doc.extended.version }}</td>
            <td class="text-center table-primary">
                <a :href="'/documentos/download/' + doc.extended.id"><i class="fa fa-2x fa-file-word-o"></i></a>
            </td>
            <td class="text-center table-primary">
                <a v-if="doc.final" :href="'/resumen/download/' + doc.id + '/extended'"><i class="fa fa-2x fa-file-pdf-o"></i></a>
            </td>
            <td class="text-center table-success">@{{ doc.executive ? doc.executive.version : '' }}</td>
            <td class="text-center table-success">
                <a v-if="doc.executive" :href="'/documentos/download/' + doc.executive.id"><i class="fa fa-2x fa-file-word-o"></i></a>
            </td>
            <td class="text-center table-success">
                <a v-if="doc.resumen" :href="'/resumen/download/' + doc.id + '/executive'"><i class="fa fa-2x fa-file-pdf-o"></i></a>
            </td>
            <td v-if="isAdmin" class="text-center"><i class="fa fa-reply fa-2x" @click="reset(doc.id)"></i></td>
        </tr>
    </table>
</div>

@endsection 

@section ('script')

<script>
    const v = new Vue({
        el: '#resume', 
        data: {
           isAdmin: {{ auth()->user()->isAdmin() ? "1" : "0" }},
           documents: []
        }, 
        methods: { 
           loadDocuments() {
               this.$http.get('/resumen/load').then(response => {
                    this.documents = response.data.documents;
                }, error => {
                    Wronr(DisplayErrors(error));
                })
           },
           reset(id) {
               Ask("¿Desea reiniciar el proceso de este documento?", () => {
                  this.$http.put('/resumen/reset', {id: id}).then(response => {
                      OK("Listo");
                      this.loadDocuments();
                  }, error => {
                      Wrong(DisplayErrors(error));
                  }) 
               });
           }
        },
        computed: {
            
        },
        created() {
            this.loadDocuments();
        }, 
        updated() {
            DataTable($('#tbl-docs'));
        }
        
    })
</script>

@endsection 
