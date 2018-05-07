<template>
    <div>
        <form id ="user-form" @submit.prevent="saveUser($event)">
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" class="form-control" v-model="user.Nombre" required>
            </div>
            <div class="form-group">
                <label>Correo</label>
                <input type="email" class="form-control" v-model="user.Correo" required>
            </div>
            <div class="form-group" v-if="!user.id || changePwd">
                <label>Password</label>
                <input type="password" class="form-control" v-model="user.Password" required>
            </div>
            <div class="form-group" v-if="!user.id || changePwd">
                <label>Confirme</label>
                <input type="password" class="form-control" v-model="user.Password_confirmation" required>
            </div>
            <div class="form-group">
                <label>Entidad</label>
                <select class="form-control" v-model="user.entidad_id" required>
                    <option value="">Seleccione</option> 
                    <option v-for="entidad in entidades" :value="entidad.id">{{ entidad.Nombre }}</option>
                </select>
            </div>
            <div class="form-group">
                <label>Rol</label>
                <select class="form-control" v-model="user.rol_id" required>
                    <option value="">Seleccione</option> 
                    <option v-for="rol in roles" :value="rol.id">{{ rol.NombreRol }}</option>
                </select>
            </div>
            <div class="form-group" v-show="user.rol_id == 3">
                <label>Supervisa</label>
                <select id ="cmbSuper"  multiple v-model="user.entidades"  :required = "user.rol_id == 3">
                    <option v-for="entitiy in entidades" :value="entitiy.id">{{ entitiy.Nombre }}</option>
                </select>
            </div>
             <div class="form-group" v-if="user.rol_id == 4">
                <label>Depende de</label>
                <select class="form-control" v-model="user.padre_id" required>
                    <option value="">Seleccione</option> 
                    <option v-for="a in all" :value="a.id">{{ a.Nombre }}</option>
                </select>
            </div>
            <p>
                <button class="btn btn-success btn-lg" ><i class="fa fa-save"></i> Guardar</button>
                <button class="btn btn-info btn-lg" v-if="user.id && !changePwd" @click.prevent="changePwd = true"><i class="fa fa-lock"></i> Cambiar contraseña</button>
            </p>
        </form>
        
    </div>
</template>

<script> 
    
    export default {
        props: ['modalData'],
        data() {
            return {
                user: { 
                    entidades: []
                },
                roles: [], 
                entidades: [], 
                all: [], 
                changePwd: false
            }
        }, 
        computed: {
          url() {
              return '/usuarios/' + (this.modalData ? this.modalData + "/edit" : "create");
          }
        },
        methods: { 
           saveUser(event) {
                const rel = $('#cmbSuper').val();
                console.log(rel);
                this.user.entidades = rel;
                console.log(this.user);
                 
               LoadButton($(event.target).find('button:first'));
               if(this.modalData){
                    this.$http.put('/usuarios/' + this.modalData,  this.user).then(
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
                    this.$http.post('/usuarios',  this.user).then(
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
            this.$http.get(this.url).then(response => {
                if(this.modalData){
                    this.user = response.data.user;
                    this.user.entidades = response.data.relations;
                }
                this.roles = response.data.roles;
                this.entidades = response.data.entidades;
                this.all = response.data.all;
            }, error => {
                Wrong(DisplayErrors(error));
            });
        }
    }
</script>