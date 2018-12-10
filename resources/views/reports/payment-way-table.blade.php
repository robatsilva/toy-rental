@extends('layouts.app')

@section('content')
<style>
    tbody tr:hover{
        background-color: #DDD;
    }

    td{
        vertical-align: middle !important;
    }

    .table_day_total{
        background-color: #EEE;
    }

    div.card-container {
        padding: 1px 10px 0 0;

    }
    div.card {
        padding-left: 10px;
        padding-top: 1px;
        padding-bottom: 10px;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 3px 10px 0 rgba(0, 0, 0, 0.19);
    }
    div.card h3{
        margin-top: 10px;
    }
    .row-cards{
        margin-top: 20px;
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
    @if(isset($total_period))
    <div class="row row-cards">
        <div class="col-xs-6 col-md-3 card-container">
            <div class="card card-value">
                <h3>Total Crédito</h3>
                <p> R$ {{ $total_cc }}</p>
            </div>
        </div>
        <div class="col-xs-6 col-md-3 card-container">
            <div class="card card-value">
                <h3>Total Débito</h3>
                <p> R$ {{ $total_cd }}</p>
            </div>
        </div>
        <div class="col-xs-6 col-md-3 card-container">
            <div class="card card-value">
                <h3>Total Dinheiro</h3>
                <p> R$ {{ $total_di }}</p>
            </div>
        </div>
        <div class="col-xs-6 col-md-3 card-container">
            <div class="card card-value">
                <h3>Total Período</h3>
                <p> R$ {{ $total_period }}</p>
            </div>
        </div>
    </div>
    @endif
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <h3>Totais por dia</h3>
            <table class="table table_day_total">
                <thead>
                <tr>
                    <th>Data</th>
                    <th class="text-right">Valor total (R$)</th>
                </tr>
                </thead>
                <tbody>
                @if(isset($days))
                    @foreach($days as $day)
                    <tr> 
                        <td>{{ \Carbon\Carbon::parse($day->data_inicio)->format('d/m/Y') }}</td> 
                        <td class="text-right">{{ $day->total_pay }}</td>
                    </tr>       
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
        <div class="col-md-6 col-xs-12">
            <h3>Totais por forma de pagamento</h3>
            <table class="table">
                <thead>
                <tr>
                    <th>Data</th>
                    <th>Forma de pgto.</th>
                    <th class="text-right">Valor total (R$)</th>
                </tr>
                </thead>
                <tbody>
                @if($rentals)
                    @foreach($rentals as $rental)
                    <tr> 
                        <td>{{ \Carbon\Carbon::parse($rental->data_inicio)->format('d/m/Y') }}</td> 
                        <td>{{ $rental->payment_way }}</td> 
                        <td class="text-right">{{ $rental->total_pay }}</td>
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
