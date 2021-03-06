@extends('layouts.app')

@section('content')
<style>
    .btn-digit, .btn-limpar{
        padding: 15px;
    }
    .hidden-toogle{
        display: none;
    }
    .type-group{
        margin-bottom: 10px;
    }
</style>
<div class="container">
    <div class="row text-center" style="margin-bottom: 20px;">
        <div {{ ($kiosk->types->count() === 1)?("class=hidden-toogle"):"" }} class="btn-group type-group" data-toggle="buttons">
            @foreach($kiosk->types as $type)
            <label class="btn btn-primary active type fa fa-toggle-on">
                <input class="type" type="checkbox" value="{{ $type->id }}" checked>{{ $type->description }}
            </label>
            @endforeach
        </div>
        <div>{{$kiosk->name}} - 
        @if($cash)
            {{ $cash->cash_drawer->name }} aberto</div>
        @else
            Nenhum caixa aberto</div>
            <p style="color: red">*Para receber em dinheiro abra um caixa</p>
        @endif
    </div>
    <div class="row text-center" style="margin-bottom: 20px;">
        <div class="col-xs-12 col-md-12">
            <buttom data-value="0" class="btn btn-primary btn-digit">0</buttom>
            <buttom data-value="1" class="btn btn-primary btn-digit">1</buttom>
            <buttom data-value="2" class="btn btn-primary btn-digit">2</buttom>
            <buttom data-value="3" class="btn btn-primary btn-digit">3</buttom>
            <buttom data-value="4" class="btn btn-primary btn-digit">4</buttom>
            <buttom data-value="5" class="btn btn-primary btn-digit">5</buttom>
            <buttom data-value="6" class="btn btn-primary btn-digit">6</buttom>
            <buttom data-value="7" class="btn btn-primary btn-digit">7</buttom>
            <buttom data-value="8" class="btn btn-primary btn-digit">8</buttom>
            <buttom data-value="9" class="btn btn-primary btn-digit">9</buttom>
            <buttom class="btn btn-primary btn-limpar"><-</buttom>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-xs-6 col-md-3">
            <input name="cpf" pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" class="form-control clear" id="cpf" placeholder="CPF" required>
        </div>
        <div class="form-group col-xs-6 col-md-3">
            <input type="hidden" name="id" id="id"/>
            <input class="form-control clear" name="name" id="name" placeholder="Nome" disabled required>
        </div>
    </div>
    @if(!$kiosk_id)
        <h2>Para começar você deve cadastrar pelo menos um <a href="kiosk">Quiosque</a></h2>
    @else
        <div class="row text-center">
            <span id="rental-tip"></span>
        </div>
        <!--Grid-->
        <div id="rentals-toys" class="row form-group">
        </div>
    @endif

    <!-- Modal payment-->
    <div id="modal-payment" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Mais de uma forma de pagamento</h4>
                </div>
                <div class="modal-body">
                    <form id="payment-form">
                        <div class="row">
                            @if($cash)
                                <div class="form-group col-md-4">
                                    <label for="value_di">Dinheiro:</label>
                                    <input name="value_di" class="form-control money" id="value_di" placeholder="Dinheiro" required>
                                </div>
                            @else
                                <input type="hidden" value="0" name="value_di" class="form-control money" id="value_di" required>
                            @endif
                            <div class="form-group col-md-4">
                                <label for="value_cd">Cartão de débito:</label>
                                <input name="value_cd" class="form-control money" id="value_cd" placeholder="Débito" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="value_cc">Cartão de crédito:</label>
                                <input name="value_cc" class="form-control money" id="value_cc" placeholder="Crédito" required>
                            </div>
                        </div>
                        <div class="row text-center">
                            <h2>Valor a pagar: <span id="value-total"></span></h2>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <buttom class="btn btn-primary btn-save-payment">Receber</buttom>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal extra-time-->
    <div id="modal-extra-time" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Adicionar tempo extra</h4>
                </div>
                <div class="modal-body">
                    <form id="extra-time-form">
                        <div class="row">
                            <div class="form-group col-md-9">
                                <label for="reason-extra-time">Escolha o motivo:</label>
                                <select size="8" name="reason-extra-time" class="form-control" id="reason-extra-time" required>
                                @foreach($reasons as $reason)
                                    <option value="{{ $reason->text }}">{{ $reason->text }}</option>
                                @endforeach
                                    <option value="">Outro</option>
                                </select>
                            </div>
                            <div class="form-group col-md-9">
                                <label for="reason-extra-time-other">Outro:</label>
                                <input name="reason_extra_time-other" class="form-control" id="reason-extra-time-other" placeholder="Motivo" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="toy-pays text-center"> 
                                <buttom data-value="1"class="btn btn-primary btn-save-extra-time">+ 1 min</buttom>
                                <buttom data-value="2"class="btn btn-primary btn-save-extra-time">+ 2 min</buttom>
                                <buttom data-value="3"class="btn btn-primary btn-save-extra-time">+ 3 min</buttom>
                                <buttom data-value="4"class="btn btn-primary btn-save-extra-time">+ 4 min</buttom>
                                <buttom data-value="5" class="btn btn-primary btn-save-extra-time">+ 5 min</buttom>
                                <buttom data-value="10"class="btn btn-primary btn-save-extra-time">+ 10 min</buttom>
                                <buttom data-value="0"class="btn btn-primary btn-save-extra-time">Zerar</buttom>
                            </div>
                        </div>
                        <div class="row text-center">
                            <h2>Tempo extra: <span id="extra-time"></span></h2>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal cancel-->
    <div id="modal-cancel" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Cancelar</h4>
                </div>
                <div class="modal-body">
                    <form id="cancel-form">
                        <div class="row">
                            <div class="form-group col-md-9">
                                <label for="reason-cancel">Escolha o motivo:</label>
                                <select size="8" name="reason-cancel" class="form-control" id="reason-cancel" required>
                                @foreach($reasons as $reason)
                                    <option value="{{ $reason->text }}">{{ $reason->text }}</option>
                                @endforeach
                                    <option value="">Outro</option>
                                </select>
                            </div>
                            <div class="form-group col-md-9">
                                <label for="reason-cancel-other">Outro:</label>
                                <input name="reason_cancel-other" class="form-control" id="reason-cancel-other" placeholder="Motivo" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="text-center"> 
                                <buttom class="btn btn-primary btn-save-cancel">Salvar cancelamento</buttom>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal-->
    <div class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Pagamento</h4>
                </div>
                <div class="modal-body">
                    <form id="rental-finish-form">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <h5><b>Periodo:</b></h5>
                                <h6><span id="period-time">0</span> <span>Minutos</span></h6>
                            </div>
                            <div class="form-group col-md-4">
                                <h5><b>Tempo excedente:</b></h5>
                                <h6><span id="time-exceeded">0</span> <span>Minutos</span></h6>
                            </div>
                            <div class="form-group col-md-4">
                                <h5><b>Tempo adicional:</b></h5>
                                <h6><span id="extra_time">0</span> <span>Minutos</span></h6>
                            </div>
                            <div class="form-group col-md-4">
                                <h5><b>Tempo de permanência:</b></h5>
                                <h6><span id="time_total">0</span> <span>Minutos</span></h6>
                            </div>
                            <div class="form-group col-md-4">
                                <h5><b>Tempo considerado:</b></h5>
                                <h6><span id="time_considered">0</span> <span>Minutos</span></h6>
                            </div>
                            <div class="form-group col-md-4">
                                <h5><b>Valor do tempo excednte:</b></h5>
                                <h6><span>R$</span> <span id="value-exceeded">0,00</span></h6>
                            </div>
                            <div class="form-group col-md-12 text-center">
                                <h5><b>Valor a pagar:</b></h5>
                                <h6><span>R$</span> <span>0,00</span></h6>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="payment_way">Forma de pagamento:</label>
                                <select name="payment_way" class="form-control" id="payment_way" required>
                                    <option value="Dinheiro">Dinheiro</option>
                                    <option value="Débito">Débito</option>
                                    <option value="Crédito">Crédito</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" id="btn-save-finish">Salvar</button>
                </div>
            </div>
        </div>
    </div>  
