@extends('layouts.app')

@section('content')
<style>
    div.card-container {
        padding: 20px 10px 0 0;

    }
    div.card {
        padding-left: 10px;
        height: 100px;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 3px 10px 0 rgba(0, 0, 0, 0.19);
    }
    div.card h3{
        display: inline-block;
    }
</style>
<div class="container">
    <div class="row">
        <form id="cash-form" action="/report/cash" method="post">
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
                    <label for="init">Data</label>
                    <input type='text' name="init" value="{{ $input?$input['init']:\Carbon\Carbon::now()->format('d/m/Y') }}" class="form-control datepicker" id='init'/>
                </div>
            </div>
            <button class="btn btn-primary">Gerar relatório</button>
        </form>
    </div>
    <div class="row">
        <div class="col-xs-3 card-container">
            <div class="card">
                <h3>Entradas</h3>
                <p> R$ {{ $cash?$cash['input']:"" }}</p>
            </div>
        </div>
        <div class="col-xs-3 card-container">
            <div class="card">
                <h3>Saídas</h3>
                <p> R$ {{ $cash?$cash['output']:"" }}</p>
            </div>
        </div>
        <div class="col-xs-3 card-container">
            <div class="card">
                <h3>Aluguéis</h3>
                <p> R$ {{ $cash?$cash['rentals']:"" }}</p>
            </div>
        </div>
        <div class="col-xs-3 card-container">
            <div class="card">
                <h3>Valor em caixa</h3>
                <p> R$ {{ $cash?$cash['total']:"" }}</p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="card-container col-md-6">
            <div class="card">
                <h3>Abertura e fechamento de caixa</h3>
                <table class="table">
                    <thead>
                    <tr>
                        <th class="text-center">Funcionário</th>
                        <th class="text-center">Data</th>
                        <th class="text-center">Valor</th>
                        <th class="text-center">Status</th>
                    </tr>
                    </thead>
                    <tbody id="rental-body">
                    @if($cash)
                        @foreach($cash['registers'] as $register)
                        <tr class="text-center" id="{{$rental->id}}"> 
                            <td>{{ $register->employe->name }}</td> 
                            <td>{{ $register->created_at }}</td>
                            <td>{{ $register->situation }}</td>
                        </tr>       
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-container col-md-6">
            <div class="card">
                <h3>Lançamentos</h3>
                <table class="table">
                    <thead>
                    <tr>
                        <th class="text-center">Funcionário</th>
                        <th class="text-center">Valor</th>
                        <th class="text-center">Descrição</th>
                        <th class="text-center">Tipo</th>
                    </tr>
                    </thead>
                    <tbody id="rental-body">
                    @if($cash)
                        @foreach($cash['cash_flows'] as $flow)
                        <tr class="text-center" id="{{$rental->id}}"> 
                            <td>{{ $flow->employe->name }}</td> 
                            <td>{{ $flow->value }}</td>
                            <td>{{ $flow->description }}</td>
                            <td>{{ $flow->type }}</td>
                        </tr>       
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        <div>
    </div>
</div>
<!-- Modal payment-->
<div id="modal-payment" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Adicionar dinheiro</h4>
            </div>
            <div class="modal-body">
                <form id="rental-form" action="/report/cash-flow" method="post">
                    {!! csrf_field() !!}
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="input">Valor</label>
                            <input type='text' name="input" class="form-control" id='input'/>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="description">Descrição</label>
                            <input type='text' name="description" class="form-control" id='description'/>
                        </div>
                        <div class="form-group col-md-4">
                            <button class="btn btn-primary col-md-12">Salvar</button>
                        </div>
                    </div>
                </form>
            </div>
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