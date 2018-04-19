
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
            <title>Libros Blancos Gto</title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            
            <meta id ="token" name="csrf-token" content="{{ csrf_token() }}">

            <link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css">
    
            <link rel="stylesheet" type="text/css" href="{{asset('css/font-awesome-4.7.0/css/font-awesome.min.css')}}">
    <!--===============================================================================================-->
            <link rel="stylesheet" type="text/css" href="{{asset('css/fonts/iconic/css/material-design-iconic-font.min.css')}}">
    <!--===============================================================================================-->
            <link rel="stylesheet" type="text/css" href="{{asset('css/animate/animate.css')}}">
    <!--===============================================================================================-->	
            <link rel="stylesheet" type="text/css" href="{{asset('css/css-hamburgers/hamburgers.min.css')}}">
    <!--===============================================================================================-->
            <link rel="stylesheet" type="text/css" href="{{asset('js/animsition/css/animsition.min.css')}}">
    
    <!--===============================================================================================-->
            <link rel="stylesheet" type="text/css" href="{{asset('css/util.css')}}">
            <link rel="stylesheet" type="text/css" href="{{asset('css/main.css')}}">
            <link rel="stylesheet" type="text/css" href="{{asset('css/menu2.css')}}">
            
            <link rel="stylesheet" type="text/css" href="{{asset('js/dhtmlx/dhtmlx.css')}}">
            <link rel="stylesheet" type="text/css" href="{{asset('js/select2/select2.css')}}">
            <link rel="stylesheet" type="text/css" href="{{asset('js/summernote/summernote.css')}}">
            <link rel="stylesheet" type="text/css" href="{{asset('js/sweetalert/dist/sweetalert.css')}}">
            
    <!--===============================================================================================-->
            @yield('style')
    </head>
    <body style="background-image: url('{{ asset('images/bg-01.jpg') }}');">
        
            @if(auth()->check())
                
                
                <div class="row custom">
                    @include('header')
                </div>
                <div class="row custom" id ="main-content">
                    
                        <div class="col-2" style="padding: 0">
                            @include('menu2')
                        </div>
                        <div class="col-10">
                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-primary" id ="section-title">
                                        <h2>@yield('title')</h2>
                                    </div>
                                </div>    
                            </div>
                            <div class="row" style="padding: 15px;">
                                <div class="col-12">
                                @yield('content')
                                </div>
                            </div>
                        </div>
                    
                </div>
            @else
                @yield('content')
            @endif
        
    <!--===============================================================================================-->
            <script src="{{asset('js/app.js')}}"></script>
    <!--===============================================================================================-->
            <script src="{{asset('js/animsition/js/animsition.min.js')}}"></script>
    <!--===============================================================================================-->
<           <script src="{{asset('js/dhtmlx/dhtmlx.js')}}"></script>
<           <script src="{{asset('js/dhtmlx/dhtmlxgrid_export.js')}}"></script>
<           <script src="{{asset('js/select2/select2.js')}}"></script>
            <script src="{{asset('js/summernote/summernote.js')}}"></script>
            <script src="{{asset('js/sweetalert/dist/sweetalert2.all.js')}}"></script>
            <script src="{{asset('js/fn.js')}}"></script>
            
            @yield('script')
          
    </body>
</html>