@extends('layouts.app')
<style>
    .btn-file {
        position: relative;
        overflow: hidden;
    }
    .btn-file input[type=file] {
        position: absolute;
        top: 0;
        right: 0;
        min-width: 100%;
        min-height: 100%;
        font-size: 100px;
        text-align: right;
        filter: alpha(opacity=0);
        opacity: 0;
        outline: none;
        background: white;
        cursor: inherit;
        display: block;
    }

    #img-upload{
        margin-top: 30px;
        width: 80px;
    }
</style>
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h1>Cadastrar Brinquedo</h1>
                </div>
            </div>
            <div class="row">
                <form action="{{ $toy?'/toy/update/' . $toy->id : url('/toy') }}" method="post" enctype="multipart/form-data">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label for="code">Código:</label>
                        <input type="text" name="code" class="form-control" id="code" value="{{$toy?$toy->code:''}}">
                    </div>
                    <div class="form-group">
                        <label for="description">Descrição:</label>
                        <input type="text" name="description" class="form-control" id="description" value="{{$toy?$toy->description:''}}">
                    </div>
                    <div class="form-group">
                        <label for="toy_id">Quiosque:</label>
                        <select name="kiosk_id" class="form-control" id="kiosk_id">
                            @foreach($kiosks as $kiosk)
                            <option value='{{ $kiosk->id }}'
                                @if ($toy && $toy->kiosk_id == $kiosk->id)
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
                    <div class="form-group">
                        <label>Upload Image</label>
                        <div class="input-group">
                            <span class="input-group-btn">
                                <span class="btn btn-default btn-file">
                                    Browse… <input type="file" id="imgInp" accept="image/*" name="image">
                                </span>
                            </span>
                            <input type="text" class="form-control" readonly>
                        </div>
                        <img id='img-upload' 
                            src="{{ $toy && $toy->image ? '/images/toys-img/' . $toy->image : '/images/imagem_indisponivel.png' }}" 
                        />
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
    $(document).ready( function() {
        setKioskListener();
        loadTypes($("#kiosk_id").val());
    	$(document).on('change', '.btn-file :file', function() {
		var input = $(this),
			label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
		input.trigger('fileselect', [label]);
		});

		$('.btn-file :file').on('fileselect', function(event, label) {
		    
		    var input = $(this).parents('.input-group').find(':text'),
		        log = label;
		    
		    if( input.length ) {
		        input.val(log);
		    } else {
		        if( log ) alert(log);
		    }
	    
		});
		function readURL(input) {
		    if (input.files && input.files[0]) {
		        var reader = new FileReader();
		        
		        reader.onload = function (e) {
		            $('#img-upload').attr('src', e.target.result);
		        }
		        
		        reader.readAsDataURL(input.files[0]);
		    }
		}

		$("#imgInp").change(function(){
		    readURL(this);
		}); 	
	});

    function setKioskListener(){
        $('#kiosk_id').change(function(){
           loadTypes($(this).val()) 
        });
    }

    function loadTypes(kiosk_id){
        showLoader();
        $.get('/type/' + kiosk_id, function(data){
            hideLoader();
            $('#type_id').html(data);
            @if($toy && $toy->type_id)
                $('#type_id').val({{ $toy->type_id }});
            @endif
        })
        .fail(function(xhr, status, error) {
            hideLoader();
            showError(error, status, xhr);
        });
    }
</script>
@endsection
