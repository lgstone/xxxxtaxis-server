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
          <th scope="col">Status</th>
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
            @if($reg->status_ == config('constant.registerStatus.approved'))
              <button type="button" class="btn btn-outline-success btn-sm btnHistoryStatus" disabled="disabled">Passed</button>
            @elseif($reg->status_ == config('constant.registerStatus.declined'))
              <button type="button" class="btn btn-outline-danger btn-sm btnHistoryStatus" disabled="disabled">Rejected</button>
            @elseif($reg->status_ == config('constant.registerStatus.draft'))
              <button type="button" class="btn btn-outline-primary btn-sm btnHistoryStatus" disabled="disabled">Draft</button>
            @endif
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