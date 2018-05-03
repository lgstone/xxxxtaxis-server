@extends('layouts.app')

@section('content')

<div class="card-body">
    <table class="table">
      <thead class="thead-light">
        <tr>
          <th scope="col">Name</th>
          <th scope="col">Email</th>
          <th scope="col">Mobile</th>
          <th scope="col">License</th>
          <th scope="col">Vehicle</th>
          <th scope="col">Operation</th>
        </tr>
      </thead>
      <tbody>
        @foreach($regs as $reg)
        <tr>
          <th scope="row">{{ucfirst($reg->first_name_)}} {{ucfirst($reg->last_name_)}}</th>
          <td>{{$reg->email_}}</td>
          <td>{{$reg->mobile_}}</td>
          <td>
            <a href="/ajax/getApplyDetail/{{$reg->id_}}" class="btn btn-primary btn-sm driverDetailBtn" id="driverDetailBtn" data-toggle="modal" data-target="#licenseModal">
              Detail
            </a>
          </td>
          <td>
            <a href="/ajax/getApplyDetail/{{$reg->id_}}" class="btn btn-primary btn-sm vehicleDetailRegBtn" id="vehicleDetailRegBtn" data-toggle="modal" data-target="#vehicleModal">
              Detail
            </a>
          </td>
          <td>
            <a href="/ajax/driverRegisterOperate?id={{$reg->id_}}" class="btn btn-success btn-sm passRegBtn" id="passRegBtn" data-toggle="modal" data-target="#operateModal">
              Approve
            </a>
            <a href="/ajax/driverRegisterOperate?id={{$reg->id_}}" class="btn btn-danger btn-sm rejectRegBtn" id="rejectRegBtn" data-toggle="modal" data-target="#operateModal">
              Decline
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
@extends('modal.operate')


@section('scripts')
    <script src='/js/user.js'></script>
@endsection