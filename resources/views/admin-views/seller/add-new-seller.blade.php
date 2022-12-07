@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('add_new_seller'))

@push('css_or_js')
 <style>
        #map {
            height: 350px;
        }

        @media only screen and (max-width: 768px) {

            /* For mobile phones: */
            #map {
                height: 200px;
            }
        }

    </style>

@endpush

@section('content')
<div class="content container-fluid main-card rtl" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('admin.dashboard.index')}}">{{\App\CPU\translate('Dashboard')}}</a>
            </li>
            <li class="breadcrumb-item" aria-current="page">{{\App\CPU\translate('add_new_seller')}}</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-12">
            <div class="card o-hidden border-0 shadow-lg my-4">
                <div class="card-body ">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="p-5">
                                <div class="text-center mb-2 ">
                                    <h3 class="" > {{\App\CPU\translate('Shop')}} {{\App\CPU\translate('Application')}}</h3>
                                    <hr>
                                </div>
                                <form class="user" action="{{route('shop.apply')}}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="status" value="approved">
                                    <h5 class="black">{{\App\CPU\translate('Seller')}} {{\App\CPU\translate('Info')}} </h5>
                                    <div class="form-group row">
                                        <div class="col-sm-6 mb-3 mb-sm-0">
                                            <input type="text" class="form-control form-control-user" id="exampleFirstName" name="f_name" value="{{old('f_name')}}" placeholder="{{\App\CPU\translate('first_name')}}" required>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control form-control-user" id="exampleLastName" name="l_name" value="{{old('l_name')}}" placeholder="{{\App\CPU\translate('last_name')}}" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6 mb-3 mb-sm-0 mt-4">
                                            <input type="email" class="form-control form-control-user" id="exampleInputEmail" name="email" value="{{old('email')}}" placeholder="{{\App\CPU\translate('email_address')}}" required>
                                        </div>
                                        <div class="col-sm-6"><small class="text-danger">( * {{\App\CPU\translate('country_code_is_must')}} {{\App\CPU\translate('like_for_BD_225')}} )</small>
                                            <input type="number" class="form-control form-control-user" id="exampleInputPhone" name="phone" value="{{old('phone')}}" placeholder="{{\App\CPU\translate('phone_number')}}" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6 mb-3 mb-sm-0">
                                            <input type="password" class="form-control form-control-user" minlength="6" id="exampleInputPassword" name="password" placeholder="{{\App\CPU\translate('password')}}" required>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="password" class="form-control form-control-user" minlength="6" id="exampleRepeatPassword" placeholder="{{\App\CPU\translate('repeat_password')}}" required>
                                            <div class="pass invalid-feedback">{{\App\CPU\translate('Repeat')}}  {{\App\CPU\translate('password')}} {{\App\CPU\translate('not match')}} .</div>
                                        </div>
                                    </div>
                                       <div class="form-group row">
                                   <div class="col-sm-6 mb-3 mb-sm-0 mt-4">
                                        {{-- <label for="name">{{\App\CPU\translate('Category')}}</label>
                                        <select
                                            class="js-example-basic-multiple form-control"
                                            name="category_id" required>
                                            <option value="{{old('category_id')}}" selected disabled>---{{\App\CPU\translate('Select')}}---</option>
                                            @foreach($cat as $c)
                                                <option value="{{$c['id']}}" {{old('name')==$c['id']? 'selected': ''}}>
                                                    {{$c['name']}}
                                                </option>
                                            @endforeach
                                        </select>
                                        </div> --}}
                                        <div class="col-sm-6 mb-3 mb-sm-0 mt-4">
                                        <label for="name">{{\App\CPU\translate('Subscription')}}</label>
                                        <select class="js-example-basic-multiple form-control" name="subscription" required>
                                              <option value="{{old('subscription')}}" selected disabled>---{{\App\CPU\translate('Select')}}---</option>
                                            @foreach($subscriptions as $subscription)
                                                  <option value="{{$subscription['id']}} {{old('name')==$subscription['id']? 'selected': ''}}">
                                                    {{$subscription['name']}}  ||  Price:{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($subscription['value']))}} 
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                   <h5 class="black">{{\App\CPU\translate('Shop')}} {{\App\CPU\translate('Info')}}</h5>
                                    <div class="form-group row">
                                        <div class="col-sm-6 mb-3 mb-sm-0 ">
                                            <input type="text" class="form-control form-control-user" id="shop_name" name="shop_name" placeholder="{{\App\CPU\translate('shop_name')}}" value="{{old('shop_name')}}"required>
                                        </div>
                                        <div class="col-sm-6">
                                            <textarea name="shop_address" class="form-control" id="shop_address"rows="1" placeholder="{{\App\CPU\translate('shop_address')}}">{{old('shop_address')}}</textarea>
                                        </div>
                                    </div>
                                      <h5 class="black">{{\App\CPU\translate('Whats_Up')}} {{\App\CPU\translate('Number')}}</h5>
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0 ">
                                    <input type="text" class="form-control form-control-user" id="whatsup" name="whats_up" placeholder="{{\App\CPU\translate('Whats_Up')}}" value="{{old('Whats_Up')}}"required>
                                </div>
                                {{-- <div class="col-sm-6">
                                    <textarea name="shop_address" class="form-control" id="shop_address"rows="1" placeholder="{{\App\CPU\translate('shop_address')}}">{{old('shop_address')}}</textarea>
                                </div> --}}
                            </div>
                                    <div class="">
                                        <div class="pb-1">
                                            <center>
                                                <img style="width: auto;border: 1px solid; border-radius: 10px; max-height:200px;" id="viewer"
                                                    src="{{asset('public\assets\back-end\img\400x400\img2.jpg')}}" alt="banner image"/>
                                            </center>
                                        </div>
        
                                        <div class="form-group">
                                            <div class="custom-file" style="text-align: left">
                                                <input type="file" name="image" id="customFileUpload" class="custom-file-input"
                                                    accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                <label class="custom-file-label" for="customFileUpload">{{\App\CPU\translate('Upload')}} {{\App\CPU\translate('image')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="">
                                        <div class="pb-1">
                                            <center>
                                                <img style="width: auto;border: 1px solid; border-radius: 10px; max-height:200px;" id="viewerLogo"
                                                    src="{{asset('public\assets\back-end\img\400x400\img2.jpg')}}" alt="banner image"/>
                                            </center>
                                        </div>
        
                                        <div class="form-group">
                                            <div class="custom-file" style="text-align: left">
                                                <input type="file" name="logo" id="LogoUpload" class="custom-file-input"
                                                    accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                <label class="custom-file-label" for="LogoUpload">{{\App\CPU\translate('Upload')}} {{\App\CPU\translate('logo')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="">
                                        <div class="pb-1">
                                            <center>
                                                <img style="width: auto;border: 1px solid; border-radius: 10px; max-height:200px;" id="viewerBanner"
                                                     src="{{asset('public\assets\back-end\img\400x400\img2.jpg')}}" alt="banner image"/>
                                            </center>
                                        </div>
                                         <div class="form-group">
                                            <div class="custom-file" style="text-align: left">
                                                <input type="file" name="banner" id="BannerUpload" class="custom-file-input"
                                                       accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" style="overflow: hidden; padding: 2%">
                                                <label class="custom-file-label" for="BannerUpload">{{\App\CPU\translate('Upload')}} {{\App\CPU\translate('Banner')}}</label>
                                            </div>
                                        </div>
                                              <div class="row">
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label class="input-label" for="choice_zones">{{ __('messages.zone') }}<span
                                        class="input-label-secondary"
                                        title="{{ __('select_zone_for_map') }}"></span></label>
                                <select name="zone_id" id="choice_zones" required class="form-control js-select2-custom"
                                    data-placeholder="{{ __('messages.select') }} {{ __('messages.zone') }}">
                                    <option value="" selected disabled>{{ __('messages.select') }}
                                        {{ __('messages.zone') }}</option>
                                    @foreach (\App\Model\Zone::all() as $zone)
                                        @if (isset(auth('admin')->user()->zone_id))
                                            @if (auth('admin')->user()->zone_id == $zone->id)
                                                <option value="{{ $zone->id }}" selected>{{ $zone->name }}
                                                </option>
                                            @endif
                                        @else
                                            <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label class="input-label" for="latitude">{{ __('messages.latitude') }}<span
                                        class="input-label-secondary"
                                        title="}"></span></label>
                                <input type="text" id="latitude" name="latitude" class="form-control"
                                    placeholder="Ex : -94.22213" value="{{ old('latitude') }}" required readonly>
                            </div>


                        </div>
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label class="input-label" for="longitude">{{ __('messages.longitude') }}<span
                                        class="input-label-secondary"
                                        title="{{ __('messages.restaurant_lat_lng_warning') }}"></span></label>
                                <input type="text" name="longitude" class="form-control" placeholder="Ex : 103.344322"
                                    id="longitude" value="{{ old('longitude') }}" required readonly>
                            </div>
                        </div>
                    </div>
                            <div class="col-md-12 col-12">
                                <input id="pac-input" class="controls rounded" style="height: 3em;width:fit-content;" title="{{__('messages.search_your_location_here')}}" type="text" placeholder="{{__('messages.search_here')}}"/>
                                <div id="map"></div>
                            </div><br>
    
                                        
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-user btn-block" id="apply">{{\App\CPU\translate('Apply')}} {{\App\CPU\translate('Shop')}} </button>
                                </form>
                                <hr>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
@if ($errors->any())
    <script>
        @foreach($errors->all() as $error)
        toastr.error('{{$error}}', Error, {
            CloseButton: true,
            ProgressBar: true
        });
        @endforeach
    </script>
@endif
<script>
    $('#inputCheckd').change(function () {
            // console.log('jell');
            if ($(this).is(':checked')) {
                $('#apply').removeAttr('disabled');
            } else {
                $('#apply').attr('disabled', 'disabled');
            }

        });

    $('#exampleInputPassword ,#exampleRepeatPassword').on('keyup',function () {
        var pass = $("#exampleInputPassword").val();
        var passRepeat = $("#exampleRepeatPassword").val();
        if (pass==passRepeat){
            $('.pass').hide();
        }
        else{
            $('.pass').show();
        }
    });
    $('#apply').on('click',function () {

        var image = $("#image-set").val();
        if (image=="")
        {
            $('.image').show();
            return false;
        }
        var pass = $("#exampleInputPassword").val();
        var passRepeat = $("#exampleRepeatPassword").val();
        if (pass!=passRepeat){
            $('.pass').show();
            return false;
        }


    });
    function Validate(file) {
        var x;
        var le = file.length;
        var poin = file.lastIndexOf(".");
        var accu1 = file.substring(poin, le);
        var accu = accu1.toLowerCase();
        if ((accu != '.png') && (accu != '.jpg') && (accu != '.jpeg')) {
            x = 1;
            return x;
        } else {
            x = 0;
            return x;
        }
    }

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#viewer').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#customFileUpload").change(function () {
        readURL(this);
    });

    function readlogoURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#viewerLogo').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    function readBannerURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#viewerBanner').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#LogoUpload").change(function () {
        readlogoURL(this);
    });
    $("#BannerUpload").change(function () {
        readBannerURL(this);
    });
