@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h1>Cadastrar Brinquedo</h1>
                </div>
            </div>
            <div class="row">
                <form action="/toy" method="post">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label for="code">Código:</label>
                        <input type="text" name="code" class="form-control" id="code">
                    </div>
                    <div class="form-group">
                        <label for="description">Descrição:</label>
                        <input type="text" name="description" class="form-control" id="description">
                    </div>
                    <div class="form-group">
                        <label for="toy_id">Quiosque:</label>
                        <select name="kiosk_id" class="form-control" id="kiosk_id">
                            @foreach($kiosks as $kiosk)
                            <option value='{{ $kiosk->id }}'>{{ $kiosk->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
