@extends('layouts.app')

@section('content')

<!-- <div class="w-75 bg-light h-100"> -->
    <div>
        <h1 class="text-center">{{ $user->name }}'s Game History</h1>

        <table class="table table-hover">
            <thead>
                <th>Game</th>
                <th>Attendance</th>
                <th>Paid</th>
                <th>Paid Ammount</th>
                <th>Method</th>
                <th>Position</th>
            </thead>
            <tbody>
                @foreach($games as $game)
                    <tr>
                        @php
                            $players = $game->players->pluck('name')->toArray();
                            $goalies = $game->goalies->pluck('name')->toArray();
                        @endphp
                        
                        <td class="align-middle"><p class="m-0">{{ $game->title }}</p></td>
                        
                        @if($game->time->isFuture())
                            @if(in_array($user->name, $players) || in_array($user->name, $goalies))
                                <td class="align-middle">Attending</td>
                            @else
                                <td class="align-middle">Not Yet Attending</td>
                            @endif
                        @else
                            @if(in_array($user->name, $players) || in_array($user->name, $goalies))
                                <td class="align-middle">Attended</td>
                            @else
                                <td class="align-middle">Did Not Attend</td>
                            @endif
                        @endif

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

                        @php
                            $players = $game->players->pluck('name')->toArray();
                            $goalies = $game->goalies->pluck('name')->toArray();
                        @endphp

                        @if(in_array($user->name, $players))
                            <td class="align-middle">Player</td>
                        @elseif(in_array($user->name, $goalies))
                            <td class="align-middle">Goalie</td>
                        @else
                            <td class="align-middle">Not Attending</td>
                        @endif
                        
                    </tr>
                @endforeach
            </tbody>
        </table>
    
    </div>

<!-- </div> -->
@endsection
