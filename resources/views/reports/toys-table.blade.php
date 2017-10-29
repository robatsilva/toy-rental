@extends('layouts.app')

@section('content')
<style>
    tbody tr:hover{
        background-color: #DDD;
    }

    td{
        vertical-align: middle !important;
    }
</style>
<div class="container">
<div class="row">
    <form id="rental-form" action="/report/toys" method="post">
        {!! csrf_field() !!}
        <div class="row">
            <div class="form-group col-md-4">
                    <label for="kiosks">Quiosque operado:</label>
                    <select name="kiosk_id" class="form-control" id="kiosks">
                    </select>
                </div>
            <div class="col-md-4">
                <label for="init">Data de:</label>
                <input type='text' name="init" value="{{ $input?$input['init']:'' }}" class="form-control" id='init'/>
            </div>
            <div class="col-md-4">
                <label for="end">Data até:</label>
                <input type='text' name="end" value="{{ $input?$input['end']:'' }}" class="form-control" id='end'/>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <button class="btn btn-primary col-md-12">Gerar relatório</button>
            </div>
        </div>

    </form>
</div>
    <div class="row">
    <table class="table">
        <thead>
        <tr>
            <th class="text-center">Brinquedo</th>
            <th class="text-center">Tempo total de uso (horas)</th>
            <th class="text-center">Valor total (R$)</th>
        </tr>
        </thead>
        <tbody id="rental-body">
        @if($rentals)
            @foreach($rentals as $rental)
            <tr class="text-center" id="{{$rental->id}}"> 
                <td>{{ $rental->toy->description }}</td> 
                <td>{{ $rental->total_time }}</td>
                <td>{{ $rental->total_pay }}</td>
            </tr>       
            @endforeach
        @endif
        </tbody>
    </table>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        $('#init').datetimepicker({
            format: 'DD/MM/YYYY'
        });
        $('#end').datetimepicker({
            format: 'DD/MM/YYYY'
        });
        
        //loaders
        initLoaders();
    });

    ////////////////////////Loaders
    function initLoaders(){
        //loadPeriods();
        loadKiosks();
    }

    function loadKiosks()
    {
        showLoader();
        $.get("/kiosk/list", function(data){
            hideLoader();
            kioskResponse(data);
            loadToys();
        });
    }

    function kioskResponse(data){
        try{
            if(data.length > 0){
                $.each(data, function(index, value){
                    try{
                        $("#kiosks").append("" +
                        "<option " +
                            (value.default?"selected":"") + 
                            " data-value='" + JSON.stringify(value) + "'" + 
                            " value=" + value.id +">" +
                            value.name + 
                        "</option>");
                    }
                    catch(error){
                        console.log(error);
                    }
                });
                hideLoader();
            }
        }
        catch(error){console.log(error);}
    }
</script>
@endsection