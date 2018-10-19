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
    <form id="rental-form" action="/report/payment-way" method="post">
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
        </div>

    </form>
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <h2>Totais por forma de pagamento</h2>
            <table class="table">
                <thead>
                <tr>
                    <th class="text-center">Data</th>
                    <th class="text-center">Forma de pagamento</th>
                    <th class="text-center">Valor total (R$)</th>
                </tr>
                </thead>
                <tbody id="rental-body">
                @if($rentals)
                    @foreach($rentals as $rental)
                    <tr class="text-center" id="{{$rental->id}}"> 
                        <td>{{ \Carbon\Carbon::parse($rental->data_inicio)->format('d/m/Y') }}</td> 
                        <td>{{ $rental->payment_way }}</td> 
                        <td>{{ $rental->total_pay }}</td>
                    </tr>       
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
        <div class="col-md-6 col-xs-12">
            <h2>Totais por dia</h2>
            <table class="table">
                <thead>
                <tr>
                    <th class="text-center">Data</th>
                    <th class="text-center">Forma de pagamento</th>
                    <th class="text-center">Valor total (R$)</th>
                </tr>
                </thead>
                <tbody id="rental-body">
                @if($days)
                    @foreach($days as $day)
                    <tr class="text-center" id="{{$rental->id}}"> 
                        <td>{{ \Carbon\Carbon::parse($day->data_inicio)->format('d/m/Y') }}</td> 
                        <td>{{ $day->total_pay }}</td>
                    </tr>       
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
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
            alert(status + ' - ' + error);
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