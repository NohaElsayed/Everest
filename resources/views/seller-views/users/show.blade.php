@extends('layouts.back-end.app-seller')

@push('css_or_js')
    <link href="{{ asset('public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('seller.users.index') }}" {{ \App\CPU\translate('Status') }}>{{ \App\CPU\translate('Back') }}</a>
        </div>
    </div>
</div>

<div class="container">
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong {{ \App\CPU\translate('Status') }}>{{ \App\CPU\translate('First Name') }}:</strong>
            {{ $user->f_name }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>{{ \App\CPU\translate('Last Name') }}:</strong>
            {{ $user->l_name }}
        </div>
    </div>
    <div class="row">
    {{-- <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong {{ \App\CPU\translate('Status') }}>{{ \App\CPU\translate('First Name') }}:</strong>
            {{ $user->name }}
        </div>
    </div> --}}
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong {{ \App\CPU\translate('Status') }}>{{ \App\CPU\translate('Email') }}:</strong>
            {{ $user->email }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong {{ \App\CPU\translate('Status') }}>{{ \App\CPU\translate('Roles') }}:</strong>
            @if(!empty($user->getRoleNames()))
            @foreach($user->getRoleNames() as $v)
            <label class="badge badge-success">{{ $v }}</label>
            @endforeach
            @endif
        </div>
    </div>
</div>
</div>
@endsection