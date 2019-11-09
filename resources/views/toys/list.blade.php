@extends('layouts.app')

<style>
    td{
        vertical-align: middle !important;
    }
</style>
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h1>Brinquedos Cadastrados</h1>
                </div>
                @if (!Auth::guest() && Auth::user()->permissions()->get()->where('name', 'franqueador')->first())
                    <div class="col-md-12 text-right">
                        <a href="toy/create" class="btn btn-primary" id="btn-new-toy" class="btn btn-primary">Novo</a>
                    </div>
                @endif
            </div>
            <div class="row">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Imagem</th>
                            <th>Código</th>
                            <th>Descrição</th>
                            <th>Status</th>
                            <th>Quiosque</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($toys as $toy)
                            <tr>
                                <td><img style="max-height: 100px;"
                                    src="{{ $toy->image ? '/images/toys-img/' . $toy->image : '/images/imagem_indisponivel.png' }}" 
                                /></td>
                                <td>{{ $toy->code }}</td>
                                <td>{{ $toy->description }}</td>
                                <td>{{ $toy->status_toy? "Ativo" : "Inativo" }}</td>
                                <td>{{ $toy->kiosk->name }}</td>
                                <td>
                                    @if (!Auth::guest() && Auth::user()->permissions()->get()->where('name', 'franqueador')->first())
                                        <a href="/toy/{{$toy->id}}" class="btn btn-default">
                                            <span class="glyphicon glyphicon-pencil" title="Editar" aria-hidden="true"></span>
                                        </a>
                                        <a href="/toy/toogle/{{$toy->id}}" class="btn btn-default">
                                            @if($toy->status_toy)
                                            <i class="fa fa-toggle-on" title="Desativar" aria-hidden="true"></i>
                                            @else
                                            <i class="fa fa-toggle-off" title="Ativar" aria-hidden="true"></i>
                                            @endif
                                        </a>
                                    @endif
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
