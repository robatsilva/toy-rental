@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h1>Brinquedos Cadastrados</h1>
                </div>
                <div class="col-md-12 text-right">
                    <a href="toy/create" class="btn btn-primary" id="btn-new-toy" class="btn btn-primary">Novo</a>
                </div>
            </div>
            <div class="row">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Descrição</th>
                            <th>Quiosque</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($toys as $toy)
                            <tr>
                                <td>{{ $toy->code }}</td>
                                <td>{{ $toy->description }}</td>
                                <td>{{ $toy->kiosk->name }}</td>
                                <td>
                                    <a href="/toy/{{$toy->id}}" class="btn btn-default">
                                        <span class="glyphicon glyphicon-pencil" title="Editar" aria-hidden="true"></span>
                                    </a>
                                    <a href="/toy/remove/{{$toy->id}}" class="btn btn-default">
                                        <span class="glyphicon glyphicon-remove" title="Excluir" aria-hidden="true"></span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
