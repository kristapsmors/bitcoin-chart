@extends('admin.layout')

{{-- Web site Title --}}
@section('title')
	TEST
@stop

{{-- Content --}}
@section('content')
	<header class="wrap-title">
		<div class="container">
		<h1 class="page-title">News</h1>
		<ol class="breadcrumb hidden-xs">
			<li>
				<a href="#"><button class="btn btn-default"><i class="fa fa-plus"></i>  Create new</button></a>
			</li>
		</ol>
		</div>
	</header>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <table class="table table-striped">
				    <thead>
				        <tr>
				            <th>ID</th>
				            <th>Title</th>
				            <th>Something</th>
				            <th></th>
				        </tr>
				    </thead>
				    <tbody>
				        <tr>
				        	<td>1</td>
				        	<td>Title</td>
				        	<td>stuff</td>
				        	<td><a href="#"><button class="btn btn-sm btn-default">Edit</button></a> <a href="#"><button class="btn btn-sm btn-primary">Delete</button></a></td>
				        </tr>
				    </tbody>
				</table>
            </div>
        </div>
    </div>
@stop

{{-- Sripts --}}
@section('scripts')

@stop