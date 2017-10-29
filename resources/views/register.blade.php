@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="col-md-6 text-center">
                <a href="kiosk">
                    <img src="/images/kiosk.jpeg" class="img-responsive center-block">
                    <h3>Quiosques</h3>
                </a>
            </div>
            <div class="col-md-6 text-center">
                <a href="toy">
                    <img src="/images/cars.jpeg" class="img-responsive center-block">
                    <h3>Brinquedos</h3>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
