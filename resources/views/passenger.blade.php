@extends('layouts.app')

@section('content')

<div class="card-body">
    <table class="table">
      <thead class="thead-light">
        <tr>
          <th scope="col">Name</th>
          <th scope="col">Email</th>
          <th scope="col">Mobile</th>
          <th scope="col">Balance (NZD)</th>
          <th scope="col">Rating</th>
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
          <td>
              <div class="star-rating">
                  <div class="star-rating-top" style="width:{{(1 - $user->passenger->overall_rate_/5)*100}}%">
                      <span></span>
                      <span></span>
                      <span></span>
                      <span></span>
                      <span></span>
                  </div>
                  <div class="star-rating-bottom">
                      <span></span>
                      <span></span>
                      <span></span>
                      <span></span>
                      <span></span>
                  </div>
              </div>
          </td>
          <td><a href="/channel/trip/{{$user->id}}" target="_blank">Details</a></td>
        </tr>
        @endforeach
      </tbody>
    </table>
</div>


@endsection
