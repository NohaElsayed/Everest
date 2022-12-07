@extends('layouts.front-end.app')

@section('title', \App\CPU\translate('Register'))

@push('css_or_js')
    <style>
        @media (max-width: 500px) {
            #sign_in {
                margin-top: -23% !important;
            }

        }
    </style>
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
    <div class="container py-4 py-lg-5 my-4"
         style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 box-shadow">
                    <div class="card-body">
                        <h2 class="h4 mb-1">{{\App\CPU\translate('no_account')}}</h2>
                        <p class="font-size-sm text-muted mb-4">{{\App\CPU\translate('register_control_your_order')}}
                            .</p>
                        <form class="needs-validation_" action="{{route('customer.auth.sign-up')}}"
                              method="post" id="sign-up-form">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="reg-fn">{{\App\CPU\translate('first_name')}}</label>
                                        <input class="form-control" value="{{old('f_name')}}" type="text" name="f_name"
                                               style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                               required>
                                        <div class="invalid-feedback">{{\App\CPU\translate('Please enter your first name')}}!</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="reg-ln">{{\App\CPU\translate('last_name')}}</label>
                                        <input class="form-control" type="text" value="{{old('l_name')}}" name="l_name"
                                               style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                        <div class="invalid-feedback">{{\App\CPU\translate('Please enter your last name')}}!</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="reg-email">{{\App\CPU\translate('email_address')}}</label>
                                        <input class="form-control" type="email" value="{{old('email')}}"  name="email"
                                               style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};" required>
                                        <div class="invalid-feedback">{{\App\CPU\translate('Please enter valid email address')}}!</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="reg-phone">{{\App\CPU\translate('phone_number')}}
                                            <small class="text-primary">( * {{\App\CPU\translate('country_code_is_must')}} {{\App\CPU\translate('like_255')}} )</small></label>
                                        <input class="form-control" type="number"  value="{{old('phone')}}"  name="phone"
                                               style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                               required>
                                        <div class="invalid-feedback">{{\App\CPU\translate('Please enter your phone number')}}!</div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                            <input type="date" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};" class="form-control form-control-user" id="exampleInputEmail" name="age" value="{{old('age')}}" placeholder="{{\App\CPU\translate('age')}}" >
                                        <div class="invalid-feedback">{{\App\CPU\translate('Please enter your phone number')}}!</div>
                                         </div>
                                        </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                            <select name="type" class="form-control form-control-user"  placeholder="{{\App\CPU\translate('type')}}" required style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};" >
                                            <option value="0">Female</option>
                                            <option value="1">Male</option>
                                            </select>
                                             <div class="invalid-feedback">{{\App\CPU\translate('Please enter your phone number')}}!</div>
                                        </div>
                                    </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="si-password">{{\App\CPU\translate('password')}}</label>
                                        <div class="password-toggle">
                                            <input class="form-control" name="password" type="password" id="si-password"
                                                   style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                                   placeholder="{{\App\CPU\translate('minimum_8_characters_long')}}"
                                                   required>
                                            <label class="password-toggle-btn">
                                                <input class="custom-control-input" type="checkbox"><i
                                                    class="czi-eye password-toggle-indicator"></i><span
                                                    class="sr-only">{{\App\CPU\translate('Show')}} {{\App\CPU\translate('password')}} </span>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- <div class="form-group">
                                        <label for="reg-password">{{\App\CPU\translate('password')}}</label>
                                        <input class="form-control" type="password" name="password">
                                        <div class="invalid-feedback">Please enter password!</div>
                                    </div> --}}
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="si-password">{{\App\CPU\translate('confirm_password')}}</label>
                                        <div class="password-toggle">
                                            <input class="form-control" name="con_password" type="password"
                                                   style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                                   placeholder="{{\App\CPU\translate('minimum_8_characters_long')}}"
                                                   id="si-password"
                                                   required>
                                            <label class="password-toggle-btn">
                                                <input class="custom-control-input" type="checkbox"
                                                       style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"><i
                                                    class="czi-eye password-toggle-indicator"></i><span
                                                    class="sr-only">{{\App\CPU\translate('Show')}} {{\App\CPU\translate('password')}} </span>
                                            </label>
                                        </div>
                                    </div>
                                    {{-- <div class="form-group">
                                        <label for="reg-password-confirm">{{\App\CPU\translate('confirm_password')}}</label>
                                        <input class="form-control" type="password" name="con_password">
                                        <div class="invalid-feedback">Passwords do not match!</div>
                                    </div> --}}
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
                            <div class="form-group d-flex flex-wrap justify-content-between">

                                <div class="form-group mb-1">
                                    <strong>
                                        <input type="checkbox" class="mr-1"
                                               name="remember" id="inputCheckd">
                                    </strong>
                                    <label class="" for="remember">{{\App\CPU\translate('i_agree_to_Your_terms')}}<a
                                            class="font-size-sm" target="_blank" href="{{route('terms')}}">
                                            {{\App\CPU\translate('terms_and_condition')}}
                                        </a></label>
                                </div>

                            </div>
                            <div class="flex-between row" style="direction: {{ Session::get('direction') }}">
                                <div class="mx-1">
                                    <div class="text-right">
                                        <button class="btn btn-primary" id="sign-up" type="submit" disabled>
                                            <i class="czi-user {{Session::get('direction') === "rtl" ? 'ml-2 mr-n1' : 'mr-2 ml-n1'}}"></i>
                                            {{\App\CPU\translate('sing_up')}}
                                        </button>
                                    </div>
                                </div>
                                <div class="mx-1">
                                    <a class="btn btn-outline-primary" href="{{route('customer.auth.login')}}">
                                        <i class="fa fa-sign-in"></i> {{\App\CPU\translate('sing_in')}}
                                    </a>
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="row">
                                        @foreach (\App\CPU\Helpers::get_business_settings('social_login') as $socialLoginService)
                                            @if (isset($socialLoginService) && $socialLoginService['status']==true)
                                                <div class="col-sm-6 text-center mt-1">
                                                    <a class="btn btn-outline-primary"
                                                       href="{{route('customer.auth.service-login', $socialLoginService['login_medium'])}}"
                                                       style="width: 100%">
                                                        <i class="czi-{{ $socialLoginService['login_medium'] }} {{Session::get('direction') === "rtl" ? 'ml-2 mr-n1' : 'mr-2 ml-n1'}}"></i>
                                                        {{\App\CPU\translate('sing_up_with_'.$socialLoginService['login_medium'])}}
                                                    </a>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $('#inputCheckd').change(function () {
            // console.log('jell');
            if ($(this).is(':checked')) {
                $('#sign-up').removeAttr('disabled');
            } else {
                $('#sign-up').attr('disabled', 'disabled');
            }

        });
        /*$('#sign-up-form').submit(function (e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('customer.auth.sign-up')}}',
                dataType: 'json',
                data: $('#sign-up-form').serialize(),
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        toastr.success(data.message, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setInterval(function () {
                            location.href = data.url;
                        }, 2000);
                    }
                },
                complete: function () {
                    $('#loading').hide();
                },
                error: function () {
                  console.log(response)
                }
            });
        });*/
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
