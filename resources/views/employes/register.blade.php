@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Cadastro de funcionário</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ $employe?'/employe/update/' . $employe->id : url('/employe') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Nome</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name')?old('name'):$employe?$employe->name:'' }}">

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email')?old('email'):$employe?$employe->email:'' }}">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Senha</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password"
                                    placeholder=" {{ $employe?'Atualize a senha':'' }} ">

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label for="password-confirm" class="col-md-4 control-label">Confirme a senha</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation">

                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="kiosk_id" class="col-md-4 control-label">Quiosque:</label>
                            <div class="col-md-6">
                                <select name="kiosk_id" class="form-control" id="kiosk_id">
                                    @foreach($kiosks as $kiosk)
                                        <option value='{{ $kiosk->id }}'
                                            @if ($employe && $employe->kiosk_id == $kiosk->id)
                                                selected="selected"
                                            @endif
                                        >{{ $kiosk->name }}</option>
                                    @endforeach
                                </select>    
                            </div>
                        </div>                        
                        <div class="form-group">
                            <label for="type" class="col-md-4 control-label">Permissão:</label>
                            <div class="col-md-6">
                                <select name="type" class="form-control" id="type">
                                        <option value='2'
                                            @if ($employe && $employe->permissions()->get()->where('name', 'funcionario')->first())
                                                selected="selected"
                                            @endif
                                        >Funcionário</option>
                                        <option value='3'
                                            @if ($employe && $employe->permissions()->get()->where('name', 'relatorio')->first())
                                                selected="selected"
                                            @endif
                                        >Relatório</option>
                                        <option value='4'
                                            @if ($employe && $employe->permissions()->get()->where('name', 'shopping')->first())
                                                selected="selected"
                                            @endif
                                        >Shopping</option>
                                </select>    
                            </div>
                        </div>                        

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-user"></i> Salvar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
