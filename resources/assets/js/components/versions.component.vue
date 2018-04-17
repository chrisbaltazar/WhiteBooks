<template>
    <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <div  v-for="(doc, index) in documents"  class="carousel-item" :class ="{active: index == 0}" v-if="index > -1">
                    <center> 
                        <i class="fa fa-file-word-o fa-5x d-block w-100"></i> 
                        <h3><span class="badge badge-pill badge-dark">{{ doc.autor.Nombre }} ({{ doc.created_at | dateFormat }})</span></h3>
                    </center>
                </div>
            </div>
            <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="sr-only">Next</span>
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
            this.$http.get('/historial/' + this.modalData).then(response => {
                this.documents = response.data.documents;
            }, error => {
               Wrong(DisplayErrors(error)); 
            });
        }
    }
        
</script>