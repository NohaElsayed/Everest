@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Category'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{\App\CPU\translate('Dashboard')}}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">{{\App\CPU\translate('category')}}</li>
            </ol>
        </nav>

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ \App\CPU\translate('category_form')}}
                    </div>
                    <div class="card-body" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                        <form action="{{route('admin.subscription.update',[$category['id']])}}" method="POST">
                            @csrf
                              <div class="form-group row">
                                     <div class="col-sm-6 mb-3 mb-sm-0 mt-4">
                                        <label for="name">{{\App\CPU\translate('Subscription')}}</label>
                                        <input type="text" class="js-example-basic-multiple form-control" name="name" value="{{$category->name}}">
                                    </div>
                                     <div class="col-sm-6 mb-3 mb-sm-0 mt-4">
                                        <label for="name">{{\App\CPU\translate('Subscription_Value')}}</label>
                                        <input type="number" class="js-example-basic-multiple form-control" name="value" value="{{$category->value}}">
                                    </div>
                                </div>
                                  <div class="form-group row">
                                     <div class="col-sm-6 mb-3 mb-sm-0 mt-4">
                                        <label for="name">{{\App\CPU\translate('Subscription Duration')}}</label>
                                        <input type="text" class="js-example-basic-multiple form-control" name="time" value="{{$category->time}}">
                                    </div>
                                     {{-- <div class="col-sm-6 mb-3 mb-sm-0 mt-4">
                                        <label for="name">{{\App\CPU\translate('Subscription_Value')}}</label>
                                        <input type="number" class="js-example-basic-multiple form-control" name="value" value="{{$category->value}}">
                                    </div> --}}
                                </div>
                            <button type="submit"class="float-right">{{\App\CPU\translate('update')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')

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
