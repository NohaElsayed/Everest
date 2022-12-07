@extends('layouts.back-end.app-seller')

@push('css_or_js')
    <link href="{{ asset('public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<!-- row -->
<div class="row row-sm">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header pb-0">
                <div class="d-flex justify-content-between">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-right">
                            {{-- @can('اضافة صلاحية') --}}
                                <a class="btn btn-primary btn-sm" href="{{ route('seller.roles.create') }}" 
                                style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">{{ \App\CPU\translate('Add') }} </a>
                            {{-- @endcan --}}
                        </div>
                    </div>
                    <br>
                </div>

            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table mg-b-0 text-md-nowrap table-hover ">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ \App\CPU\translate('Name') }} </th>
                                <th>{{ \App\CPU\translate('Action') }} </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $key => $role)
                                <tr>
                                   @if ($role->name !== 'seller')
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        {{-- @can('عرض صلاحية') --}}
                                            <a class="btn btn-success btn-sm" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                                href="{{ route('seller.roles.show', $role->id) }}">{{ \App\CPU\translate('View') }} </a>
                                        {{-- @endcan --}}
                                        {{-- @can('تعديل صلاحية') --}}
                                            <a class="btn btn-primary btn-sm"
                                                href="{{ route('seller.roles.edit', $role->id) }}">{{ \App\CPU\translate('Edit') }} </a>
                                        {{-- @endcan --}}

                                        {{-- @if ($role->name !== 'seller') --}}
                                            {{-- @can('حذف صلاحية') --}}
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['seller.roles.destroy',
                                                $role->id], 'style' => 'display:inline']) !!}
                                                {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-sm']) !!}
                                                {!! Form::close() !!}
                                            {{-- @endcan --}}
                                        @endif


                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!--/div-->
</div>
<!-- row closed -->
</div>
<!-- Container closed -->
</div>
<!-- main-content closed -->
@endsection
@section('js')
<!--Internal  Notify js -->
<script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>
@endsection
