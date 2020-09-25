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
                        <input name="cnpj" class="cnpj form-control" value="{{$kiosk?$kiosk->cnpj:$user->cnpj}}" id="cnpj">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="credit-tax">Taxa crédito:</label>
                        <input name="credit-tax" class="form-control tax" value="{{$kiosk?$kiosk->credit_tax:''}}" id="credit-tax">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="debit-tax">Taxa débito:</label>
                        <input name="debit-tax" class="form-control tax" value="{{$kiosk?$kiosk->debit_tax:''}}" id="debit-tax">
                    </div>
                    @if (!Auth::guest() && Auth::user()->permissions()->get()->where('name', 'franqueador')->first())
                    <div class="form-group col-md-2">
                        <label for="royalty">Taxa royalty:</label>
                        <input name="royalty" class="form-control tax" value="{{$kiosk?$kiosk->royalty:''}}" id="royalty">
                    </div>
                    @endif
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
                        <select name="address_state" class="form-control" value="{{$kiosk?$kiosk->address_state:''}}" id="address_state">
                            <option>Selecione...</option>
                            <option value="AC">Acre</option>
                            <option value="AL">Alagoas</option>
                            <option value="AP">Amapá</option>
                            <option value="AM">Amazonas</option>
                            <option value="BA">Bahia</option>
                            <option value="CE">Ceará</option>
                            <option value="DF">Distrito Federal</option>
                            <option value="ES">Espírito Santo</option>
                            <option value="GO">Goiás</option>
                            <option value="MA">Maranhão</option>
                            <option value="MT">Mato Grosso</option>
                            <option value="MS">Mato Grosso do Sul</option>
                            <option value="MG">Minas Gerais</option>
                            <option value="PA">Pará</option>
                            <option value="PB">Paraíba</option>
                            <option value="PR">Paraná</option>
                            <option value="PE">Pernambuco</option>
                            <option value="PI">Piauí</option>
                            <option value="RJ">Rio de Janeiro</option>
                            <option value="RN">Rio Grande do Norte</option>
                            <option value="RS">Rio Grande do Sul</option>
                            <option value="RO">Rondônia</option>
                            <option value="RR">Roraima</option>
                            <option value="SC">Santa Catarina</option>
                            <option value="SP">São Paulo</option>
                            <option value="SE">Sergipe</option>
                            <option value="TO">Tocantins</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="postalcode">CEP:</label>
                        <input name="postalcode" class="form-control" value="{{$kiosk?$kiosk->postalcode:''}}" id="postalcode">
                    </div>
                    <hr>
                </form>
                <div class="form-group col-md-12">
                    <button class="btn btn-primary" id="salvar">Salvar</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    var creditCardBrands;
    var creditCardBrand;
    $(document).ready(function(){
        $('.cnpj').mask('00.000.000/0000-00');
        $('#card_doc').mask('00.000.000/0000-00');
        $('#card_number').mask('0000 0000 0000 0000');
        $('#card_date').mask('00/0000');
        $('#card_ccv').mask('0000');
        $('.integer').mask('#');
        $('.decimal').mask('000.000,00', {reverse: true});
        $('.tax').mask('0,00');
        $('#area_code').mask('00');
        $('#card_phone').mask('00 000000000');
        $('#postalcode').mask('00000-000');
      
        $("#salvar").click(function(){
            showLoader();
            $("#form-kiosk").submit();
        });
    });

</script>
@endsection
