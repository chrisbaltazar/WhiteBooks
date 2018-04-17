
@extends('base')

@section('title', 'Lista de entidades')

@section('content')

<div id ="entities">
    @include('modal')
    <div class="form-group">
        <p>
            <button class="btn btn-primary pull-right" 
                   @click="newEntity">
                    <i class="fa fa-user-plus"></i> 
                    Nueva entidad
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
        el: '#entities', 
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
            newEntity(){
                this.modalTitle = "Nueva entidad";
                this.component = "app-entity";
                ModalComponent(this);
            }, 
            View(id){
                this.modalTitle = "Editar entidad";
                this.component = "app-entity";
                this.modalData = id;
                ModalComponent(this);
            }, 
            Delete(id){
                const self = this;
                Ask("¿Seguro de borrar registro?", function(){
                    self.$http.delete('/entidades/' + id).then(response => {
                        OK("Eliminado");
                        self.Grid();
                    }, error => {
                       Wrong(DisplayErrors(error)); 
                    });
                });
            }, 
            Grid() {
                Loading();
                ReloadGrid(grid, '/entidades/show');
            }
        }
    })
</script>

@endsection 
