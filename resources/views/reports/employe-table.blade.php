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
    <form id="rental-form" action="/report/employes" method="post">
        {!! csrf_field() !!}
        <div class="row">
        @if (!Auth::guest() && !Auth::user()->kiosk_id)
            <div class="form-group col-md-4">
                <label for="kiosks">Quiosque operado:</label>
                <select name="kiosk_id" class="form-control" id="kiosks">
                </select>
            </div>
        @else
            <input type="hidden" name="kiosk_id" value="{{ Auth::user()->kiosk_id }}">
        @endif
            <div class="form-group col-md-4">
                <label for="init">Data de:</label>
                <input type='text' name="init" value="{{ $input?$input['init']:\Carbon\Carbon::now()->format('d/m/Y') }}" class="form-control datepicker" id='init'/>
            </div>
            <div class="form-group col-md-4">
                <label for="end">Data até:</label>
                <input type='text' name="end" value="{{ $input?$input['end']:\Carbon\Carbon::now()->format('d/m/Y') }}" class="form-control datepicker" id='end'/>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <button class="btn btn-primary col-md-12">Gerar relatório</button>
            </div>
            @if($rentals_total)
                <div class="col-md-8 text-right">
                    <h4>Total de aluguéis no período </h4>
                    <h2>{{ $rentals_total }} </h2>
                </div>
            @endif
        </div>

    </form>
</div>
    <div class="row">
    <table class="table">
        <thead>
        <tr>
            <th class="text-center">Funcionário</th>
            <th class="text-center">Quantidade</th>
        </tr>
        </thead>
        <tbody id="rental-body">
        @if($rentals)
            @foreach($rentals as $rental)
            <tr class="text-center" id="{{$rental->id}}"> 
                <td>{{ $rental->employe->name }}</td> 
                <td>{{ $rental->qtd }}</td>
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
        $('.datepicker').datepicker({ dateFormat: 'dd/mm/yy' });
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
        })
        .fail(function(xhr, status, error) {
            showError(error, status, xhr);
        });
    }

    function kioskResponse(data){
        try{
            if(data.length > 0){
                $.each(data, function(index, value){
                    try{
                        $("#kiosks").append("" +
                        "<option " +
                            (value.id == {!! $input?$input['kiosk_id']:0 !!} ? "selected" : "") + 
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