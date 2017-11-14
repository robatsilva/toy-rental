@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h1>Cadastrar Cliente</h1>
                </div>
            </div>
            <div class="row">
                <form action="/customer" method="post">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label for="name">Nome:</label>
                        <input type="name" name="name" class="form-control" id="name">
                    </div>
                    <div class="form-group">
                        <label for="cpf">CPF:</label>
                        <input type="text" name="cpf" class="form-control" id="cpf">
                    </div>
                    <div class="form-group">
                        <label for="kiosk_id">Quiosque:</label>
                        <select name="kiosk_id" class="form-control" id="kiosk_id">
                        @foreach ($kiosks as $kiosk)
                            <option value='{{ $kiosk.id }}'>{{ $kiosk.name }}</option>
                        @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-default">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
