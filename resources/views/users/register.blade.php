@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Trocar senha</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ '/user/update' }}">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('password_old') ? ' has-error' : '' }}">
                            <label for="password_old" class="col-md-4 control-label">Senha atual</label>

                            <div class="col-md-6">
                                <input id="password_old" type="password" class="form-control" name="password_old"
                                    placeholder=" {{ $user?'Senha atual':'' }} ">

                                @if ($errors->has('password_old'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_old') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Nova senha</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password"
                                    placeholder=" {{ $user?'Atualize a senha':'' }} ">

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
