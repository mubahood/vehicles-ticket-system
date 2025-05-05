@extends('layouts.auth-layout')

@section('content')
<form action="{{ url('auth/login') }}" method="post">
    @csrf

    <div class="form-group has-feedback {{ $errors->has('username') ? 'has-error' : '' }}">
        <input
            type="text"
            name="username"
            class="form-control"
            placeholder="{{ trans('admin.username') }}"
            value="{{ old('username') }}">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        @if ($errors->has('username'))
            @foreach ($errors->get('username') as $message)
                <span class="help-block">
                    <i class="fa fa-times-circle-o"></i> {{ $message }}
                </span>
            @endforeach
        @endif
    </div>

    <div class="form-group has-feedback {{ $errors->has('password') ? 'has-error' : '' }}">
        <input
            type="password"
            name="password"
            class="form-control"
            placeholder="{{ trans('admin.password') }}">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        @if ($errors->has('password'))
            @foreach ($errors->get('password') as $message)
                <span class="help-block">
                    <i class="fa fa-times-circle-o"></i> {{ $message }}
                </span>
            @endforeach
        @endif
    </div>

    <input type="hidden" name="remember" value="1">

    <div class="row">
        <div class="col-xs-12">
            <button type="submit" class="btn btn-primary btn-block btn-flat">
                {{ trans('admin.login') }}
            </button>
        </div>
    </div>
</form>
@endsection
