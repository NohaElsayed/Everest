@extends('layouts.back-end.app-seller')

@section('title',\App\CPU\translate('Service Order'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('seller.dashboard.index')}}">{{\App\CPU\translate('Dashboard')}}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">{{\App\CPU\translate('Services Order' )}}</li>

            </ol>
        </nav>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row flex-between justify-content-between align-items-center flex-grow-1">
                            <div class="col-12 mb-1 col-md-4">
                                <h5>{{ \App\CPU\translate('Service')}} {{ \App\CPU\translate('Table')}}</h5>

                            </div>


                            {{-- <div class="col-12 mb-1 col-md-5">
                                <form action="{{ url()->current() }}" method="GET">
                                    <!-- Search -->
                                    <div class="input-group input-group-merge input-group-flush">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                            placeholder="{{\App\CPU\translate('Search by Service Name')}}" aria-label="Search orders" value="{{ $search }}" required>
                                        <button type="submit" class="btn btn-primary">{{\App\CPU\translate('search')}}</button>
                                    </div>
                                    <!-- End Search -->
                                </form>
                            </div> --}}
                            {{-- <div class="col-12 col-md-3">
                                <a href="{{route('seller.service.add-new')}}" class="btn btn-primary float-{{Session::get('direction') === "rtl" ? 'left' : 'right'}}">
                                    <i class="tio-add-circle"></i>
                                    <span class="text">{{\App\CPU\translate('Add new Service')}}</span>
                                </a>
                            </div> --}}
                        </div>

                    </div>
                    <div class="card-body" style="padding: 0">
                        <div class="table-responsive">
                            <table id="datatable"
                                   style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                   class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                                   style="width: 100%">
                                <thead class="thead-light">
                                <tr>
                                    <th>{{\App\CPU\translate('SL#')}}</th>
                                    <th>{{\App\CPU\translate('Service Name')}}</th>
{{--                                    <th>{{\App\CPU\translate('purchase_price')}}</th>--}}
                                    <th>{{\App\CPU\translate('Phone Customer')}}</th>
                                    <th>{{\App\CPU\translate('notes')}}</th>
                                    {{-- <th>{{\App\CPU\translate('Active')}} {{\App\CPU\translate('status')}}</th>
                                    <th style="width: 5px" class="text-center">{{\App\CPU\translate('Action')}}</th> --}}
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($services as $k=>$p)
                                    <tr>
                                        <th scope="row">#</th>
                                        <td>{{$p ->name}} </td>
{{--                                        <td>--}}
{{--                                            {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($p['purchase_price']))}}--}}
{{--                                        </td>--}}
                                        <td>{{$p ->phone}} </td>
                                        <td>{{$p ->notes}} </td>
                                        <td>
{{--                                            <a class="btn btn-warning btn-sm" title="{{ \App\CPU\translate('barcode') }}"--}}
{{--                                                    href="{{ route('seller.service.barcode', [$p['id']]) }}">--}}
{{--                                                    <i class="tio-barcode"></i>--}}
{{--                                                </a>--}}

{{--                                            <a class="btn btn-info btn-sm"--}}
{{--                                                title="{{\App\CPU\translate('view')}}"--}}
{{--                                                href="{{route('seller.service.view',[$p['id']])}}">--}}
{{--                                                <i class="tio-visible"></i>--}}
{{--                                            </a>--}}
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Footer -->
                     <div class="card-footer">
                        {{-- {{$services->links()}} --}}
                    </div>
                    @if(count($services)==0)
                        <div class="text-center p-4">
                            <img class="mb-3" src="{{asset('public/assets/back-end')}}/svg/illustrations/sorry.svg" alt="Image Description" style="width: 7rem;">
                            <p class="mb-0">{{\App\CPU\translate('No data to show')}}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <!-- Page level plugins -->
    <script src="{{asset('public/assets/back-end')}}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{asset('public/assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        // Call the dataTables jQuery plugin
        $(document).ready(function () {
            $('#dataTable').DataTable();
        });

        $(document).on('change', '.status', function () {
            var id = $(this).attr("id");
            if ($(this).prop("checked") == true) {
                var status = 1;
            } else if ($(this).prop("checked") == false) {
                var status = 0;
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('seller.service.status-update')}}",
                method: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function (data) {
                    if(data.success == true) {
                        toastr.success('{{\App\CPU\translate('Status updated successfully')}}');
                    }
                    else if(data.success == false) {
                        toastr.error('{{\App\CPU\translate('Status updated failed. Service must be approved')}}');
                    }
                }
            });
        });
    </script>
@endpush
