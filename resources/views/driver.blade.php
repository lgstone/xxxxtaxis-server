@extends('layouts.app')

@section('content')

<div class="card-body">
    <table class="table">
      <thead class="thead-light">
        <tr>
          <th scope="col">Name</th>
          <th scope="col">Email</th>
          <th scope="col">Mobile</th>
          <th scope="col">Balance(NZD)</th>
          <th scope="col">License</th>
          <th scope="col">Vehicle</th>
          <th scope="col">Online Status</th>
          <th scope="col">Trip History</th>

        </tr>
      </thead>
      <tbody>
        @foreach($users as $user)
        <tr>
          <th scope="row">{{ucfirst($user->first_name)}} {{ucfirst($user->last_name)}}</th>
          <td>{{$user->email}}</td>
          <td>{{$user->mobile}}</td>
          <td>{{$user->balance}}</td>
          <td>
            <a href="/ajax/getDriverDetail/{{$user->id}}" class="btn btn-primary btn-sm driverDetailBtn" id="driverDetailBtn" data-toggle="modal" data-target="#licenseModal">
              Detail
            </a>
          </td>
          <td>
            <a href="/ajax/getVehicleDetailByUser/{{$user->id}}" class="btn btn-primary btn-sm vehicleDetailBtn" id="vehicleDetailBtn" data-toggle="modal" data-target="#vehicleModal">
              Detail
            </a>
          </td>
          <td>
            @if($user->driver->online_status_ == '1')
              <button type="button" class="btn btn-outline-success btn-sm" disabled="disabled">online</button>
            @elseif($user->driver->online_status_ == '0')
              <button type="button" class="btn btn-outline-danger btn-sm" disabled="disabled">offline</button>
            @elseif($user->driver->online_status_ == '2')
              <button type="button" class="btn btn-outline-info btn-sm" disabled="disabled">on trip</button>
            @endif
          </td>
          <td>
            <a href="/channel/tripByDriver/{{$user->id}}">Detail
            </a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
</div>

@endsection


@extends('modal.license')
@extends('modal.vehicle')


@section('scripts')
    <script src='/js/user.js'></script>
@endsection