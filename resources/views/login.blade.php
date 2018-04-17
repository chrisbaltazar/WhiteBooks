@extends('base')

@section('content')

<div class="limiter" id ="login-form">
    <div class="container-login100" >
            <div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-54">
                    <form class="login100-form validate-form" method="post" action="/login">
                            {{csrf_field()}}

                            <img src="{{asset('images/logogto.png')}}">
                            <h3 class="text-center">Sistema de Libros Blancos del Estado de Guanajuato</h3>
                            <hr>
                            <span class="login100-form-title p-b-49">
                                    Login
                            </span>
                            
                            <div class="wrap-input100 validate-input m-b-23" data-validate = "Username is reauired">
                                    <span class="label-input100">Correo electrónico</span>
                                    <input class="input100" type="email" name="username" placeholder="Ingresa tu correo" requiered>
                                    <span class="focus-input100" data-symbol="&#xf206;"></span>
                            </div>

                            <div class="wrap-input100 validate-input" data-validate="Password is required">
                                    <span class="label-input100">Password</span>
                                    <input class="input100" type="password" name="pass" placeholder="Ingresa tu contraseña" required>
                                    <span class="focus-input100" data-symbol="&#xf190;"></span>
                            </div>

                            <div class="text-right p-t-8 p-b-31">
                                    <a href="#">
                                            Recuperar contraseña
                                    </a>
                            </div>

                            <div class="container-login100-form-btn">
                                    <div class="wrap-login100-form-btn">
                                            <div class="login100-form-bgbtn"></div>
                                            <button class="login100-form-btn">
                                                    Entrar
                                            </button>
                                    </div>
                            </div>
                            @include('errors')
                    </form>
            </div>
    </div>
</div>

@endsection 

@section('script')

<script>
    new Vue({
        el: '#login-form', 
        methods: {
            login() {
                location.href = "/home";
            }
        }
    })
</script>

@endsection