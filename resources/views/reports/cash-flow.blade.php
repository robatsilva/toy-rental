@extends('layouts.app')

@section('content')
<style>
    div.card-container {
        padding: 20px 10px 0 0;

    }
    div.card {
        padding-left: 10px;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 3px 10px 0 rgba(0, 0, 0, 0.19);
    }
    div.card-value {
        padding-left: 10px;
        height: 110px;
    }
    div.card h3{
        display: inline-block;
    }

    a{
        cursor: pointer;
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
                <input type="hidden" name="kiosk_id" id="kiosks" value="{{ Auth::user()->kiosk_id }}">
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
            <div class="card card-value">
                <h3>Entradas</h3>
                <p> R$ {{ $cash?$cash['input']:"" }}</p>
                <a id="new_input">Novo</a>
            </div>
        </div>
        <div class="col-xs-3 card-container">
            <div class="card card-value">
                <h3>Saídas</h3>
                <p> R$ {{ $cash?$cash['output']:"" }}</p>
                <a id="new_output">Novo</a>
            </div>
        </div>
        <div class="col-xs-3 card-container">
            <div class="card card-value">
                <h3>Aluguéis</h3>
                <p> R$ {{ $cash?$cash['rentals']:"" }}</p>
            </div>
        </div>
        <div class="col-xs-3 card-container">
            <div class="card card-value">
                <h3>Valor em caixa</h3>
                <p> R$ {{ $cash?$cash['total']:"" }}</p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="card-container col-md-6">
            <div class="card">
                <h3>Abertura e fechamento de caixa</h3><a style="float: right; margin: 10px;" id="new_cash">Novo</a>
                <table class="table">
                    <thead>
                    <tr>
                        <th class="text-center">Funcionário</th>
                        <th class="text-center">Abertura</th>
                        <th class="text-center">Fechamento</th>
                        <th class="text-center"></th>
                    </tr>
                    </thead>
                    <tbody id="rental-body">
                    @if($cash)
                        @foreach($cash['cashes'] as $register)
                        <tr class="text-center" id="{{$register->id}}"> 
                            <td>{{ $register->employe->name }}</td> 
                            <td>{{ $register->value_open }}</td>
                            <td>{{ $register->value_close }}</td>
                            <td>
                                @if($register->value_close == 0)
                                <a value="{{ $register }}" class="close_cash">Fechar</a>
                                @endif
                                <a value="{{ $register->id }}" class="delete_cash">excluir</a>
                            </td>
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
                        <th class="text-center">Anexo</th>
                        <th class="text-center"></th>
                    </tr>
                    </thead>
                    <tbody id="rental-body">
                    @if($cash)
                        @foreach($cash['cash_flows'] as $flow)
                        <tr class="text-center" id="{{$flow->id}}"> 
                            <td>{{ $flow->employe->name }}</td> 
                            <td>{{ $flow->input != 0? $flow->input : $flow->output }}</td>
                            <td>{{ $flow->description }}</td>
                            <td>{{ $flow->input != 0? 'Entrada' : 'Saída' }}</td>
                            <td><a href="/files/{{ $flow->file }}">{{ $flow->file }}</a></td>
                            <td><a value="{{ $flow->id }}" class="delete_cash_flow">excluir</a></td>
                        </tr>       
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        <div>
    </div>
</div>
<!-- Modal input/output-->
<div id="modal_input" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Novo lançamento</h4>
            </div>
            <div class="modal-body">
                <form id="rental-form" action="/cash-flow" method="post" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <div class="row">
                        <input type='hidden' name="kiosk_id" class="kiosk_id form-control"/>
                        <input type='hidden' name="created_at" id="created_at"class="form-control"/>
                        <div class="form-group col-md-6">
                            <label for="input">Valor</label>
                            <input type='text' name="input" class="form-control" id='input' value="0"/>
                            <input type='text' name="output" class="form-control" id='output' value="0"/>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="description">Descrição</label>
                            <input type='text' name="description" class="form-control" id='description'/>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="description">Anexo</label>
                            <input type='file' name="file" class="form-control" id='file'/>
                        </div>
                    </div>
                    <button class="btn btn-primary">Salvar</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal open/close-->
<div id="modal_cash" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Abrir/Fechar caixa</h4>
            </div>
            <div class="modal-body">
                <form id="rental-form" action="/cash" method="post">
                    {!! csrf_field() !!}
                    <div class="row">
                        <input type='hidden' name="kiosk_id" class="kiosk_id form-control"/>
                        <input type='hidden' name="created_at" id="created_at_cash"class="form-control"/>
                        <input type='hidden' name="id" id="id_cash"class="form-control"/>
                        <div class="form-group col-md-6">
                            <label for="value_open">Abertura</label>
                            <input type='text' name="value_open" class="form-control" id='value_open'/>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="value_close">Fechamento</label>
                            <input disabled type='text' name="value_close" class="form-control" id='value_close'/>
                        </div>
                    </div>
                    <button class="btn btn-primary">Salvar</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        $('.datepicker').datepicker({ dateFormat: 'dd/mm/yy' });
        $('#input, #output, #value_open, #value_close').mask('000.000,00', {reverse: true});

        //loaders
        initLoaders();

        //listeners
        initListeners();
    });

    ////////////////////////Listeners
    function initListeners(){
        $('#new_input').click(function(){
            $('.kiosk_id').val($('#kiosks').val());
            $('#created_at').val($('#init').val());
            $('#modal_input').modal('show');
            $('#input').show();
            $('#output').hide();
            
        }); 

        $('#new_output').click(function(){
            $('.kiosk_id').val($('#kiosks').val());
            $('#created_at').val($('#init').val());
            $('#modal_input').modal('show');
            $('#output').show();
            $('#input').hide();
        });

        $('#new_cash').click(function(){
            $('.kiosk_id').val($('#kiosks').val());
            $('#created_at_cash').val($('#init').val());
            $('#id_cash').val("");
            $('#value_open').prop("disabled", "");
            $('#value_close').prop("disabled", "disabled");

            $('#modal_cash').modal('show');
        });

        $('.close_cash').click(function(){
            var cash = JSON.parse($(this).attr('value'));
            $('.kiosk_id').val($('#kiosks').val());
            $('#created_at_cash').val($('#init').val());
            $('#id_cash').val(cash.id);
            $('#value_open').val(cash.value_open);
            $('#value_open').prop("disabled", "disabled");
            $('#value_close').prop("disabled", "");

            $('#modal_cash').modal('show');
        });

        $('.delete_cash_flow').click(function(){
            $.get('/cash-flow/delete/' + $(this).attr('value'), function(data){
                $('#cash-form').submit();
            });
        });
        $('.delete_cash').click(function(){
            $.get('/cash/delete/' + $(this).attr('value'), function(data){
                $('#cash-form').submit();
            });
        });
    }

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
                @if(isset($cash_save) || !$cash)
                $('#cash-form').submit();
                @endif
                hideLoader();
            }
        }
        catch(error){console.log(error);}
    }
</script>
@endsection