</script>
</script>
       <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDfnUAEQtTSJ1ca2GZKF0_MPc16K6MixlI&libraries=drawing,places&v=3.45.8"></script>
    <script>
        @php($default_location = "maps/api/js?key=AIzaSyDfnUAEQtTSJ1ca2GZKF0_MPc16K6MixlI")
        @php($default_location = $default_location? json_decode($default_location, true) : 0)
         let myLatlng = {
            lat: {{ $default_location ? $default_location['lat'] : '23.757989' }},
            lng: {{ $default_location ? $default_location['lng'] : '90.360587' }}
        };
        let map = new google.maps.Map(document.getElementById("map"), {
            zoom: 13,
            center: myLatlng,
        });
        var zonePolygon = null;
        let infoWindow = new google.maps.InfoWindow({
            content: "Click the map to get Lat/Lng!",
            position: myLatlng,
        });
        var bounds = new google.maps.LatLngBounds();

        function initMap() {
            // Create the initial InfoWindow.
            infoWindow.open(map);
            //get current location block
            infoWindow = new google.maps.InfoWindow();
            // Try HTML5 geolocation.
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        myLatlng = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };
                        infoWindow.setPosition(myLatlng);
                        infoWindow.setContent("Location found.");
                        infoWindow.open(map);
                        map.setCenter(myLatlng);
                    },
                    () => {
                        handleLocationError(true, infoWindow, map.getCenter());
                    }
                );
            } else {
                // Browser doesn't support Geolocation
                handleLocationError(false, infoWindow, map.getCenter());
            }
            //-----end block------
            // Create the search box and link it to the UI element.
            const input = document.getElementById("pac-input");
            const searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
            let markers = [];
            searchBox.addListener("places_changed", () => {
                const places = searchBox.getPlaces();

                if (places.length == 0) {
                return;
                }
                // Clear out the old markers.
                markers.forEach((marker) => {
                marker.setMap(null);
                });
                markers = [];
                // For each place, get the icon, name and location.
                const bounds = new google.maps.LatLngBounds();
                places.forEach((place) => {
                if (!place.geometry || !place.geometry.location) {
                    console.log("Returned place contains no geometry");
                    return;
                }
                const icon = {
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25),
                };
                // Create a marker for each place.
                markers.push(
                    new google.maps.Marker({
                    map,
                    icon,
                    title: place.name,
                    position: place.geometry.location,
                    })
                );

                if (place.geometry.viewport) {
                    // Only geocodes have viewport.
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
                });
                map.fitBounds(bounds);
            });
        }
        initMap();

        function handleLocationError(browserHasGeolocation, infoWindow, pos) {
            infoWindow.setPosition(pos);
            infoWindow.setContent(
                browserHasGeolocation ?
                "Error: The Geolocation service failed." :
                "Error: Your browser doesn't support geolocation."
            );
            infoWindow.open(map);
        }
        $('#choice_zones').on('change', function() {
            var id = $(this).val();
            $.get({
                url: '{{ url('/') }}/shop/zone/get-coordinates/' + id,
                dataType: 'json',
                success: function(data) {
                    if (zonePolygon) {
                        zonePolygon.setMap(null);
                    }
                    zonePolygon = new google.maps.Polygon({
                        paths: data.coordinates,
                        strokeColor: "#FF0000",
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: 'white',
                        fillOpacity: 0,
                    });
                    zonePolygon.setMap(map);
                    zonePolygon.getPaths().forEach(function(path) {
                        path.forEach(function(latlng) {
                            bounds.extend(latlng);
                            map.fitBounds(bounds);
                        });
                    });
                    map.setCenter(data.center);
                    google.maps.event.addListener(zonePolygon, 'click', function(mapsMouseEvent) {
                        infoWindow.close();
                        // Create a new InfoWindow.
                        infoWindow = new google.maps.InfoWindow({
                            position: mapsMouseEvent.latLng,
                            content: JSON.stringify(mapsMouseEvent.latLng.toJSON(),
                                null, 2),
                        });
                        var coordinates = JSON.stringify(mapsMouseEvent.latLng.toJSON(), null,
                            2);
                        var coordinates = JSON.parse(coordinates);

                        document.getElementById('latitude').value = coordinates['lat'];
                        document.getElementById('longitude').value = coordinates['lng'];
                        infoWindow.open(map);
                    });
                },
            });
        });
    </script>
@endpush
