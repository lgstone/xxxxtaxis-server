@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">

        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header">
                    <ul class="nav nav-pills">
                      <li class="nav-item">
                        <a class="nav-link active" href="/passenger">Passenger</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="/driver">Driver</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="/trip">Trip</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="/payment">Payment</a>
                      </li>
                    </ul>
                </div>

                <div class="card-body">
                    <table class="table">
                      <thead class="thead-light">
                        <tr>
                          <th scope="col">Name</th>
                          <th scope="col">Email</th>
                          <th scope="col">Mobile</th>
                          <th scope="col">Balance</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($users as $user)
                        <tr>
                          <th scope="row">{{$user->first_name}} {{$user->last_name}}</th>
                          <td>{{$user->email}}</td>
                          <td>{{$user->mobile}}</td>
                          <td>{{$user->balance}}</td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
