@extends('layouts.scaffold')

@section('main')

<h1>Edit News</h1>

{{ Form::model($news, array('method' => 'PATCH', 'route' => array('admin.news.update', $news->id))) }}
	<ul>
        <li>
            {{ Form::label('datetime', 'Datetime:') }}
            {{ Form::text('datetime', null, ['type' => 'datetime', 'placeholder' => 'Date time (YYYY-MM-DD HH:MM:SS)']) }}
        </li>

        <li>
            {{ Form::label('currency', 'Currency:') }}
            {{ Form::select('currency', $currencies) }}
        </li>

        <li>
            {{ Form::label('title', 'Title:') }}
            {{ Form::text('title') }}
        </li>

        <li>
            {{ Form::label('link', 'Link:') }}
            {{ Form::text('link') }}
        </li>

        <li>
            {{ Form::label('price', 'Price:') }}
            {{ Form::text('price') }}
        </li>

        <li>
            {{ Form::label('price_24h', 'Price (24h):') }}
            {{ Form::text('price_24h') }}
        </li>

        <li>
            {{ Form::label('price_3d', 'Price (3 days):') }}
            {{ Form::text('price_3d') }}
        </li>

        <li>
            {{ Form::label('price_week', 'Price (week):') }}
            {{ Form::text('price_week') }}
        </li>

		<li>
			{{ Form::submit('Update', array('class' => 'btn btn-info')) }}
			{{ link_to_route('admin.news.show', 'Cancel', $news->id, array('class' => 'btn')) }}
		</li>
	</ul>
{{ Form::close() }}

@if ($errors->any())
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
@endif

<link type="text/css" rel="stylesheet" href="{{ URL::asset('js/datetimepicker/jquery.datetimepicker.css') }}" />
<script src="{{ URL::asset('js/datetimepicker/jquery.js') }}"></script>
<script src="{{ URL::asset('js/datetimepicker/jquery.datetimepicker.js') }}"></script>
<script>
    $(function() {
        $('#datetime').datetimepicker({format: 'Y-m-d H:i:00', step: 15});
    });
</script>
@stop
