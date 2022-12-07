@extends('layouts.back-end.app-seller')

@push('css_or_js')
    <link href="{{ asset('public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">{{ \App\CPU\translate('Users') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">{{ \App\CPU\translate('Edit User') }}
                </span>
        </div>
    </div>
</div>
<!-- breadcrumb -->
@endsection
@section('content')
<!-- row -->
<div class="row">
    <div class="col-lg-12 col-md-12">

        @if (count($errors) > 0)
        <div class="alert alert-danger">
            <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>{{ \App\CPU\translate('erorre') }}</strong>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-right">
                        <a class="btn btn-primary btn-sm" href="{{ route('seller.users.index') }}">{{ \App\CPU\translate('Back') }}</a>
                    </div>
                </div><br>

                {!! Form::model($user, ['method' => 'PATCH','route' => ['seller.users.update', $user->id]]) !!}
                <div class="">

                    <div class="row mg-b-20">
                        <div class="parsley-input col-md-6" id="fnWrapper">
                            <label style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"> {{ \App\CPU\translate('Frist Name') }}: <span class="tx-danger">*</span></label>
                            {!! Form::text('f_name', null, array('class' => 'form-control','required')) !!}
                        </div>

                        <div class="parsley-input col-md-6 mg-t-20 mg-md-t-0" id="lnWrapper">
                            <label style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">{{ \App\CPU\translate('Last Name') }}: <span class="tx-danger">*</span></label>
                            {!! Form::text('l_name', null, array('class' => 'form-control','required')) !!}
                        </div>
                    </div>

                </div>

                <div class="row mg-b-20">
                    <div class="parsley-input col-md-6 mg-t-20 mg-md-t-0" id="lnWrapper">
                        <label style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"> {{ \App\CPU\translate('Password') }}: <span class="tx-danger">*</span></label>
                        {!! Form::password('password', array('class' => 'form-control','required')) !!}
                    </div>

                    <div class="parsley-input col-md-6 mg-t-20 mg-md-t-0" id="lnWrapper">
                        <label style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">{{ \App\CPU\translate('Password Confirm') }}: <span class="tx-danger">*</span></label>
                        {!! Form::password('confirm-password', array('class' => 'form-control','required')) !!}
                    </div>
                </div>

                <div class="row row-sm mg-b-20">
                    {{-- <div class="col-lg-6">
                        <label style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};" class="form-label">{{ \App\CPU\translate('Status') }}</label>
                        <select style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};" name="Status" id="select-beast" class="form-control  nice-select  custom-select">
                            <option value="{{ $user->Status}}">{{ $user->Status}}</option>
                            <option value="مفعل">{{ \App\CPU\translate('Active') }}</option>
                            <option value="غير مفعل">{{ \App\CPU\translate('Not Active') }}</option>
                        </select>
                    </div> --}}
                     <div class="parsley-input col-md-6 mg-t-20 mg-md-t-0" id="lnWrapper">
                            <label style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">{{ \App\CPU\translate('E-mail') }}: <span class="tx-danger">*</span></label>
                            {!! Form::text('email', null, array('class' => 'form-control','required')) !!}
                        </div>
                </div>

                <div class="row mg-b-20">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                            <strong style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">{{ \App\CPU\translate('Type User') }}: </strong>
                            {!! Form::select('roles[]', $roles,$userRole, array('class' => 'form-control','multiple'))
                            !!}
                        </div>
                    </div>
                </div>
                <div class="mg-t-30">
                    <button class="btn btn-main-primary pd-x-20" type="submit" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">{{ \App\CPU\translate('Update') }}</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>




</div>
<!-- row closed -->
</div>
<!-- Container closed -->
</div>
<!-- main-content closed -->
@endsection
@section('js')

<!-- Internal Nice-select js-->
<script src="{{URL::asset('assets/plugins/jquery-nice-select/js/jquery.nice-select.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jquery-nice-select/js/nice-select.js')}}"></script>

<!--Internal  Parsley.min js -->
<script src="{{URL::asset('assets/plugins/parsleyjs/parsley.min.js')}}"></script>
<!-- Internal Form-validation js -->
<script src="{{URL::asset('assets/js/form-validation.js')}}"></script>
@endsection