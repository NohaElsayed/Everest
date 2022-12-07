@extends('layouts.back-end.app')
@section('title', \App\CPU\translate('Subscription'))
@push('css_or_js')
@endpush
@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{\App\CPU\translate('Dashboard')}}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">{{\App\CPU\translate('Subscription')}}</li>
            </ol>
        </nav>
        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ \App\CPU\translate('Subscription_form')}}
                    </div>
                    <div class="card-body" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                        <form action="{{route('admin.subscription.store')}}" method="POST">
                            @csrf
                             <div class="form-group row">
                                     <div class="col-sm-6 mb-3 mb-sm-0 mt-4">
                                        <label for="name">{{\App\CPU\translate('Subscription')}}</label>
                                        <input type="text" class="js-example-basic-multiple form-control" name="name" required>
                                    </div>
                                     <div class="col-sm-6 mb-3 mb-sm-0 mt-4">
                                        <label for="name">{{\App\CPU\translate('Subscription_Value')}}</label>
                                        <input type="number" class="js-example-basic-multiple form-control" name="value" required>
                                    </div>
                                </div>
                                 <div class="form-group row">
                                     <div class="col-sm-6 mb-3 mb-sm-0 mt-4">
                                        <label for="name">{{\App\CPU\translate('Subscription Time')}}</label>
                                        <input type="text" class="js-example-basic-multiple form-control" name="time" required>
                                    </div>
                                     {{-- <div class="col-sm-6 mb-3 mb-sm-0 mt-4">
                                        <label for="name">{{\App\CPU\translate('Subscription_Value')}}</label>
                                        <input type="number" class="js-example-basic-multiple form-control" name="value" required>
                                    </div> --}}
                                </div>
                            <button type="submit" class="btn btn-primary float-right">{{\App\CPU\translate('submit')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 20px" id="cate-table">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row flex-between justify-content-between align-items-center flex-grow-1">
                            {{-- <div class="col-12 col-sm-6 col-md-6">
                                <h5>{{ \App\CPU\translate('category_table')}} <span style="color: red;">({{ $categories->total() }})</span></h5>
                            </div> --}}
                            <div class="col-12 col-sm-6 col-md-4" style="width: 30vw">
                                <!-- Search -->
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group input-group-merge input-group-flush">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="" type="search" name="search" class="form-control"
                                            placeholder="{{ \App\CPU\translate('search_here')}}" value="{{ $search }}" required>
                                        <button type="submit" class="btn btn-primary">{{\App\CPU\translate('search')}}</button>
                                    </div>
                                </form>
                                <!-- End Search -->
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="padding: 0">
                        <div class="table-responsive">
                            <table style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                <thead class="thead-light">
                                <tr>
                                    {{-- <th style="width: 100px">{{ \App\CPU\translate('category')}} {{ \App\CPU\translate('ID')}}</th> --}}
                                    <th>{{ \App\CPU\translate('name')}}</th>
                                    <th>{{ \App\CPU\translate('value')}}</th>
                                     <th>{{ \App\CPU\translate('Duration')}}</th>
                                    <th class="text-center" style="width:15%;">{{ \App\CPU\translate('action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($categories as $key=>$category)
                                    <tr>
                                        {{-- <td class="text-center">{{$category['id']}}</td> --}}
                                        <td>{{$category['name']}}</td>
                                      
                                        <td>{{$category['value']}} XOF
                                         <td>{{$category['time']}}
                                            {{-- @if($category->home_status == true)
                                                <div style="padding: 10px;border: 1px solid;cursor: pointer"
                                                     onclick="location.href='{{route('admin.category.status',[$category['id'],0])}}'">
                                                    <span class="legend-indicator bg-success" style="{{Session::get('direction') === "rtl" ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'}}"></span>{{\App\CPU\translate('active')}}
                                                </div>
                                            @elseif($category->home_status == false)
                                                <div style="padding: 10px;border: 1px solid;cursor: pointer"
                                                     onclick="location.href='{{route('admin.category.status',[$category['id'],1])}}'">
                                                    <span class="legend-indicator bg-danger" style="{{Session::get('direction') === "rtl" ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'}}"></span>{{\App\CPU\translate('disabled')}}
                                                </div>
                                            @endif --}}
                                            {{-- <label class="switch switch-status">
                                                <input type="checkbox" class="category-status"
                                                       id="{{$category['id']}}" {{$category->home_status == 1?'checked':''}}>
                                                <span class="slider round"></span>
                                            </label> --}}
                                        </td>
                                        <td>
                                            <a style="background-color:#ffc0cb!important;color:black" class="btn btn-primary btn-sm edit" style="cursor: pointer;"
                                                title="{{ \App\CPU\translate('Edit')}}"
                                               href="{{route('admin.subscription.edit',[$category['id']])}}">
                                                <i class="tio-edit"></i>
                                            </a>
                                              <a class="btn btn-danger btn-sm" style="cursor: pointer;"
                                                title="{{ \App\CPU\translate('Delete')}}"
                                               href="{{route('admin.subscription.delete',[$category['id']])}}">
                                                <i class="tio-add-to-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer">
                        {{-- {{$categories->links()}} --}}
                    </div>
                    @if(count($categories)==0)
                        <div class="text-center p-4">
                            <img class="mb-3" src="{{asset('public/assets/back-end')}}/svg/illustrations/sorry.svg" alt="Image Description" style="width: 7rem;">
                            <p class="mb-0">{{\App\CPU\translate('no_data_found')}}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    $(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>
    {{-- <script>
        $(".lang_link").click(function (e) {
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.split("-")[0];
            console.log(lang);
            $("#" + lang + "-form").removeClass('d-none');
            if (lang == '{{$default_lang}}') {
                $(".from_part_2").removeClass('d-none');
            } else {
                $(".from_part_2").addClass('d-none');
            }
        });

        $(document).ready(function () {
            $('#dataTable').DataTable();
        });
    </script> --}}
    <script>
        $(document).on('change', '.category-status', function () {
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
                url: "{{route('admin.category.status')}}",
                method: 'POST',
                data: {
                    id: id,
                    home_status: status
                },
                success: function (data) {
                    if(data.success == true) {
                        toastr.success('{{\App\CPU\translate('Status updated successfully')}}');
                    }
                }
            });
        });
    </script>
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this);
        });
    </script>
@endpush
