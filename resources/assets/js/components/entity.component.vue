<template>
    <div>
        <form id ="entity-form" @submit.prevent="save($event)">
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" class="form-control" v-model="entity.Nombre" placeholder="Entidad" required>
            </div>
            <p>
                <button class="btn btn-success btn-lg" ><i class="fa fa-save"></i> Guardar</button>
            </p>
        </form>
    </div>
</template>

<script> 
    export default {
        props: ['modalData'],
        data() {
            return {
                entity: {}
            }
        }, 
        computed: {
          url() {
              return '/entidades/' + (this.modalData ? this.modalData + "/edit" : "create");
          }
        },
        methods: {
           save(event) {
               LoadButton($(event.target).find('button'));
               if(this.modalData){
                    this.$http.put('/entidades/' + this.modalData,  this.entity).then(
                     response => {
                        Ready();
                        OK("Actualizado");
                        CloseModal();
                        v.Grid();
                    }, error => {
                        Ready();
                        Wrong(DisplayErrors(error));
                    });
                }else{
                    this.$http.post('/entidades',  this.entity).then(
                     response => {
                        Ready();
                        OK("Guardado");
                        CloseModal();
                        v.Grid();
                    }, error => {
                        Ready();
                        Wrong(DisplayErrors(error));
                    });
                }

           }
        }, 
        created(){
            if(this.modalData){
                this.$http.get(this.url).then(response => {
                    this.entity = response.data.entity;
                }, error => {
                    Wrong(DisplayErrors(error));
                });
            }
        }
    }
</script>