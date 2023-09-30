@extends('layouts.app')

@section('content')

<!-- <div class="w-75 bg-light h-100"> -->
    <div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                @foreach($errors->all() as $error)
                    <li>
                        {{$error}}
                    </li>
                @endforeach
                </ul>
            </div>
        @endif

        @if(session()->get('message'))
            <div class="alert alert-success" role="alert">
                <strong>Success: </strong>{{session()->get('message')}}
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{Auth::user()->name}}'s Profile</div>
                    
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{$message}}</p>
                            </div>
                        @endif

                        <script>
                            function showDropdownText(item) {
                                    document.getElementById("gameRoleDropdown").innerHTML = 'Game Role: ' + item.innerHTML;
                                }
                        </script>

                        <form action="{{route('profile')}}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="name"><strong>Name:</strong></label>
                                <input type="text" class="form-control" id ="name" name="name" value="{{Auth::user()->name}}">
                            </div>

                            <div class="form-group mt-2">
                                <label for="email"><strong>Email:</strong></label>
                                <input type="text" class="form-control" id ="email" value="{{Auth::user()->email}}" name="email">
                            </div>

                            <div class="dropdown mt-2">
                            <label for="email"><strong>Game Role:</strong></label>
                                <select class="form-select" aria-label="Default select example" name="role" id="role">
                                    <option value="" selected disabled hidden>{{Str::title(Auth::user()->role_preference)}}</option>
                                    @foreach ($GAME_ROLES as $gamerole)
                                        <option value="{{ $gamerole }}">{{ $gamerole->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button class="btn btn-primary mt-2" type="submit">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    
    </div>

<!-- </div> -->
@endsection
