    @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h1>Quiosques Cadastrados</h1>
                </div>
                <div class="col-md-12 text-right">
                <a href="kiosk/create" class="btn btn-primary" id="btn-new-kiosk" class="btn btn-primary">Novo</a>
                </div>
            </div>
            <div class="row">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Tolerância</th>
                            <th>R$ minuto extra</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kiosks as $kiosk)
                            <tr>
                                <td>{{ $kiosk->name }}</td>
                                <td>{{ $kiosk->tolerance }}</td>
                                <td>{{ $kiosk->extra_value }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
