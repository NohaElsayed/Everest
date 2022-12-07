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
            <h4 class="content-title mb-0 my-auto">المستخدمين</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ قائمة
                المستخدمين</span>
        </div>
    </div>
</div>
<!-- breadcrumb -->
@endsection

@section('content')

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<!-- row opened -->
<div class="row row-sm">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header pb-0">
                <div class="col-sm-1 col-md-2">
                    {{-- @can('اضافة مستخدم') --}}
                        <a class="btn btn-primary btn-sm" href="{{ route('seller.users.create') }}" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">{{ \App\CPU\translate('Add User') }}</a>
                    {{-- @endcan --}}
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive hoverable-table">
                    <table class="table table-hover" id="example1" data-page-length='50' style=" text-align: center;text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                        <thead>
                            <tr>
                                <th class="wd-10p border-bottom-0">#</th>
                                <th class="wd-15p border-bottom-0" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"> {{ \App\CPU\translate('Nmae') }}</th>
                                <th class="wd-20p border-bottom-0" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">{{ \App\CPU\translate('E- mail') }}</th>
                                {{-- <th class="wd-15p border-bottom-0" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"> {{ \App\CPU\translate('Status') }}</th> --}}
                                <th class="wd-15p border-bottom-0"style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">{{ \App\CPU\translate('Type User') }}</th>
                                <th class="wd-10p border-bottom-0" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">{{ \App\CPU\translate('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $key => $user)
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $user->f_name }} {{ $user->l_name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                      
                                    {{-- </td>
                                    {{ $user->status }}
                                    <td> --}}
                                        @if (!empty($user->getRoleNames()))
                                            @foreach ($user->getRoleNames() as $v)
                                                <label class="badge badge-success">{{ $v }}</label>
                                            @endforeach
                                        @endif
                                    </td>

                                    <td>
                                        {{-- @can('تعديل مستخدم') --}}
                                            <a href="{{ route('seller.users.edit', $user->id) }}" class="btn btn-sm btn-info"
                                                title="{{ \App\CPU\translate('Edit') }}"> {{ \App\CPU\translate('Edit') }}<i class="las la-pen"></i></a>
                                        {{-- @endcan

                                        @can('حذف مستخدم') --}}
                                            <a class="modal-effect btn btn-sm btn-danger" data-effect="effect-scale"
                                                data-user_id="{{ $user->id }}" data-username="{{ $user->name }}"
                                                data-toggle="modal" href="#modaldemo8"> {{ \App\CPU\translate('Delete') }}<i class="las la-trash"></i></a>
                                        {{-- @endcan --}}
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

    <!-- Modal effects -->
    <div class="modal" id="modaldemo8">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">{{ \App\CPU\translate('Delete User') }}</h6><button aria-label="Close" class="close"
                        data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="{{ route('seller.users.destroy', 'test') }}" method="post">
                    {{ method_field('delete') }}
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <p>{{ \App\CPU\translate('Are you Sure') }}</p><br>
                        <input type="hidden" name="user_id" id="user_id" value="">
                        <input class="form-control" name="username" id="username" type="text" readonly>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ \App\CPU\translate('Cancel') }}</button>
                        <button type="submit" class="btn btn-danger">{{ \App\CPU\translate('Save') }}</button>
                    </div>
            </div>
            </form>
        </div>
    </div>
</div>

</div>
<!-- /row -->
</div>
<!-- Container closed -->
</div>
<!-- main-content closed -->
@endsection
@section('js')
<!-- Internal Data tables -->
<script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js') }}"></script>
<!--Internal  Datatable js -->
<script src="{{ URL::asset('assets/js/table-data.js') }}"></script>
<!--Internal  Notify js -->
<script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>
<!-- Internal Modal js-->
<script src="{{ URL::asset('assets/js/modal.js') }}"></script>

<script>
    $('#modaldemo8').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var user_id = button.data('user_id')
        var username = button.data('username')
        var modal = $(this)
        modal.find('.modal-body #user_id').val(user_id);
        modal.find('.modal-body #username').val(username);
    })

</script>


@endsection
