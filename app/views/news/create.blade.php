@extends('layouts.scaffold')

@section('main')

<h1>Create News</h1>

{{ Form::open(array('route' => 'admin.news.store')) }}
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
            (Optional. Populated according to date.)
        </li>

        <li>
            {{ Form::label('price_24h', 'Price (after 24 hours):') }}
            {{ Form::text('price_24h') }}
            (Optional. Populated according to date.)
        </li>

        <li>
            {{ Form::label('price_3d', 'Price (after 3 days):') }}
            {{ Form::text('price_3d') }}
            (Optional. Populated according to date.)
        </li>

        <li>
            {{ Form::label('price_week', 'Price (after a week):') }}
            {{ Form::text('price_week') }}
            (Optional. Populated according to date.)
        </li>

		<li>
			{{ Form::submit('Submit', array('class' => 'btn btn-info')) }}
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


