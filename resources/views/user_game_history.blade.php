@extends('layouts.app')

@section('content')

<!-- <div class="w-75 bg-light h-100"> -->
    <div>
        <h1 class="text-center">{{ $user->name }}'s Game History</h1>

        @foreach($games as $game)

            @php
                $players = $game->players->pluck('name')->toArray();
                $goalies = $game->goalies->pluck('name')->toArray();
            @endphp

            <div class="row align-items-center justify-content-between m-0">
                <div class="col-lg-3">
                    <h5><b>Game:</b> {{ $game->title }}</h5>
                </div>
                <div class="col-lg-4">
                    @if($game->time->isFuture())
                        @if(in_array($user->name, $players) || in_array($user->name, $goalies))
                            <h5><b>Attendance:</b> Attending</h5>
                        @else
                            <h5><b>Attendance:</b> Not Yet Attending</h5>
                        @endif
                    @else
                        @if(in_array($user->name, $players) || in_array($user->name, $goalies))
                            <h5><b>Attendance:</b> Attended</h5>
                        @else
                            <h5><b>Attendance:</b> Did Not Attend</h5>
                        @endif
                    @endif
                </div>
                {{-- <div>
                    Paid, Paid amount, and method

                    @if($game->time->isFuture())
                        @if($game->gamePlayers()->wherePivot('user_id', $user->id)->exists())
                            <td class="align-middle">Paid</td>
                        @else
                            <td class="align-middle">Please Pay</td>
                        @endif
                    @else
                        @if($game->gamePlayers()->wherePivot('user_id', $user->id)->exists())
                            <td class="align-middle">Paid</td>
                        @else
                            <td class="align-middle">No Payment</td>
                        @endif
                    @endif
                    
                    @if($game->gamePayments()->wherePivot('user_id', $user->id)->pluck('payment')->first())
                        <td class="align-middle">{{ $game->gamePayments()->wherePivot('user_id', $user->id)->pluck('payment')->first() }}</td>
                    @else
                        <td class="align-middle">-</td>
                    @endif

                    @if($game->gamePayments()->wherePivot('user_id', $user->id)->pluck('method')->first())
                        <td class="align-middle">{{ $game->gamePayments()->wherePivot('user_id', $user->id)->pluck('method')->first() }}</td>
                    @else
                        <td class="align-middle">-</td>
                    @endif
                </div> --}}
                <div class="col-lg-2">
                    @if(in_array($user->name, $players))
                        <h5><b>Position:</b> Player</h5>
                    @elseif(in_array($user->name, $goalies))
                        <h5><b>Position:</b> Goalie</h5>
                    @else
                        <h5><b>Position:</b> Not Attending</h5>
                    @endif

                </div>
                <hr>
                
            </div>

        @endforeach
    
    </div>

<!-- </div> -->
@endsection
