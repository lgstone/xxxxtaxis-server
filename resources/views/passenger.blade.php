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
          <th scope="col">Trip</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $user)
        <tr>
          <th scope="row">{{ucfirst($user->first_name)}} {{ucfirst($user->last_name)}}</th>
          <td>{{$user->email}}</td>
          <td>{{$user->mobile}}</td>
          <td>{{$user->balance}}</td>
          <td><a href="/channel/trip/{{$user->id}}" target="_blank">Details</a></td>
        </tr>
        @endforeach
      </tbody>
    </table>
</div>

@endsection
