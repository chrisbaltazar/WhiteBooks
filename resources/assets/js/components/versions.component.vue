<template>
    <div id="carousel" class="carousel slide" data-ride="carousel" data-interval="false">
        <div class="carousel-inner">
            <div  v-for="(doc, index) in documents"  class="carousel-item" :class ="{active: index == 1}" v-if="index > 0">
                <center> 
                    <a :href ="'/documentos/download/' + doc.id" target ="_blank" title ="Descargar"><i class="fa fa-file-word-o fa-5x d-block w-100"></i></a>
                    <br>
                    <h3><span class="badge badge-pill badge-danger">Versión {{ doc.version }}</span></h3>
                    <h4><span class="badge badge-pill badge-dark">{{ doc.autor.Nombre }} ({{ doc.created_at | dateFormat(true) }})</span></h4>
                </center>
            </div>
        </div>
        <a class="carousel-control-prev" href="#carousel" role="button" data-slide="prev">
            <button class="btn btn-default"><i class="fa fa-arrow-circle-o-left"></i> Anterior</button>
        </a>
        <a class="carousel-control-next" href="#carousel" role="button" data-slide="next">
            <button class="btn btn-default">Siguiente <i class="fa fa-arrow-circle-o-right"></i></button>
        </a>
    </div>
</template>

<script> 
    export default {
        props: ['modalData'], 
        data () {
            return {
                documents: []
            }
        },
        created() {
            this.$http.get('/historial/' + this.modalData.documento_id + '/' + this.modalData.tipo).then(response => {
                this.documents = response.data.documents;
                this.path = response.data.path;
            }, error => {
               Wrong(DisplayErrors(error)); 
            });
        }
    }
        
</script>