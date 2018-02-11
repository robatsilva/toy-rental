@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="row">
                <div class="col-md-12 text-center">
                    @if($kiosk)
                        <h1>Editar Quiosque</h1>
                    @else
                        <h1>Cadastrar Quiosque</h1>
                    @endif
                </div>
            </div>
            <div class="row">
                <form id="form-kiosk" action="{{$kiosk?'/kiosk/update/' . $kiosk->id : '/kiosk'}}" method="post">
                    {!! csrf_field() !!}
                    <div class="form-group col-md-5">
                        <label for="name">Nome do quiosque:</label>
                        <input name="name" class="form-control" value="{{$kiosk?$kiosk->name:''}}" id="name">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="cnpj">CNPJ:</label>
                        <input name="cnpj" class="form-control" value="{{$kiosk?$kiosk->cnpj:'88789465000114'}}" id="cnpj">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="tolerance">Tolerância:</label>
                        <input name="tolerance" class="form-control integer" value="{{$kiosk?$kiosk->tolerance:''}}" id="tolerance">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="extra-value">R$ minuto extra:</label>
                        <input name="extra-value" class="form-control decimal" value="{{$kiosk?$kiosk->extra_value:''}}" id="extra-value">
                    </div>
                    <div class="form-group col-md-10">
                        <label for="address">Endereço:</label>
                        <input name="address" class="form-control" value="{{$kiosk?$kiosk->address:''}}" id="address">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="address_number">Número:</label>
                        <input name="address_number" class="form-control" value="{{$kiosk?$kiosk->address_number:''}}" id="address_number">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="address_district">Bairro:</label>
                        <input name="address_district" class="form-control" value="{{$kiosk?$kiosk->address_district:''}}" id="address_district">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="address_city">Cidade:</label>
                        <input name="address_city" class="form-control" value="{{$kiosk?$kiosk->address_city:''}}" id="address_city">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="address_state">Estado:</label>
                        <input name="address_state" class="form-control" value="{{$kiosk?$kiosk->address_state:''}}" id="address_state">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="postalcode">CEP:</label>
                        <input name="postalcode" class="form-control" value="{{$kiosk?$kiosk->postalcode:''}}" id="postalcode">
                    </div>
                    <hr>
                    <h2>Dados para pagamento</h2>
                    <input type="hidden" id="hash" name="hash">
                    <input type="hidden" id="token" name="card_token">
                    <h3>Dados do titular do cartão</h3>
                    <div class="form-group col-md-6">
                        <label for="card_name">Nome impresso no cartão</label>
                        <input class="form-control" id="card_name" name="card_name" value="{{$kiosk?$kiosk->cnpj:$user->name}}">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="card_doc">Documento:</label>
                        <input name="card_doc" class="form-control" value="{{$kiosk?$kiosk->cnpj:$user->cnpj}}" id="card_doc">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="card_birth">Data de nascimento:</label>
                        <input name="card_birth" class="form-control" value="{{$kiosk?$kiosk->birth:$user->birth}}" id="card_birth">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="card_phone">Telefone:</label>
                        <input name="card_phone" class="form-control" value="{{$kiosk?$kiosk->area_code . $kiosk->phone:$user->area_code . $user->phone}}" id="card_phone">
                    </div>
                    <h3>Dados do cartão</h3>
                    <div class="form-group col-md-4">
                        <label for="card_number">Número do cartão</label>
                        <input class="form-control" id="card_number" name="card_number" placeholder="xxxx xxxx xxxx xxxx">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="card_date">Data de validade</label>
                        <input class="form-control" id="card_date" name="card_date" placeholder="mm/yyyy">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="card_ccv">Cõdigo de segurança</label>
                        <input class="form-control" id="card_ccv" name="card_ccv"placeholder="xxxx">
                    </div>
                    <div class="form-group col-md-12">
                        <img id="card_img"></img>
                    </div>
                </form>
                <button class="btn btn-primary" id="salvar">Salvar</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript" src=
"https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js"></script>

<script>
    var creditCardBrands;
    var creditCardBrand;
    $(document).ready(function(){
        $('#card_number').mask('0000 0000 0000 0000');
        $('#card_date').mask('00/0000');
        $('#card_ccv').mask('0000');
        $('.integer').mask('#');
        $('.decimal').mask('000.000,00', {reverse: true});
        showLoader();
        $.get('/payment/session', function(data){
            PagSeguroDirectPayment.setSessionId(data.id);
            PagSeguroDirectPayment.getPaymentMethods({
            amount: 120.00,
            success: function(response) {
                hideLoader();
                creditCardBrands = $.map(response.paymentMethods.CREDIT_CARD.options, function(el) { return el });
            },
            error: function(response) {
                alert(response);
                //tratamento do erro
            }
        },'JSON');

        $("input#card_number").keyup(function(){
            if($("input#card_number").val().length >= 6){
                PagSeguroDirectPayment.getBrand({
                cardBin: $("input#card_number").val().replace(" ", ""),
                success: function(response) {
                    creditCardBrands.forEach(function(c){
                        if(c.name == response.brand.name.toUpperCase()){
                            creditCardBrand = c.name;
                            $('#card_img').attr('src', 'https://stc.pagseguro.uol.com.br/' + c.images.MEDIUM.path);
                            $('#card_ccv').attr('placeholder', response.brand.cvvSize == '3' ? 'xxx' : 'xxxx');
                        }
                    });
                },
                error: function(response) {
                    //tratamento do erro
                },
                complete: function(response) {
                    //tratamento comum para todas chamadas
                }
            });
            }
        });

        $("#salvar").click(function(){
            showLoader();
            var param = {
                cardNumber: $("input#card_number").val().replace(new RegExp(" ", 'g'), ""),
                cvv: $("input#card_ccv").val(),
                expirationMonth: $("input#card_date").val().substr(0,2),
                expirationYear: $("input#card_date").val().substr(3,4),
                success: function(response) {
                    $("#token").val(response.card.token);
                    $("#hash").val(PagSeguroDirectPayment.getSenderHash());
                    var body = $("#form-kiosk").serialize();
                    console.log("sucess");
                    console.log(response);
                    $.post('/payment/pre-approvals', body ,function(data){
                        hideLoader();
                        alert();
                    })
                    .fail(function(xhr, status, error) {
                        hideLoader();
                        alert(status + ' - ' + error);
                    });
                    
                },
                error: function(response) {
                    alert(JSON.stringfy(response));
                    console.log("error");
                    console.log(response);

                }
            }

            PagSeguroDirectPayment.createCardToken(param);

        });
    });

});
</script>
@endsection
