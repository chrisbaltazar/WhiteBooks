@extends ('base')

@section ('title', 'Reporte de avance')

@section ('style') 
<style type="text/css">
    
</style>
@endsection

@section ('content')
<div id ="advance">
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

@section ('script')

<script>
    var grid;
    
    const v = new Vue({
        el: '#advance', 
        methods: {
            Grid() {
                Loading();
                ReloadGrid(grid, '/avance/show');
            }
        },
        mounted() {
            {!! setGrid("grid", $params, true) !!} 
            this.Grid();
        }, 
    })
</script>

@endsection 
