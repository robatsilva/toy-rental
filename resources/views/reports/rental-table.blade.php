@extends('layouts.app')

@section('content')
<style>
    tbody tr:hover{
        background-color: #DDD;
    }

    td{
        vertical-align: middle !important;
    }

    .Cancelado{
        background: #FAA;
    }

    #royalty{
        color: red;
    }
</style>
<div class="container">
<div class="row">
    <form id="rental-form" action="/report" method="post">
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
            @if($resume)
                <div class="col-md-8 text-right">
                    <h4>Total no período </h4>
                    <h2>R$ @currency($resume) </h2>
                    @if (!Auth::guest() && Auth::user()->royalty == '1')
                        <h5 id="royalty"> Royalties R$ @currency($royalties) </h5>
                    @endif
                </div>
            @endif
        </div>

    </form>
</div>
    <div class="row">
    <table class="table">
        <thead>
        <tr>
            <th class="text-center">Data</th>
            <th class="text-center">Brinquedo</th>
            <th class="text-center">Cliente</th>
            <th class="text-center">Início</th>
            <th class="text-center">Retorno</th>
            <th class="text-center">Periodo</th>
            <th class="text-center">Excedido</th>
            <th class="text-center">Adicional (não cobrado)</th>
            <th class="text-center">Permanencia</th>
            <th class="text-center">Cobrado</th>
            <th class="text-center">Motivo tempo adicional</th>
            <th class="text-center">Valor Crédito</th>
            <th class="text-center">Valor Débito</th>
            <th class="text-center">Valor Dinheiro</th>
            <th class="text-center">Recebido por</th>
            <th class="text-center">Motivo do cancelamento</th>
            <th class="text-center">Status</th>
        </tr>
        </thead>
        <tbody id="rental-body">
        @if($rentals)
            @foreach($rentals as $rental)
            <tr class="text-center {{ $rental->rental_status }}" id="{{$rental->id}}"> 
                <td>{{ Carbon\Carbon::parse($rental->init)->format('d/m/y') }}</td> 
                <td>{{ $rental->toy->description }}</td> 
                <td>{{ $rental->customer->name }}</td> 
                <td>{{ Carbon\Carbon::parse($rental->init)->format('H:i') }}</td> 
                <td>{{ Carbon\Carbon::parse($rental->end)->format('H:i') }}</td> 
                <td>{{ $rental->period->time }}</td> 
                <td>{{ $rental->time_exceded }}</td> 
                <td>{{ $rental->extra_time }}</td> 
                <td>{{ $rental->time_diff }}</td> 
                <td>{{ $rental->time_considered }}</td> 
                <td>{{ $rental->reason_extra_time }}</td> 
                <td>@currency($rental->value_cc)</td> 
                <td>@currency($rental->value_cd)</td> 
                <td>@currency($rental->value_di)</td> 
                <td>{{ $rental->employe->name }}</td> 
                <td>{{ $rental->reason_cancel }}</td> 
                <td>{{ $rental->rental_status }}</td> 
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