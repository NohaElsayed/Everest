@extends('layouts.back-end.app-seller')

@push('css_or_js')
    <link href="{{ asset('public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<!-- row -->
<div class="row">


    <div class="col-lg-12 col-md-12">

        @if (count($errors) > 0)
        <div class="alert alert-danger">
            <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>error</strong>
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
                        <a class="btn btn-primary btn-sm" href="{{ route('seller.users.create') }}" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">{{ \App\CPU\translate('Back') }}</a>
                    </div>
                </div><br>
                <form class="parsley-style-1" id="selectForm2" autocomplete="off" name="selectForm2"
                    action="{{route('seller.users.store','test')}}" method="post">
                    {{csrf_field()}}

                    <div class="">

                        <div class="row mg-b-20">
                            <div class="parsley-input col-md-6" id="fnWrapper">
                                <label style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">{{ \App\CPU\translate('First Name') }}: <span class="tx-danger">*</span></label>
                                <input class="form-control form-control-sm mg-b-20"
                                    data-parsley-class-handler="#lnWrapper" name="f_name" required="" type="text">
                            </div>

                            <div class="parsley-input col-md-6 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <label style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">{{ \App\CPU\translate('Last Name') }}: <span class="tx-danger">*</span></label>
                                <input class="form-control form-control-sm mg-b-20"
                                    data-parsley-class-handler="#lnWrapper" name="l_name" required="" type="text">
                            </div>
                        </div>

                    </div>

                    <div class="row mg-b-20">
                        <div class="parsley-input col-md-6 mg-t-20 mg-md-t-0" id="lnWrapper">
                            <label style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">{{ \App\CPU\translate('Password') }}: <span class="tx-danger">*</span></label>
                            <input class="form-control form-control-sm mg-b-20" data-parsley-class-handler="#lnWrapper"
                                name="password" required="" type="password">
                        </div>

                        <div class="parsley-input col-md-6 mg-t-20 mg-md-t-0" id="lnWrapper">
                            <label style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">{{ \App\CPU\translate('Password Confirm') }} : <span class="tx-danger">*</span></label>
                            <input class="form-control form-control-sm mg-b-20" data-parsley-class-handler="#lnWrapper"
                                name="confirm-password" required="" type="password">
                        </div>
                    </div>

                    <div class="row row-sm mg-b-20">
                        {{-- <div class="col-lg-6">
                            <label class="form-label" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">{{ \App\CPU\translate('Status') }}</label>
                            <select name="Status" id="select-beast" class="form-control  nice-select  custom-select" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                <option value="مفعل">{{ \App\CPU\translate('Active') }}</option>
                                <option value="غير مفعل">{{ \App\CPU\translate('Not Active') }}</option>
                            </select>
                        </div> --}}
                        <div class="parsley-input col-md-6 mg-t-20 mg-md-t-0" id="lnWrapper">
                                <label style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">{{ \App\CPU\translate('Email') }}: <span class="tx-danger">*</span></label>
                                <input class="form-control form-control-sm mg-b-20"
                                    data-parsley-class-handler="#lnWrapper" name="email" required="" type="email">
                            </div>
                    </div>

                    <div class="row mg-b-20">
                        <div class="col-xs-12 col-md-12">
                         {{-- @if ($user->roles_name == 'seller') --}}
                            <div class="form-group">
                                <label class="form-label" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">{{ \App\CPU\translate('Role User') }}</label>
                                {{-- @if ($roles!= "seller") --}}
                                {!! Form::select('roles_name[]', $roles,[], array('class' => 'form-control','multiple')) !!}
                            </div>
                            {{-- @endif --}}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                        <button class="btn btn-main-primary pd-x-20" type="submit" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">{{ \App\CPU\translate('Save') }}</button>
                    </div>
                </form>
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