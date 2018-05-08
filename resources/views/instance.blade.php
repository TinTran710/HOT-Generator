@extends('index')

@section('content')

  	<h4 class="border-bottom" style="padding-bottom: 5px;">Options for VMs
  	</h4><br>

    @if(session('message'))
        <div class="alert alert-success">
            {{session('message')}}
        </div>
    @endif

	<form action="instance" method="POST">
		<input type="hidden" name="_token" value="{{csrf_token()}}">

		<div class="form-group">
			<label for="number">Number:</label>
			<input type="number" class="form-control" id="number" name="number" placeholder="Enter number of VMs" min="1" max="100">
		</div>

		<div class="form-group">
	  		<label for="flavor">Select flavor:</label>
		  	<select class="form-control" id="flavor" name="flavor">
		    	@foreach($flavorList as $flavor)
		    		<option>{{$flavor}}</option>
		    	@endforeach
		  </select>
		</div>		

		<div class="form-group">
	  		<label for="image">Select image:</label>
		  	<select class="form-control" id="image" name="image">
		    	@foreach($imageList as $img)
		    		<option>{{$img}}</option>
		    	@endforeach
		  </select>
		</div>

		<div class="form-group">
	  		<label for="keyName">Select key:</label>
		  	<select class="form-control" id="keyName" name="keyName">
		    	@foreach($keyNameList as $key)
		    		<option>{{$key}}</option>
		    	@endforeach
		  </select>
		</div>		

		<div class="form-group">
	  		<label for="publicNet">Select public network:</label>
		  	<select class="form-control" id="publicNet" name="publicNet">
		    	@foreach($publicNetList as $publicnet)
		    		<option>{{$publicnet}}</option>
		    	@endforeach
		  </select>
		</div>	

		<div class="form-group">
	  		<label for="privateSubnet">Select private subnet:</label>
		  	<select class="form-control" id="privateSubnet" name="privateSubnet">
		    	@foreach($privateSubnetList as $privatenet)
		    		<option value="{{$privatenet['name']}}|{{$privatenet['parent']}}">{{$privatenet['name']}}</option>
		    	@endforeach
		  </select>
		</div>							

		<button type="submit" class="btn btn-default">Submit</button>
	</form>
	<br>

@endsection