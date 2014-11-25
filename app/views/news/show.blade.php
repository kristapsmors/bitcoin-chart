@extends('layouts.scaffold')

@section('main')

<h1>Show News</h1>

<p>{{ link_to_route('admin.news.index', 'Return to all news') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Datetime</th>
				<th>Title</th>
				<th>Link</th>
				<th>Price</th>
				<th>Price_24h</th>
				<th>Price_3d</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $news->datetime }}}</td>
					<td>{{{ $news->title }}}</td>
					<td>{{{ $news->link }}}</td>
					<td>{{{ $news->price }}}</td>
					<td>{{{ $news->price_24h }}}</td>
					<td>{{{ $news->price_3d }}}</td>
                    <td>{{ link_to_route('admin.news.edit', 'Edit', array($news->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {{ Form::open(array('method' => 'DELETE', 'route' => array('admin.news.destroy', $news->id))) }}
                            {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                        {{ Form::close() }}
                    </td>
		</tr>
	</tbody>
</table>

@stop
