@extends('layouts.app')
<style>
    video {
        margin-top: 10px;
    }

    .videos {
        margin-top: 50px;
    }
</style>
@section('content')

    <div class="row">
        <div class="col-md-12 text-center">
            <h1>Tutoriais</h1>
        </div>
    </div>
    <div class="row videos">
        <div class="col-xs-12 col-md-3 text-center">
            <div><b>Login</b></div>
            <video width="300" controls>
                <source src="/videos/tutoriais/login.mp4" type="video/mp4">
            </video>
        </div>
        <div class="col-xs-12 col-md-3 text-center">
            <div><b>Primeiro acesso e cadastro</b></div>
            <video width="300" controls>
                <source src="/videos/tutoriais/primeiro_acesso.mp4" type="video/mp4">
            </video>
        </div>
        <div class="col-xs-12 col-md-3 text-center">
            <div><b>Edição do quiosque</b></div>
            <video width="300" controls>
                <source src="/videos/tutoriais/cadastro_quiosque.mp4" type="video/mp4">
            </video>
        </div>
        <div class="col-xs-12 col-md-3 text-center">
            <div><b>Cadastro do carrinho</b></div>
            <video width="300" controls>
                <source src="/videos/tutoriais/cadastro_carrinho.mp4" type="video/mp4">
            </video>
        </div>
    </div>
    <hr>
    <div class="row videos">
        <div class="col-xs-12 col-md-3 text-center">
            <div><b>Cadastro de períodos</b></div>
            <video width="300" controls>
                <source src="/videos/tutoriais/cadastro_periodo.mp4" type="video/mp4">
            </video>
        </div>
        <div class="col-xs-12 col-md-3 text-center">
            <div><b>Cadastro de funcionários</b></div>
            <video width="300" controls>
                <source src="/videos/tutoriais/cadastro_funcionario.mp4" type="video/mp4">
            </video>
        </div>
        <div class="col-xs-12 col-md-3 text-center">
            <div><b>Tela de aluguéis</b></div>
            <video width="300" controls>
                <source src="/videos/tutoriais/tela_alugueis.mp4" type="video/mp4">
            </video>
        </div>
        <div class="col-xs-12 col-md-3 text-center">
            <div><b>Primeiro aluguel</b></div>
            <video width="300" controls>
                <source src="/videos/tutoriais/primeiro_aluguel.mp4" type="video/mp4">
            </video>
        </div>
    </div>
    <div class="row videos">
        <div class="col-xs-12 col-md-3 text-center">
            <div><b>Aluguel período</b></div>
            <video width="300" controls>
                <source src="/videos/tutoriais/aluguel_periodo.mp4" type="video/mp4">
            </video>
        </div>
        <div class="col-xs-12 col-md-3 text-center">
            <div><b>Aluguel retorno</b></div>
            <video width="300" controls>
                <source src="/videos/tutoriais/aluguel_retorno.mp4" type="video/mp4">
            </video>
        </div>
        <div class="col-xs-12 col-md-3 text-center">
            <div><b>Aluguel tempo adicional</b></div>
            <video width="300" controls>
                <source src="/videos/tutoriais/aluguel_tempo_adicioanl.mp4" type="video/mp4">
            </video>
        </div>
        <div class="col-xs-12 col-md-3 text-center">
            <div><b>Aluguel finalizar / receber</b></div>
            <video width="300" controls>
                <source src="/videos/tutoriais/aluguel_finalizar.mp4" type="video/mp4">
            </video>
        </div>
    </div>
    <div class="row videos">
        <div class="col-xs-12 col-md-3 text-center">
            <div><b>Aceitar mais de uma forma de pagamento</b></div>
            <video width="300" controls>
                <source src="/videos/tutoriais/aluguel_mais_um_pagamento.mp4" type="video/mp4">
            </video>
        </div>
        <div class="col-xs-12 col-md-3 text-center">
            <div><b>Aluguel pausar</b></div>
            <video width="300" controls>
                <source src="/videos/tutoriais/aluguel_pausar.mp4" type="video/mp4">
            </video>
        </div>
        <div class="col-xs-12 col-md-3 text-center">
            <div><b>Aluguel cancelar</b></div>
            <video width="300" controls>
                <source src="/videos/tutoriais/aluguel_cancelar.mp4" type="video/mp4">
            </video>
        </div>
        <div class="col-xs-12 col-md-3 text-center">
            <div><b>Aluguel trocar carrinho</b></div>
            <video width="300" controls>
                <source src="/videos/tutoriais/aluguel_trocar_carrinho.mp4" type="video/mp4">
            </video>
        </div>
    </div>
    <div class="row videos">
        <div class="col-xs-12 col-md-3 text-center">
            <div><b>Aluguel duplicar cliente</b></div>
            <video width="300" controls>
                <source src="/videos/tutoriais/aluguel_mais_um_cliente.mp4" type="video/mp4">
            </video>
        </div>
    </div>

@endsection
