    @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h1>Quiosques Cadastrados</h1>
                </div>
            </div>
            <div class="row">
                <table>
                @foreach ($kiosks as $kiosk)
                    <tr>
                        <td>{{ $kiosk->name }}</td>
                    </tr>
                @endforeach
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
