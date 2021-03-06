@extends('layouts.app')

@section('content')
<style>
    h3.valor-caixa-title{
        margin-bottom: 0;
    }
    h3.valor-caixa{
        margin-top: 0;
    }
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
            @if(isset($closeCash))
                <input type="hidden" name="close_cash" value="{{ $closeCash }}">
            @endif
                <div class="form-group col-md-4">
                    <label for="init">Data</label>
                    <input type='text' name="init" value="{{ $input?$input['init']:\Carbon\Carbon::now()->format('d/m/Y') }}" class="form-control datepicker" id='init'/>
                </div>
                <div class="form-group col-md-4">
                    <label for="$cash_drawers">Caixa:</label>
                    <select name="cash_drawer" class="form-control" id="cash_drawer">
                        @if($cash_drawers)
                            @foreach($cash_drawers['cash_drawers'] as $drawer)
                                <option value="{{ $drawer->id }}" {{ $cash_drawers['cash_drawer_id'] == $drawer->id ? 'selected' : '' }}> {{ $drawer->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-check col-md-12">
                    <input type="checkbox" class="form-check-input" name="check_employe" id="check_employe" {{ isset($input['check_employe'])?'checked':'' }}>
                    <label class="form-check-label" for="check_employe">Exibir somente meus lançamentos</label>
                </div>
            </div>
            <button class="btn btn-primary">Gerar relatório</button>
        </form>
    </div>
    <div class="row">
        <div class="col-xs-6 col-md-3 card-container">
            <div class="card card-value">
                <h3>Entradas</h3>
                <p> R$ @currency($cash?$cash['input_day']:"")</p>
                <a id="new_input">Novo</a>
            </div>
        </div>
        <div class="col-xs-6 col-md-3 card-container">
            <div class="card card-value">
                <h3>Saídas</h3>
                <p> R$ @currency($cash?$cash['output_day']:"")</p>
                <a id="new_output">Novo</a>
            </div>
        </div>
        <div class="col-xs-12 col-md-6 card-container">
            <div class="col-xs-6" style="padding: 0 10px 0 0;">
                <div class="col-xs-12 card card-value">
                    <h3>Alugueis dinheiro</h3>
                    <p> R$ @currency($cash?$cash['rentals_day']:"")</p>

                </div>
            </div>
            <div class="col-xs-6 card card-value">
                <h3>Alugueis cartões</h3>
                <p> R$ @currency($cash?$cash['total_cartao']:"")</p>
            </div>
        </div>
        <div class="col-xs-12 col-md-12 card-container">
            <div class="card card-value">
                <h3 class="valor-caixa-title">Valor em Caixa</h3>
                <p> = Entradas - Saídas + Alugueis dinheiro + Valor em caixa dos dias anteriores</p>
                <h3 class="valor-caixa"> R$ @currency($cash?$cash['total']:"")</h3>
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
                        <th class="text-center">Hora</th>
                        <th class="text-center">Fechamento</th>
                        <th class="text-center">Hora</th>
                        <th class="text-center"></th>
                    </tr>
                    </thead>
                    <tbody id="rental-body">
                    @if($cash)
                        @foreach($cash['cashes'] as $register)
                        <tr class="text-center" id="{{$register->id}}"> 
                            <td>{{ $register->employe->name }}</td> 
                            <td>{{ $register->value_open }}</td>
                            <td>{{ Carbon\Carbon::parse($register->created_at)->format('H:i') }}</td>
                            <td>{{ $register->value_close != 0 ? $register->value_close : '' }}</td>
                            <td>{{ $register->value_close != 0 ? Carbon\Carbon::parse($register->updated_at)->format('H:i') : ''}}</td>
                            <td>
                                @if($register->value_close == 0)
                                <a value="{{ $register }}" class="close_cash">Fechar</a>
                                @endif
                                <a value="{{ $register }}" class="see_cash">Ver</a>
                                @if (!Auth::guest() && Auth::user()->permissions()->get()->where('name', 'franqueador')->first())
                                    <a value="{{ $register->id }}" class="delete_cash">Excluir</a>
                                @endIf
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
                        <th class="text-center">Hora</th>
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
                            <td>{{ Carbon\Carbon::parse($flow->created_at)->format('H:i') }}</td>
                            <td>{{ $flow->input != 0? $flow->input : $flow->output }}</td>
                            <td>{{ $flow->description }}</td>
                            <td>{{ $flow->input != 0? 'Entrada' : 'Saída' }}</td>
                            <td><a href="/files/{{ $flow->file }}">{{ $flow->file }}</a></td>
                            @if (!Auth::guest() && Auth::user()->permissions()->get()->where('name', 'franqueador')->first())
                                <td><a value="{{ $flow->id }}" class="delete_cash_flow">Excluir</a></td>
                            @EndIf
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
                <form id="input-output-form" action="/cash-flow" method="post" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <div class="row">
                        <input type='hidden' name="kiosk_id" class="kiosk_id form-control"/>
                        <input type='hidden' name="created_at" id="created_at" class="form-control"/>
                        <input type='hidden' name="cash_drawer" id="cash_drawer_flow" class="form-control"/>
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
                <h4 id="modal-abrir-fechar-title" class="modal-title">Abrir/Fechar caixa</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form id="cash-open-close-form" class="form-inline" action="/cash" method="post">
                        {!! csrf_field() !!}
                            <input type='hidden' name="kiosk_id" class="kiosk_id form-control"/>
                            <input type='hidden' name="init" class="init form-control"/>
                            <input type='hidden' name="cash_drawer" class="cash_drawer form-control"/>
                            <input type='hidden' name="created_at" id="created_at_cash"class="form-control"/>
                            <input type='hidden' name="id" id="id_cash"class="form-control"/>
                            <input type='hidden' name="close_cash" value="{{ isset($close_cash) ? $close_cash : ''  }}" class="form-control"/>
                            <div class="form-group col-md-6">
                                <div><b>Abertura</b><span id="data_abertura"></span></div>
                                <input type='hidden' name="value_open" class="form-control" id='value_open'/>

                                <div class="input-group">
                                    <div class="input-group-addon">R$ &nbsp&nbsp&nbsp&nbsp&nbsp 0,05</div>
                                    <input type="text" class="form-control valores_abertura" peso="0.05" id="a005" name="a005" class="form-control">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">R$ &nbsp&nbsp&nbsp&nbsp&nbsp 0,10</div>
                                    <input type="text" class="form-control valores_abertura" peso="0.1" id="a010" name="a010" class="form-control">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">R$ &nbsp&nbsp&nbsp&nbsp&nbsp 0,25</div>
                                    <input type="text" class="form-control valores_abertura" peso="0.25" id="a025" name="a025" class="form-control">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">R$ &nbsp&nbsp&nbsp&nbsp&nbsp 0,50</div>
                                    <input type="text" class="form-control valores_abertura" peso="0.5" id="a050" name="a050" class="form-control">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">R$ &nbsp&nbsp&nbsp&nbsp&nbsp 1,00</div>
                                    <input type="text" class="form-control valores_abertura" peso="1" id="a1" name="a1" class="form-control">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">R$ &nbsp&nbsp&nbsp&nbsp&nbsp 2,00</div>
                                    <input type="text" class="form-control valores_abertura" peso="2" id="a2" name="a2" class="form-control">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">R$ &nbsp&nbsp&nbsp&nbsp&nbsp 5,00</div>
                                    <input type="text" class="form-control valores_abertura" peso="5" id="a5" name="a5" class="form-control">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">R$ &nbsp&nbsp 10,00</div>
                                    <input type="text" class="form-control valores_abertura" peso="10" id="a10" name="a10" class="form-control">
                                </div>
                                <label class="sr-only" for="a20">R$ 20,00</label>
                                <div class="input-group">
                                    <div class="input-group-addon">R$ &nbsp&nbsp 20,00</div>
                                    <input type="text" class="form-control valores_abertura" peso="20" id="a20" name="a20" class="form-control">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">R$ &nbsp&nbsp 50,00</div>
                                    <input type="text" class="form-control valores_abertura" peso="50" id="a50" name="a50" class="form-control">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">R$ 100,00</div>
                                    <input type="text" class="form-control valores_abertura" peso="100" id="a100" name="a100" class="form-control">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">R$ 200,00</div>
                                    <input type="text" class="form-control valores_abertura" peso="200" id="a200" name="a200" class="form-control">
                                </div>

                                <div><b>Total: R$ <span id='valor_abertura'></span></b></div>
                            </div>
                            <div class="form-group col-md-6">
                                <div><b>Fechamento</b><span id="data_fechamento"></span></div>
                                <input type='hidden' name="value_close" class="form-control" id='value_close'/>
                                
                                <div class="input-group">
                                    <div class="input-group-addon">R$ &nbsp&nbsp&nbsp&nbsp&nbsp 0,05</div>
                                    <input type="text" class="form-control valores_fechamento" peso="0.05" id="f005" name="f005" class="form-control">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">R$ &nbsp&nbsp&nbsp&nbsp&nbsp 0,10</div>
                                    <input type="text" class="form-control valores_fechamento" peso="0.1" id="f010" name="f010" class="form-control">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">R$ &nbsp&nbsp&nbsp&nbsp&nbsp 0,25</div>
                                    <input type="text" class="form-control valores_fechamento" peso="0.25" id="f025" name="f025" class="form-control">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">R$ &nbsp&nbsp&nbsp&nbsp&nbsp 0,50</div>
                                    <input type="text" class="form-control valores_fechamento" peso="0.5" id="f050" name="f050" class="form-control">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">R$ &nbsp&nbsp&nbsp&nbsp&nbsp 1,00</div>
                                    <input type="text" class="form-control valores_fechamento" peso="1" id="f1" name="f1" class="form-control">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">R$ &nbsp&nbsp&nbsp&nbsp&nbsp 2,00</div>
                                    <input type="text" class="form-control valores_fechamento" peso="2" id="f2" name="f2" class="form-control">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">R$ &nbsp&nbsp&nbsp&nbsp&nbsp 5,00</div>
                                    <input type="text" class="form-control valores_fechamento" peso="5" id="f5" name="f5" class="form-control">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">R$ &nbsp&nbsp 10,00</div>
                                    <input type="text" class="form-control valores_fechamento" peso="10" id="f10" name="f10" class="form-control">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">R$ &nbsp&nbsp 20,00</div>
                                    <input type="text" class="form-control valores_fechamento" peso="20" id="f20" name="f20" class="form-control">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">R$ &nbsp&nbsp 50,00</div>
                                    <input type="text" class="form-control valores_fechamento" peso="50" id="f50" name="f50" class="form-control">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">R$ 100,00</div>
                                    <input type="text" class="form-control valores_fechamento" peso="100" id="f100" name="f100" class="form-control">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">R$ 200,00</div>
                                    <input type="text" class="form-control valores_fechamento" peso="200" id="f200" name="f200" class="form-control">
                                </div>

                                <div><b>Total: R$ <span id='valor_fechamento'></span></b></div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button id="save_cash" class="btn btn-primary">Salvar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        $('.datepicker').datepicker({ dateFormat: 'dd/mm/yy' });
        $('#input, #output, #value_open, #value_close').mask('000.000,00', {reverse: true});
        $('.valores_abertura, .valores_fechamento').mask('#', {reverse: true});

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
            $('#cash_drawer_flow').val($('#cash_drawer').val());
            $('#modal_input').modal('show');
            $('#input').show();
            $('#output').hide();
            
        }); 

        $('#new_output').click(function(){
            $('.kiosk_id').val($('#kiosks').val());
            $('#created_at').val($('#init').val());
            $('#cash_drawer_flow').val($('#cash_drawer').val());
            $('#modal_input').modal('show');
            $('#output').show();
            $('#input').hide();
        });

        $('#new_cash').click(function(){
            modalCashOpen();
        });
        
        $('#cash_drawer, #kiosks, #date, #init').change(function(){
            showLoader();
            $('#cash-form').submit();
        });

        $('.close_cash').click(function(){
            var cash = JSON.parse($(this).attr('value'));
            modalCashClose(cash);
        });
        
        $('.see_cash').click(function(){
            var cash = JSON.parse($(this).attr('value'));
            modalCashSee(cash);
        });
        
        $('#cash-form').submit(function(e){
            showLoader();
            if(!validateDate($('#init').val())){
                alert('Data inválida');
                e.preventDefault();
                hideLoader();
            }
        });
        function validateDate(data) {
            reg = /[^\d\/\.]/gi;                  // Mascara = dd/mm/aaaa | dd.mm.aaaa
            var valida = data.replace(reg,'');    // aplica mascara e valida só numeros
            if (valida && valida.length == 10) {  // é válida, então ;)
                var ano = data.substr(6),
                mes = data.substr(3,2),
                dia = data.substr(0,2),
                M30 = ['04','06','09','11'],
                v_mes = /(0[1-9])|(1[0-2])/.test(mes),
                v_ano = /(19[1-9]\d)|(20\d\d)|2100/.test(ano),
                rexpr = new RegExp(mes),
                fev29 = ano % 4? 28: 29;

                if (v_mes && v_ano) {
                if (mes == '02') return (dia >= 1 && dia <= fev29);
                else if (rexpr.test(M30)) return /((0[1-9])|([1-2]\d)|30)/.test(dia);
                else return /((0[1-9])|([1-2]\d)|3[0-1])/.test(dia);
                }
            }
            return false                           // se inválida :(
        }
        $('#cash-open-close-form').submit(function(e){
            showLoader();
            if( $('#id_cash').val() ){
                if( $('#value_close').val() == {!! $cash['total'] ? $cash['total'] : '0'; !!})
                    return;
                else
                {   
                    if($('#value_close').val() > {!! $cash['total'] ? $cash['total'] : '0'; !!})
                        alert('Valor informado MAIOR do que o valor em caixa de R$ {!! $cash['total']; !!}. Verifique se é necessário realizar lançamentos de ENTRADA');
                    else
                        alert('Valor informado MENOR do que o valor em caixa de R$ {!! $cash['total']; !!}. Verifique se é necessário realizar lançamentos de SAÍDA');
                }
            } else {
                if( $('#value_open').val() ==  {!! $cash['total'] ? $cash['total'] : '0'; !!})
                    return;
                else
                {   
                    if($('#value_open').val() > {!! $cash['total'] ? $cash['total'] : '0'; !!})
                        alert('Valor informado MAIOR do que o valor em caixa de R$ {!! $cash['total'] ? $cash['total'] : '0'; !!}. Verifique se é necessário realizar lançamentos de ENTRADA');
                    else 
                        alert('Valor informado MENOR do que o valor em caixa de R$ {!! $cash['total'] ? $cash['total'] : '0'; !!}. Verifique se é necessário realizar lançamentos de SAÍDA');
                }
            }
            hideLoader();
            e.preventDefault();
        });
        $('#input-output-form').submit(function(e){
            showLoader();
        });
        
        $('.delete_cash_flow').click(function(){
            showLoader();
            $.get('/cash-flow/delete/' + $(this).attr('value'), function(data){
                $('#cash-form').submit();
            })
            .fail(function(xhr, status, error) {
                hideLoader();
                showError(error, status, xhr);
            });
        });
        $('.delete_cash').click(function(){
            showLoader();
            $.get('/cash/delete/' + $(this).attr('value'), function(data){
                $('#cash-form').submit();
            })
            .fail(function(xhr, status, error) {
                hideLoader();
                showError(error, status, xhr);
            });
        });
        $('.valores_abertura').keyup(function(){
            var total = 0;
            $('.valores_abertura').each(function(){
                var valor = Number($(this).val());
                if (!isNaN(valor)) total += valor * $(this).attr('peso');
            });
            $('#valor_abertura').html(total.toFixed(2).replace('.', ','));
            $('#value_open').val(total);
        });
        $('.valores_fechamento').keyup(function(){
            var total = 0;
            $('.valores_fechamento').each(function(){
                var valor = Number($(this).val());
                if (!isNaN(valor)) total += valor * $(this).attr('peso');
            });
            $('#valor_fechamento').html(total.toFixed(2).replace('.', ','));
            $('#value_close').val(total);
        });
    }

    function modalCashOpen(){
        $('#modal-abrir-fechar-title').html("Abrir caixa");
        $('.kiosk_id').val($('#kiosks').val());
        $('.init').val($('#init').val());
        $('.cash_drawer').val($('#cash_drawer').val());
        $('#created_at_cash').val($('#init').val());
        $('#id_cash').val("");
        $('.valores_abertura').prop("disabled", "");
        $('.valores_fechamento').prop("disabled", "disabled");
        $('#save_cash').prop("disabled", "");

        $('#modal_cash').modal('show');
    }

    function modalCashClose(cash){
        $('#modal-abrir-fechar-title').html("Fechar caixa");
        $('.kiosk_id').val($('#kiosks').val());
        $('.init').val($('#init').val());
        $('#created_at_cash').val($('#init').val());
        $('#id_cash').val(cash.id);
        $('#value_open').val(cash.value_open);
        $('#valor_abertura').html(cash.value_open);
        $('#data_abertura').html(' : ' + dateToDMY(cash.created_at));

        $('#a005').val(cash.a005);
        $('#a010').val(cash.a010);
        $('#a025').val(cash.a025);
        $('#a050').val(cash.a050);
        $('#a1').val(cash.a1);
        $('#a2').val(cash.a2);
        $('#a5').val(cash.a5);
        $('#a10').val(cash.a10);
        $('#a20').val(cash.a20);
        $('#a50').val(cash.a50);
        $('#a100').val(cash.a100);
        $('#a200').val(cash.a200);
        $('.valores_abertura').prop("disabled", "disabled");
        $('.valores_fechamento').prop("disabled", "");
        $('#save_cash').prop("disabled", "");

        $('#modal_cash').modal('show');
    }
    function modalCashSee(cash){
        $('#value_open').val(cash.value_open);
        $('#valor_abertura').html(cash.value_open);
        $('#valor_fechamento').html(cash.value_close);
        $('#data_abertura').html(' : ' + dateToDMY(cash.created_at));
        $('#data_fechamento').html(' : ' + dateToDMY(cash.updated_at));

        $('#a005').val(cash.a005);
        $('#a010').val(cash.a010);
        $('#a025').val(cash.a025);
        $('#a050').val(cash.a050);
        $('#a1').val(cash.a1);
        $('#a2').val(cash.a2);
        $('#a5').val(cash.a5);
        $('#a10').val(cash.a10);
        $('#a20').val(cash.a20);
        $('#a50').val(cash.a50);
        $('#a100').val(cash.a100);
        $('#a200').val(cash.a200);

        $('#f005').val(cash.f005);
        $('#f010').val(cash.f010);
        $('#f025').val(cash.f025);
        $('#f050').val(cash.f050);
        $('#f1').val(cash.f1);
        $('#f2').val(cash.f2);
        $('#f5').val(cash.f5);
        $('#f10').val(cash.f10);
        $('#f20').val(cash.f20);
        $('#f50').val(cash.f50);
        $('#f100').val(cash.f100);
        $('#f200').val(cash.f200);
        $('.valores_abertura').prop("disabled", "disabled");
        $('.valores_fechamento').prop("disabled", "disabled");
        $('#save_cash').prop("disabled", "disabled");

        $('#modal_cash').modal('show');
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
            kioskResponse(data);
        })
        .fail(function(xhr, status, error) {
            hideLoader();
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
            }
            
            @if(isset($cash_save) || !$cash)
                showLoader();
                $('#cash-form').submit();
            @endif
            
            @if(isset($show_cash) && $show_cash)
                modalCashOpen();
            @endif
            @if(isset($close_cash))
                modalCashClose(JSON.parse( '{!! $close_cash !!}' ));
            @endif

            @if(isset($cash['cashes_old']))
                modalCashClose(JSON.parse( '{!! $cash["cashes_old"] !!}' ));
            @endif
            @if(isset($cash_save) || !$cash)
                return;
            @endif
            hideLoader();
            
        }
        catch(error){console.log(error);}
    }

    function dateToDMY(date) {
        var dateObj = new Date(date);
        
        var d = dateObj.getDate();
        var m = dateObj.getMonth() + 1; //Month from 0 to 11
        var y = dateObj.getFullYear();
        return ''  + (d <= 9 ? '0' + d : d) + '/' + (m<=9 ? '0' + m : m) + '/'  + y + date.substr(10);
    }
</script>
@endsection
