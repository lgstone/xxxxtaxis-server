@extends('layouts.app')

@section('content')

<div class="card-body">
    @if(isset($user))
      @if($type == 'driver')
        <div class="alert alert-secondary">Trip details of Driver <font color="green">{{ucfirst($user->first_name)}} {{ucfirst($user->last_name)}}</font></div>
      @elseif($type == 'passenger')
        <div class="alert alert-secondary">Trip details of Passenger <font color="green"> {{ucfirst($user->first_name)}} {{ucfirst($user->last_name)}}</font></div>
      @endif
    @endif
    <table class="table">
      <thead class="thead-light">
        <tr>
          <th scope="col">Passenger</th>
          <th scope="col">Driver</th>
          <th scope="col">Distance(KM)</th>
          <th scope="col">Total Price(NZD)</th>
          <th scope="col">Details</th>
        </tr>
      </thead>
      <tbody>
        @forelse($trips as $trip)
        <tr>
          <input type="hidden" id="trip_id" value="{{$trip->id_}}">
          
          <td>{{ucfirst($trip->passenger->user->first_name)}}&nbsp{{ucfirst($trip->passenger->user->last_name)}}</td>
          <td>{{ucfirst($trip->driver->user->first_name)}}&nbsp{{ucfirst($trip->driver->user->last_name)}}</td>
          <td>
            @if($trip->status_ != 3)
              <button type="button" class="btn btn-outline-info btn-sm">Trip In Progress</button>
            @else
              {{$trip->distance_}}
            @endif
          </td>
          <td>
            @if($trip->status_ != 3)
              <button type="button" class="btn btn-outline-info btn-sm">Trip In Progress</button>
            @else
              {{$trip->total_price_}}
            @endif
          </td>
          <td>
            <a href="/ajax/getTripDetail/{{$trip->id_}}" class="btn btn-primary btn-sm tripDetailBtn" id="tripDetailBtn" data-toggle="modal" data-target="#tripModal">
              Detail
            </a>
          </td>
        </tr>
        @empty
        @endforelse
      </tbody>
    </table>
</div>

@endsection

@extends('modal.tripDetail')

@section('scripts')
    <script src='/js/user.js'></script>
@endsection
