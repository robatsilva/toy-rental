@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h1>Cadastrar Quiosque</h1>
                </div>
            </div>
            <div class="row">
                <form action="/kiosk" method="post">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label for="name">Nome:</label>
                        <input name="name" class="form-control" id="name">
                    </div>
                    <div class="form-group">
                        <label for="tolerance">Tolerância:</label>
                        <input name="tolerance" class="form-control" id="tolerance">
                    </div>
                    <div class="form-group">
                        <label for="extra-value">R$ minuto extra:</label>
                        <input name="extra-value" class="form-control" id="extra-value">
                    </div>
                    <button type="submit" class="btn btn-default">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
