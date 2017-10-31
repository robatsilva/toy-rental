@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h1>Periodos Cadastrados</h1>
                </div>
                <div class="col-md-12 text-right">
                    <a href="period/create" class="btn btn-primary" id="btn-new-period" class="btn btn-primary">Novo</a>
                </div>
            </div>
            <div class="row">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tempo</th>
                            <th>Valor</th>
                            <th>Quiosque</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($periods as $period)
                            <tr>
                                <td>{{ $period->time }}</td>
                                <td>{{ $period->value }}</td>
                                <td>{{ $period->kiosk->name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