</div>
@endsection
@section('scripts')
<script>
    var kiosk_id = {{ $kiosk_id }};
    var toys;
    var toy;
    var customer = {id: "", name: "", cpf: ""};
    var types = [];

    
    $(document).ready(function(){
        $('.money').mask("#,##", {reverse: true});
        $('#cpf').mask('000.000.000-00', {reverse: true});
        $('#cpf').focus();

        // $('#toys').select2({
        //     theme: "bootstrap"
        // });
        showLoader();
        reloadRentals();

        //loaders
        // initLoaders();
        //Listeners
        initListeners();
    });

    ////////////////////////Loaders
    // function initLoaders(){;
    //     loadKiosks();
    // }
    // function loadPeriods(){
    //     $.get("/period/getByKioskId/" + kiosk_id, function(data){
    //         periodResponse(data);
    //     })
    //     .fail(function(xhr, status, error) {
    //         showError(error, status, xhr);
    //     });
    // }
    // function loadKiosks()
    // {
    //     showLoader();
    //     $.get("/kiosk/list", function(data){
    //         hideLoader();
    //         kioskResponse(data);
    //         loadToys();
    //         loadPeriods();
    //     });
    // }
    // function loadToys()
    // {
    //     showLoader();
    //     $.get("/toy/getByKioskId/" + $("#kiosks").val(), function(data){
    //         toyResponse(data);
    //     });
    // }
    function loadRentals() {
        //$.get("/rental/" + $("#kiosks").val(), function(data){
        types = [];
        $.each($("input.type:checked"), function(){
            types.push($(this).val());
        });
        $.get("/rental/" + kiosk_id + "?types=" + types.join(","), function(data){
            rentalsResponse(data);
            hideLoader();
        })
        .fail(function(xhr, status, error) {
            hideLoader();
            showError(error, status, xhr);
        });
    }
    function loadCpf(){
        if(validateCpf()){
            showLoader();
            $.get("/customer/" + {{ $kiosk_id }} + "/" + $("#cpf").val(), function(data){
                cpfResponse(data);
                //validateCustomer();
            })
            .fail(function(xhr, status, error) {
                hideLoader();
                cpfResponse('');
                showError(error, status, xhr);
            });
        }
        else{
            $("#customer").hide();
        }
    }
    ////////////////End Loaders
    ////////////////Listeners
    function initListeners(){
        cpfChange();
        nameChange();
        inputKeyUp();
        typeToogle();
    }
    function inputKeyUp(){
        $('#cpf').keydown(function (e){
            if(e.keyCode == 13){
                loadCpf();
            }
        });
    }
    function cpfChange(){
        $("#cpf").change(function(){
            loadCpf();
        });
    }
    function nameChange(){
        $("#name").on('blur', function() {
            customer.name = $("#name").val();
        });
    }
    function typeToogle(){
        $("input.type").change(function() {
            if($("input.type:checked").length === 0){
                $(this).parent().addClass("active fa-toggle-off");
                $(this).prop("checked", true);
                alert('Não é permitido desabilitar os dois tipos')
                return;
            } 
            
            if($(this).prop("checked")){
                $(this).parent().addClass("fa-toggle-on");
                $(this).parent().removeClass("fa-toggle-off");
            } else {
                $(this).parent().addClass("fa-toggle-off");
                $(this).parent().removeClass("fa-toggle-on");
            }
    
            showLoader();
            loadRentals();
            
        });
    }

    $(".btn-digit").click(function(){
        var digit = $(this).attr("data-value");
        $("#cpf").val($("#cpf").val() + digit);
        $("#cpf").trigger('input');
        if(validateCpf(false)){
            loadCpf();
        }
    });

    $(".btn-limpar").click(function(){
        var texto = $("#cpf").val();
        $("#cpf").val(texto.substr(0, texto.length - 1));
    });

    $(".btn-save-extra-time").click(function(){
        if(!$("#reason-extra-time :selected").val() 
            && !$("#reason-extra-time-other").val()
            && $(this).attr("data-value") != 0){
            alert("Selecione um motivo");
            return;
        }
            
        showLoader();
        var extra_time = $(this).attr("data-value");
        $.post("/rental/extra-time  ", {
            _token: "{{ csrf_token() }}",
            id: toy.rental.id,
            extra_time: extra_time,
            reason_extra_time: $("#reason-extra-time :selected").val(),
            reason_extra_time_other: $("#reason-extra-time-other").val()
        }, function(data){
            if(extra_time == 0)
                $("#extra-time").html("0");
            else
                $("#extra-time").html(Number($("#extra-time").html()) + Number(extra_time));
            loadRentals();
        })
        .fail(function(xhr, status, error) {
            showError(error, status, xhr);
        });
    });

    $(".money").focus(function(){
        var cc = Number($('#value_cc').val().replace(',', '.'));
        var cd = Number($('#value_cd').val().replace(',', '.'));
        var di = Number($('#value_di').val().replace(',', '.'));
        var total = cc + cd + di;
        if(total != Number(toy.rental.value_to_pay) && total != 0){
            $(this).val((toy.rental.value_to_pay - cc - cd - di).toString().replace('.', ','));
        }
    });

    $(".btn-save-payment").click(function(){    
        showLoader();
        var cc = Number($('#value_cc').val().replace(',', '.'));
        var cd = Number($('#value_cd').val().replace(',', '.'));
        var di = Number($('#value_di').val().replace(',', '.'));
        var total = cc + cd + di;

        if(total != Number(toy.rental.value_to_pay)){
            alert('A soma dos pagamentos deve ser igual a R$' + toy.rental.value_to_pay.toString().replace('.', ','));
            hideLoader();
            return;
        }
        toy.rental.payment_way = undefined;
        toy.rental.value_cd = $('#value_cd').val().replace(',', '.');
        toy.rental.value_cc = $('#value_cc').val().replace(',', '.');
        toy.rental.value_di = $('#value_di').val().replace(',', '.');
        toy.rental._token = "{{ csrf_token() }}";
        showLoader();
        $.post("/rental/finish", toy.rental, function(){
            loadRentals();
            $("#modal-payment").modal('hide');
        })
        .fail(function(xhr, status, error) {
            hideLoader();
            showError(error, status, xhr);
        });
    });

    $(".btn-save-cancel").click(function(){
        if(!$("#reason-cancel :selected").val() 
            && !$("#reason-cancel-other").val()){
            alert("Selecione um motivo");
            return;
        }
        showLoader();
        $.post("/rental/cancel/" + toy.rental.id, {
            _token: "{{ csrf_token() }}",
            reason_cancel: $("#reason-cancel :selected").val(),
            reason_cancel_other: $("#reason-cancel-other").val()
            },function(data){
            loadRentals();
            $("#modal-cancel").modal('hide');
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
        })
        .fail(function(xhr, status, error) {
            hideLoader();
            showError(error, status, xhr);
        });
    });
    
    // function periodChange(){
    //     $("#period").on('change', function() {
    //         validateCustomer();
    //         $("#btn-register-rental").focus();
    //         $("#btn-register-rental").select();        
    //     });
    // }
    
    // function kioskChange(){
    //     $("#kiosks").on("change", function(){
    //         $("#tolerance").val($(this).find(":selected").data("value").tolerance);
    //         $("#extra-value").val("R$ " + $(this).find(":selected").data("value").extra_value);
    //         //$("#period").val($(this).find(":selected").data("value").period_id);
    //         loadRentals();
    //         validateCustomer();
    //     });
    // } 
