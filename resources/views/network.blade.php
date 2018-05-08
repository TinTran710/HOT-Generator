@extends('index')

@section('content')

	<h4 class="border-bottom" style="padding-bottom: 5px;">Options for Networks
	</h4><br>


	@if(session('message'))
	    <div class="alert alert-success">
	        {{session('message')}}
	    </div>
	@endif

	<form action="network" method="POST">
		<input type="hidden" name="_token" value="{{csrf_token()}}">

		<div class="form-group">
			<label for="network">Networks:</label>
			<input type="number" class="form-control" id="network" name="network" placeholder="Enter number of networks" min="1" max="100">
		</div>

		<div class="form-group">
			<label for="subnet">Subnets:</label>
			<input type="number" class="form-control" id="subnet" name="subnet" placeholder="Enter number of sub networks for each network" min="1" max="200">
		</div>

		<button type="submit" class="btn btn-default">Submit</button>
	</form>

@endsection