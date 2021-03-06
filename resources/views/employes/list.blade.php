@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h1>Funcionários Cadastrados</h1>
                </div>
                <div class="col-md-12 text-right">
                    <a href="employe/create" class="btn btn-primary" id="btn-new-toy" class="btn btn-primary">Novo</a>
                </div>
            </div>
            <div class="row">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Status</th>
                            <th>Quiosque</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employes as $employe)
                            <tr>
                                <td>{{ $employe->name }}</td>
                                <td>{{ $employe->email }}</td>
                                <td>{{ $employe->status_employe }}</td>
                                <td>{{ $employe->kiosk->name }}</td>
                                <td>
                                    <a href="/employe/{{$employe->id}}" class="btn btn-default">
                                        <span class="glyphicon glyphicon-pencil" title="Editar" aria-hidden="true"></span>
                                    </a>
                                    <a href="/employe/toogle/{{$employe->id}}" class="btn btn-default">
                                        @if($employe->status)
                                        <i class="fa fa-toggle-on" title="Desativar" aria-hidden="true"></i>
                                        @else
                                        <i class="fa fa-toggle-off" title="Ativar" aria-hidden="true"></i>
                                        @endif
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
