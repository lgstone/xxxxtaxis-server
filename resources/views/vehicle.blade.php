@extends('layouts.app')

@section('content')

<div class="card-body">
    <table class="table">
      <thead class="thead-light">
        <tr>
          <th scope="col">Driver</th>
          <th scope="col">Model</th>
          <th scope="col">Plate</th>
          <th scope="col">Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($vehicles as $vehicle)
        <tr>
          <td>{{ucfirst($vehicle->driver->user->first_name)}} {{ucfirst($vehicle->driver->user->last_name)}}</td>
          <td>{{$vehicle->brand_}} {{$vehicle->model_}} {{$vehicle->year_}}</td>
          <td>{{$vehicle->plate_number_}} </td>
          <td>
            @if($vehicle->status_ == config('constant.vehicleStatus.Ready'))
              <font color="green">Ready</font>
            @elseif($vehicle->status_ == config('constant.vehicleStatus.Broken'))
              <font color="red">Broken</font>
            @elseif($vehicle->status_ == config('constant.vehicleStatus.Fixing'))
              <font color="orange">Fixing</font>
            @else
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
</div>
@endsection

