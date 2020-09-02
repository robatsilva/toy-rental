@extends('layouts.app')

@section('content')
<style>
    tbody tr:hover{
        background-color: #DDD;
    }

    td{
        vertical-align: middle !important;
    }


    .table-values{
        margin: 0;
        padding: 0;
    }

    div.card-container {
        padding: 1px 10px 0 0;
        height: 130px;

    }
    div.card {
        padding-left: 10px;
        padding-top: 1px;
        padding-bottom: 10px;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 3px 10px 0 rgba(0, 0, 0, 0.19);
        height: 120px;
    }
    div.card h3{
        margin-top: 10px;
    }
    .row-cards{
        margin-top: 20px;
    }
</style>
<div class="container">
    <form id="rental-form" action="/report/entry-exit" method="post">
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
            <div class="form-group col-md-4">
                <label for="emploies">Funcionário:</label>
                <select name="employe_id" class="form-control" id="emploies">
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <button class="btn btn-primary col-md-12">Gerar relatório</button>
            </div>
        </div>

    </form>
    @if(isset($cash_input))
    <div class="row row-cards">
        <div class="col-xs-6 col-md-3 card-container">
            <div class="card card-value">
                <h3>Entradas</h3>
                <p> R$ @currency($cash_input)</p>
            </div>
        </div>
        <div class="col-xs-6 col-md-3 card-container">
            <div class="card card-value">
                <h3>Saídas</h3>
                <p> R$ @currency($cash_output)</p>
            </div>
        </div>
    </div>
    @endif
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <h3>Lançamentos</h3>
            <table class="table table_cash_flow_total">
                <thead>
                <tr>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Descrição</th>
                    <th class="text-right">Valor</th>
                </tr>
                </thead>
                <tbody>
                @if(isset($cash_flows))
                    @foreach($cash_flows as $cash_flow)
                    <tr> 
                        <td>{{ \Carbon\Carbon::parse($cash_flow->created_at)->format('d/m/y') }}</td> 
                        <td>{{$cash_flow->input > 0 ? "Entrada" : "Saída"}}</td>
                        <td>{{$cash_flow->description}}</td>
                        <td class="text-right">@currency($cash_flow->input > 0 ? $cash_flow->input : $cash_flow->output)</td>
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
        $('#kiosks').change(function(){
            showLoader();
        });
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

    function loadEmploies(){
        
        if($('#kiosks').val()){
            showLoader();
            $.get("/employe/list/" + $('#kiosks').val(), function(data){
                hideLoader();
                emploiesResponse(data);
            })
            .fail(function(xhr, status, error) {
                showError(error, status, xhr);
            });
        } 
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
                loadEmploies();
            }
        }
        catch(error){console.log(error);}
    }

    function emploiesResponse(data){
        try{
            $("#emploies").html('');
            if(data.length > 0){
                $.each(data, function(index, value){
                    try{
                        $("#emploies").append("" +
                        "<option " +
                            (value.id == {!! $input?$input['employe_id']:0 !!} ? "selected" : "") + 
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