//    function btnClick(){
//         $("#btn-register-rental").click(function(event){
//             showLoader();
//             event.preventDefault();
//             registerRental();
//         });
//     }

    
    ////////////////End listeners
    ////////////////Registers
    
    ////////////////End Registers
    ////////////////Responses
    // function periodResponse(data){
    //     if(data.length > 0)
    //         $.each(data, function(index, value){
    //             $("#period").append("" +
    //             "<option value="+ value.id +">" +
    //                 value.time + " min - R$" + value.value +
    //             "</option>");
    //         });
    // }
    // function kioskResponse(data){
    //     try{
    //         if(data.length > 0){
    //             $.each(data, function(index, value){
    //                 try{
    //                     $("#kiosks").append("" +
    //                     "<option " +
    //                         (value.default?"selected":"") + 
    //                         " data-value='" + JSON.stringify(value) + "'" + 
    //                         " value=" + value.id +">" +
    //                         value.name + 
    //                     "</option>");
    //                 }
    //                 catch(error){
    //                     console.log(error);
    //                 }
    //             });
    //             $("#kiosks").trigger('change');
    //             hideLoader();
    //         }
    //     }
    //     catch(error){console.log(error);}
    // }

    // function toyResponse(data){
    //     if(data.length > 0){
            
    //         $("#toys").html("");
    //         $("#toys").append("<option value=''>Brinquedo...</option>");
    //         $.each(data, function(index, value){
    //             $("#toys").append("" +
    //             "<option value="+ value.id +">" +
    //                 value.code + " - " + value.description + 
    //             "</option>");
    //         });
    //     }
    //     hideLoader();
    // }

    function rentalsResponse(data){
        $("#rentals-toys").html(data);
        // validateCustomer();
    }

    function cpfResponse(data){
        $("#customer").show();
        customer.cpf = $("#cpf").val();
        if(data.name !== undefined){
            if(data.id){
                customer = data;
                $("#id").val(data.id);
                $("#name").val(data.name);
                $("#name").attr("disabled", true);
                // toysFocus();
            }
        }
        else{
            customer.id = undefined;
            $("#name").attr("disabled", false);
            $("#name").attr("placeholder", "Insira um nome");
            $("#name").focus();
            $("#name").select();
        }
        customer.change_toy = false;
        $('#rental-tip').html('Clique duas vezes em uma imagem para: ALUGAR');
        // validateCustomer();
        hideLoader();
    }
    ///////////////End Responses
    ///////////////Validate form
    // function validateCustomer(){
    //     if($("#name").val() == "" 
    //         || $("#toys").val() == "" 
    //         || $("#cpf").val() == ""
    //         || $("#period").val() == ""
    //         || $("#tolerance").val() == ""
    //         || $("#extra_value").val() == "")
    //         $("#btn-register-rental").attr("disabled", true);
    //     else
    //         $("#btn-register-rental").attr("disabled", false);
	// }
 
    function validateCpf(showMessage){
        var cpf = $('#cpf').val().replace(/[^0-9]/g, '').toString();

        if( cpf.length == 11 )
        {
            var v = [];

            //Calcula o primeiro dígito de verificação.
            v[0] = 1 * cpf[0] + 2 * cpf[1] + 3 * cpf[2];
            v[0] += 4 * cpf[3] + 5 * cpf[4] + 6 * cpf[5];
            v[0] += 7 * cpf[6] + 8 * cpf[7] + 9 * cpf[8];
            v[0] = v[0] % 11;
            v[0] = v[0] % 10;

            //Calcula o segundo dígito de verificação.
            v[1] = 1 * cpf[1] + 2 * cpf[2] + 3 * cpf[3];
            v[1] += 4 * cpf[4] + 5 * cpf[5] + 6 * cpf[6];
            v[1] += 7 * cpf[7] + 8 * cpf[8] + 9 * v[0];
            v[1] = v[1] % 11;
            v[1] = v[1] % 10;

            //Retorna Verdadeiro se os dígitos de verificação são os esperados.
            if ( (v[0] != cpf[9]) || (v[1] != cpf[10]) )
            {
                if(showMessage){
                    alert('CPF inválido: ' + cpf);

                    // $('#cpf').val('');
                    $('#cpf').focus();
                }
                return false;
            }
            return true;
        }
        else
        {
            if(showMessage){
                alert('CPF inválido: ' + cpf);

                // $('#cpf').val('');
                $('#cpf').focus();
            }
            return false;
        }
    }
    // function toysFocus()
    // {
    //     $("#toys").focus();
    //     $("#toys").select();
    //     $('#toys').select2('open');
    // }

    function reloadRentals(){
        loadRentals();
        setTimeout(function() {
                $("#btn-register-rental").focus();
                $("#btn-register-rental").select();
                
                reloadRentals();
            }, 60000);  
    }

</script>
@endsection
