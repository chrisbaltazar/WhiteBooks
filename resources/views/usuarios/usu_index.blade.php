@extends('base')

@section('title', 'Lista de usuarios')

@section('content')

<div id ="users">
    @include('modal')
    <div class="form-group">
        <p>
            <button class="btn btn-primary mb-3 pull-right" 
                   @click="newUser">
                    <i class="fa fa-user-plus"></i> 
                    Nuevo usuario
            </button>
        </p>
        
    </div>
    <br>
    <table width="100%"  cellpadding="0" cellspacing="0">		
        <tr>
             <td id="pager_grid"></td>
        </tr>
        <tr>
             <td><div id="infopage_grid" style =""></div></td>
        </tr>
        <tr>
             <td><div id="grid" style ="height: 500px; width: 100%"></div></td>
        </tr>
        <tr>
             <td class = "RowCount"></td>
        </tr>
    </table>
</div>

@endsection 


@section('script')

<script> 
    var grid;
    
    var v = new Vue({
        el: '#users', 
        data: {
            modalTitle: "", 
            component: '', 
            modalData: ''
        },
        mounted() {
            {!! setGrid("grid", $params, true) !!} 
            this.Grid();
        }, 
        methods: {
            newUser(){
                this.modalTitle = "Nuevo usuario";
                this.component = "app-user";
                ModalComponent(this, null, function(){
                    MultiSelect($('#cmbSuper'), 'Seleccione', 'Incluidos', true);
                });

            }, 
            View(id){
                this.modalTitle = "Editar usuario";
                this.component = "app-user";
                this.modalData = id;
                ModalComponent(this, null, function(){
                    MultiSelect($('#cmbSuper'), 'Seleccione', 'Incluidos', true);
                });
                
            }, 
            Delete(id){
                const self = this;
                Ask("¿Seguro de borrar registro?", function(){
                    self.$http.delete('/usuarios/' + id).then(response => {
                        OK("Eliminado");
                        self.Grid();
                    }, error => {
                       Wrong(DisplayErrors(error)); 
                    });
                });
            }, 
            Grid() {
                Loading();
                ReloadGrid(grid, '/usuarios/show');
            }
        }
    })
</script>

@endsection 
