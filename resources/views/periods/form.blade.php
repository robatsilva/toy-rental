@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h1>Cadastrar Periodo</h1>
                </div>
            </div>
            <div class="row">
                <form action="{{ $period?'/period/update/' . $period->id : url('/period') }}" method="post">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label for="time">Tempo:</label>
                        <input type="text" name="time" class="form-control" id="time" value="{{$period?$period->time:''}}">
                    </div>
                    <div class="form-group">
                        <label for="value">Valor:</label>
                        <input type="text" name="value" class="form-control" id="value" value="{{$period?$period->value:''}}">
                    </div>
                    <div class="form-group">
                        <label for="kiosk_id">Quiosque:</label>
                        <select name="kiosk_id" class="form-control" id="kiosk_id">
                            @foreach($kiosks as $kiosk)
                            <option value='{{ $kiosk->id }}'
                                @if ($period && $period->kiosk_id == $kiosk->id)
                                    selected="selected"
                                @endif
                            >{{ $kiosk->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="type_id">Tipo:</label>
                        <select name="type_id" class="form-control" id="type_id">
                            
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>    
    $(document).ready(function(){
        loadTypes($("#kiosk_id").val());
        $('#kiosk_id').change(function(){
            loadTypes($(this).val()) 
        });
    });
    function loadTypes(kiosk_id){
        showLoader();
        $.get('/type/' + kiosk_id, function(data){
            hideLoader();
            $('#type_id').html(data);
            @if($period && $period->type_id)
                $('#type_id').val({{ $period->type_id }});
            @endif
        })
        .fail(function(xhr, status, error) {
            hideLoader();
            showError(error, status, xhr);
        });
    }
</script>
@endsection
