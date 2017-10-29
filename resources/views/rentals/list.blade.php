@extends('layouts.app')

@section('content')
<div class="container">
    <!--form-->
    <div class="row">
        <form id="rental-form">
            {!! csrf_field() !!}
            <div class="row">
                <div class="form-group col-md-4">
                    <label for="kiosks">Quiosque operado:</label>
                    <select name="kiosk_id" class="form-control" id="kiosks">
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="tolerance">Tolerância:</label>
                    <input name="tolerance" disabled class="form-control" id="tolerance" placeholder="Tolerância" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="extra-value">Valo do minuto adicional:</label>
                    <input name="extra_value" disabled class="form-control" id="extra-value" placeholder="Minuto adicional" required>
                </div>
                <div class="form-group col-md-3">
                    <label for="cpf">CPF:</label>
                    <input name="cpf" pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" class="form-control clear" id="cpf" placeholder="CPF" required>
                </div>
                <div class="form-group col-md-3">
                    <input type="hidden" name="id" id="id"/>
                    <label for="name">Nome:</label>
                    <input class="form-control clear" name="name" id="name" placeholder="Nome" disabled required>
                </div>
                <div class="col-md-3 form-group">
                    <label for="toys">Brinquedo:</label>
                        <select name="toy_id" class="js-example-basic-single js-states clear form-control" id="toys" required>
                        </select>
                </div>
                
                <div class="form-group col-md-3">
                    <label for="period">Periodo:</label>
                    <select name="period_id" class="form-control" id="period" required>
                        <option value="">Escolha o periodo...</option>
                    </select>
                </div>
                
            </div>
            <div class="row">
                <div class="form-group col-md-4">
                    <button id="btn-register-rental" class="btn btn-primary col-md-12" disabled>Registrar</button>
                </div>
            </div>
        </form>
    </div>
    
    <!--Table-->
    <div id="table-rental" class="row form-group">
    </div>

    <!--Modal payment-->
    <!-- Modal payment-->
    <div id="modal-payment" class="modal fade" role="dialog">
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
                                <h6><span>R$</span> <span id="value-total">0,00</span></h6>
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

    <!-- Modal extra-time-->
    <div id="modal-extra-time" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Tempo extra</h4>
                </div>
                <div class="modal-body">
                    <form id="extra-time-form">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="extra-time">Tempo adicional:</label>
                                <input name="extra_time" class="form-control" id="extra-time" placeholder="Tempo adicional">
                            </div>
                            <div class="form-group col-md-9">
                                <label for="reason-extra-time">Motivo do tempo adicional:</label>
                                <input name="reason_extra_time" class="form-control" id="reason-extra-time" placeholder="Motivo" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" id="btn-save-extra-time">Salvar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    $(document).ready(function(){
        $('#cpf').mask('000.000.000-00', {reverse: true});
        $('#extra-time').mask('00', {reverse: true});
        $('#cpf').focus();

        $('#toys').select2({
            theme: "bootstrap"
        });

        reloadRentals();

        //loaders
        initLoaders();
        //Listeners
        initListeners();
    });

    ////////////////////////Loaders
    function initLoaders(){
        loadPeriods();
        loadKiosks();
    }
    function loadPeriods(){
        $.get("/period", function(data){
            periodResponse(data);
        });
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
    function loadToys()
    {
        showLoader();
        $.get("/toy/" + $("#kiosks").val(), function(data){
            toyResponse(data);
        });
    }
    function loadRentals()
    {
        $.get("/rental/" + $("#kiosks").val(), function(data){
            rentalsResponse(data);
        });
    }
    function loadCpf(){
        if(validateCpf()){
            showLoader();
            $.get("/customer/" + $("#kiosks").val() + "/" + $("#cpf").val(), function(data){
                cpfResponse(data);
                validateCustomer();
            });
        }
        else{
            $("#customer").hide();
        }
    }
    ////////////////End Loaders
    ////////////////Listeners
    function initListeners(){
        kioskChange();
        cpfChange();
        nameChange();
        toysChange();
        periodChange();
        inputKeyUp();
        btnClick();
    }
    function inputKeyUp(){
        $('#cpf').keydown(function (e){
            if(e.keyCode == 13){
                loadCpf();
            }
        });
        $('#name').keydown(function (e){
            if(e.keyCode == 13){
                toysFocus();
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
            validateCustomer();
        });
    }
    function toysChange(){
        $("#toys").on('change', function() {
            setTimeout(function() {
                $("#period").focus();
                $("#period").select();
            }, 0);  
            validateCustomer();
        });
    }
    
    function periodChange(){
        $("#period").on('change', function() {
            validateCustomer();
            $("#btn-register-rental").focus();
            $("#btn-register-rental").select();        
        });
    }
    
    function kioskChange(){
        $("#kiosks").on("change", function(){
            $("#tolerance").val($(this).find(":selected").data("value").tolerance);
            $("#extra-value").val($(this).find(":selected").data("value").extra_value);
            //$("#period").val($(this).find(":selected").data("value").period_id);
            loadRentals();
            validateCustomer();
        });
    } 
   function btnClick(){
        $("#btn-register-rental").click(function(event){
            showLoader();
            event.preventDefault();
            registerRental();
        });
    }

    $("#btn-save-finish").click(function(){
        var rentalId = $(this).val();
        showLoader();
        $.post("/rental/finish", 
        {
            _token: "{{ csrf_token() }}",
            id: rentalId,
            payment_way: $("#payment_way").val(),
            discount: $("#discount").val()

        }, function(){
            loadRentals();
            loadToys();
            $("#modal-payment").modal('hide');
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
        });
    });
    ////////////////End listeners
    ////////////////Registers
    function registerRental(){

        $.post("/rental", $("#rental-form").serialize(), function(data){
            //reload toys and rentals
            loadToys();
            loadRentals();
            $(".clear").val("");
            $(".clear").text("");
            validateCustomer();
            hideLoader();
        });
    }
    ////////////////End Registers
    ////////////////Responses
    function periodResponse(data){
        if(data.length > 0)
            $.each(data, function(index, value){
                $("#period").append("" +
                "<option value="+ value.id +">" +
                    value.time + " min - R$" + value.value +
                "</option>");
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
                $("#kiosks").trigger('change');
                hideLoader();
            }
        }
        catch(error){console.log(error);}
    }

    function toyResponse(data){
        if(data.length > 0){
            
            $("#toys").html("");
            $("#toys").append("<option value=''>Brinquedo...</option>");
            $.each(data, function(index, value){
                $("#toys").append("" +
                "<option value="+ value.id +">" +
                    value.code + " - " + value.description + 
                "</option>");
            });
        }
        hideLoader();
    }

    function rentalsResponse(data){
        $("#table-rental").html(data);
        validateCustomer();
    }

    function cpfResponse(data){
        $("#customer").show();
        if(data.name !== undefined){
            if(data.id){
                $("#id").val(data.id);
                $("#name").val(data.name);
                $("#name").attr("disabled", true);
                toysFocus();
            }
        }
        else{
            $("#name").attr("disabled", false);
            $("#name").attr("placeholder", "Insira um nome");
            $("#name").focus();
            $("#name").select();
        }
        validateCustomer();
        hideLoader();
    }
    ///////////////End Responses
    ///////////////Validate form
    function validateCustomer(){
        if($("#name").val() == "" 
            || $("#toys").val() == "" 
            || $("#cpf").val() == ""
            || $("#period").val() == ""
            || $("#tolerance").val() == ""
            || $("#extra_value").val() == "")
            $("#btn-register-rental").attr("disabled", true);
        else
            $("#btn-register-rental").attr("disabled", false);
	}
 
    function validateCpf(){
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
                alert('CPF inválido: ' + cpf);

                $('#cpf').val('');
                $('#cpf').focus();
                return false;
            }
            return true;
        }
        else
        {
            alert('CPF inválido: ' + cpf);
            
            $('#cpf').val('');
            $('#cpf').focus();
            return false;
        }
    }
    function toysFocus()
    {
        $("#toys").focus();
        $("#toys").select();
        $('#toys').select2('open');
    }

    function reloadRentals(){
        setTimeout(function() {
                $("#btn-register-rental").focus();
                $("#btn-register-rental").select();
                loadRentals();
                reloadRentals();
            }, 60000);  
    }

</script>
@endsection
