@extends('layouts.scaffold')

@section('main')

<h1>All News</h1>

<p>{{ link_to_route('admin.news.create', 'Add new news') }}</p>

@if ($news->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Datetime</th>
				<th>Title</th>
				<th>Link</th>
				<th>Price</th>
				<th>Price 24h</th>
				<th>Price 3 days</th>
				<th>Price week</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($news as $news)
				<tr>
					<td>{{{ $news->datetime }}}</td>
					<td>{{{ $news->title }}}</td>
					<td>{{{ $news->link }}}</td>
					<td>{{{ $news->price }}}</td>
					<td>{{{ $news->price_24h }}}</td>
					<td>{{{ $news->price_3d }}}</td>
					<td>{{{ $news->price_week }}}</td>
                    <td>{{ link_to_route('admin.news.edit', 'Edit', array($news->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('admin.news.destroy', $news->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no news
@endif

@stop
