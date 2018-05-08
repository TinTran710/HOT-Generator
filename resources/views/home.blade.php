@extends('index')

@section('content')

  <h4 class="border-bottom" style="padding-bottom: 5px;">Select type of resource for which to generate Heat template</h4>
  <br>

  <a href="instance"><button type="button" class="btn btn-danger">Instances <span class="badge"></span></button></a>
  <a href="network"><button type="button" class="btn btn-primary">Networks <span class="badge"></span></button></a>
  <button type="button" class="btn btn-success">Routers <span class="badge"></span></button>
  <button type="button" class="btn btn-warning">Floating IPs <span class="badge"></span></button>

@endsection