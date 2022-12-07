@extends('layouts.back-end.app-seller')

@push('css_or_js')
    <link href="{{ asset('public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')


@endsection
@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">{{ \App\CPU\translate('Permission') }} </h4>
            {{-- <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ 
                {{ \App\CPU\translate('Permission') }} </span> --}}
        </div>
    </div>
</div>
<!-- breadcrumb -->


<!-- row -->
<div class="row">
    <div class="col-md-12">
        <div class="card mg-b-20">
            <div class="card-body">
                <div class="main-content-label mg-b-5">
                    <div class="pull-right">
                        <a class="btn btn-primary btn-sm" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};" href="{{ route('seller.roles.index') }}">{{ \App\CPU\translate('Back') }} </a>
                    </div>
                </div>
                <div class="row">
                    <!-- col -->
                    <div class="col-lg-4">
                        <ul id="treeview1">
                            <li><a style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};" href="#">{{ $role->name }}</a>
                                <ul>
                                    @if(!empty($rolePermissions))
                                    @foreach($rolePermissions as $v)
                                    <li>{{ $v->name }}</li>
                                    @endforeach
                                    @endif
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <!-- /col -->
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
<script src="{{URL::asset('assets/plugins/treeview/treeview.js')}}"></script>

@endsection