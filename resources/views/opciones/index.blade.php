

@extends('base')

@section('title', 'Opciones de sistema')

@section('content')

<div id ="options" class="row">
    <form class="col-6 offset-3" @submit.prevent ="changePwd($event)">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h3 class="card-title ">Cambio de contraseña</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Nueva contraseña</label>
                    <input type="password" class="form-control" placeholder="Ingrese nueva contraseña" v-model="user.password" required>
                </div>
                <div class="form-group">
                    <label>Repita contraseña</label>
                    <input type="password" class="form-control" placeholder="Confirme nueva contraseña" v-model="user.password_confirmation" required>
                </div>
                <p>
                <button class="btn btn-lg btn-dark"><i class="fa fa-lock"></i> Aceptar</button>
            </p>
            </div>
            
        </div>
        
    </form>
</div>

@endsection 


@section('script')

<script> 
    var v = new Vue({
        el: '#options', 
        data: {
            user: {
                password: '',
                password_confrmation: ''
            }
        }, 
        methods: {
            changePwd(event){
                LoadButton($(event.target).find('button'));
                this.$http.put('/opciones/{{ auth()->user()->id }}', this.user).then(response => {
                    Ready();
                    OK("Contraseña cambiada");
                    this.user.newPassword = "";
                    this.user.newPassword_confrmation = "";
                }, error => {
                    Ready();
                    Wrong(DisplayErrors(error));
                })
            }
        }
        
    })
</script>

@endsection 
