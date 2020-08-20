@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h1>Quiosques Cadastrados</h1>
                </div>
                <!-- <div class="col-md-12 text-right">
                    <a href="kiosk/create" class="btn btn-primary" id="btn-new-kiosk" class="btn btn-primary">Novo</a>
                </div> -->
            </div>
            <div class="row">
                <!-- Clique na <span class="glyphicon glyphicon-star" title="Principal" aria-hidden="true"></span> para trocar o quiosque operado -->
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <!-- <th>Tolerância</th>
                            <th>R$ minuto extra</th> -->
                            <th>Status</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kiosks as $kiosk)
                            <tr>
                                <td>{{ $kiosk->name }}</td>
                                <!-- <td>{{ $kiosk->tolerance }}</td>
                                <td>{{ $kiosk->extra_value }}</td> -->
                                <td>{{ $kiosk->status?"Ativo" : "Inativo" }}</td>
                                <td>
                                    <a href="/kiosk/{{$kiosk->id}}" class="btn btn-default">
                                        <span class="glyphicon glyphicon-pencil" title="Editar" aria-hidden="true"></span>
                                    </a>
                                    
                                    @if(!$kiosk->pivot->default)
                                    <a href="/kiosk/toogle/{{$kiosk->id}}" class="btn btn-default">
                                        @if($kiosk->status)
                                        <i class="fa fa-toggle-on" title="Desativar" aria-hidden="true"></i>
                                        @else
                                        <i class="fa fa-toggle-off" title="Ativar" aria-hidden="true"></i>
                                        @endif
                                    </a>
                                    @if($kiosk->status)
                                    <a href="/kiosk/default/{{$kiosk->id}}" class="btn btn-default">
                                        <span class="glyphicon glyphicon-star" title="Principal" aria-hidden="true"></span>
                                    </a>
                                    @endif
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
