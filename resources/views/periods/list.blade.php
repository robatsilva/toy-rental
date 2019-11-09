@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h1>Periodos Cadastrados</h1>
                </div>
                @if (!Auth::guest() && Auth::user()->permissions()->get()->where('name', 'franqueador')->first())
                    <div class="col-md-12 text-right">
                        <a href="period/create" class="btn btn-primary" id="btn-new-period" class="btn btn-primary">Novo</a>
                    </div>
                @endif
            </div>
            <div class="row">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tempo</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th>Quiosque</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($periods as $period)
                            <tr>
                                <td>{{ $period->time }}</td>
                                <td>{{ $period->value }}</td>
                                <td>{{ $period->status_period? "Ativo" : "Inativo" }}</td>
                                <td>{{ $period->kiosk->name }}</td>
                                <td>
                                    <!-- <a href="/period/{{$period->id}}" class="btn btn-default">
                                        <span class="glyphicon glyphicon-pencil" title="Editar" aria-hidden="true"></span>
                                    </a> -->
                                    <a href="/period/toogle/{{$period->id}}" class="btn btn-default">
                                        @if($period->status_period)
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
