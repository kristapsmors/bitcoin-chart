@extends('visitor.layout')

{{-- Web site Title --}}
@section('title')
Bitcoin Charts
@stop

{{-- Content --}}
@section('content')
<header class="wrap-title">
    <div class="container">
        <h1 class="page-title">Bitcoin Charts</h1>

        <ol class="breadcrumb hidden-xs">
            <li><a href="#">Bitcoin Charts</a></li>
            <li class="active">Bitcoin prices</li>
        </ol>
    </div>
</header>

<div class="container">
    <div class="row">
        <div class="col-xs-12">

            <h2>Bitcoin prices</h2>
            <ul class="nav nav-tabs" id="currency-tabs">
                @foreach ($currencies as $key => $currency)
                <li{{ !$key ? ' class="active"' : '' }}><a href="#" data-currency="{{$currency['short']}}">{{$currency['short']}}</a></li>
                @endforeach
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane active">
                    <div class="row">
                        <div class="col-sm-12">
                            <div id="container_markets" style="height: 600px;"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <h3>Bitcoin markets <span class="currency" data-current-currency="long">USD dollar</span>
                            </h3>

                            <ul class="nav nav-tabs" id="summary-tabs"></ul>

                            <div class="tab-pane hide" id="summary-tab-template">
                                <table class="table table-striped" id="summary-table">
                                    <thead>
                                    <tr>
                                        <th>Market</th>
                                        <th>Latest price</th>
                                        <th>Avg</th>
                                        <th>Low</th>
                                        <th>High</th>
                                        <th>Change</th>
                                        <th>Volume</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <!--							<tr>-->
                                    <!--								<td>LocalBitcoins</td>-->
                                    <!--								<td>$980.23</td>-->
                                    <!--								<td>$670.21</td>-->
                                    <!--								<td>$1020.21</td>-->
                                    <!--								<td>$634.23</td>-->
                                    <!--								<td><span class="label label-success">3.2%</span></td>-->
                                    <!--								<td>$725,234</td>-->
                                    <!--							</tr>-->
                                    </tbody>
                                </table>

                            </div>

                            <!-- Tab panes -->
                            <div class="tab-content" id="summary-content"></div>

                        </div>
                    </div>

                </div>
                <div class="tab-pane" id="eur">eur charts and data</div>
                <div class="tab-pane" id="cny">cny charts and data</div>
                <div class="tab-pane" id="jpy">jpy charts and data</div>
            </div>

            <h2>Bitcoin news</h2>

            <div class="row">
                <div class="col-sm-12">

                    <ul class="nav nav-tabs" id="news-tabs">
                        <li class="active"><a href="#" data-type="0">All news</a></li>
                        <li><a href="#" data-type="1">Positive</a></li>
                        <li><a href="#" data-type="-1">Negative</a></li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="news-all">

                            <table class="table table-striped" id="news-table">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>Price (announced)</th>
                                    <th>Price (after 24h)</th>
                                    <th>Price (after 3 days)</th>
                                    <th>Price (after a week)</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($news as $item)
                                <tr class="hide" data-timestamp="{{{ $item['timestamp'] }}}" data-change="{{{ $item['change_24h'] }}}" data-id="{{{ $item['id'] }}}">
                                    <td>{{{ $item['id'] }}}</td>
                                    <td><a href="{{ $item['link'] }}" rel="external" target="_blank">{{{ $item['title'] }}}</a></td>
                                    <td>{{{ $item['datetime']->format('F j, Y') }}}</td>
                                    <td>{{{ currency($item['price'], $currency_symbol) }}}</td>
                                    <td>
                                        {{{ currency($item['price_24h'], $currency_symbol) }}}
                                        <span class="label {{ $item['change_24h'] >= 0 ? 'label-success' : 'label-danger' }}">
                                            {{ $item['change_24h'] >= 0 ? '+' : '' }}{{ round($item['change_24h'], 2) }}%
                                        </span>
                                    </td>
                                    <td>
                                        {{{ currency($item['price_3d'], $currency_symbol) }}}
                                        <span class="label {{ $item['change_3d'] >= 0 ? 'label-success' : 'label-danger' }}">
                                            {{ $item['change_3d'] >= 0 ? '+' : '' }}{{ round($item['change_3d'], 2) }}%
                                        </span>
                                    </td>
                                    <td>
                                        {{{ currency($item['price_week'], $currency_symbol) }}}
                                        <span class="label {{ $item['change_week'] >= 0 ? 'label-success' : 'label-danger' }}">
                                            {{ $item['change_week'] >= 0 ? '+' : '' }}{{ round($item['change_week'], 2) }}%
                                        </span>
                                    </td>
                                </tr>
                                @endforeach

                                </tbody>
                            </table>

                            <div id="container_chart" style="height: 400px"></div>

                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div> <!-- container  -->

<aside id="footer-widgets">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h3 class="footer-widget-title">About us</h3>

                <p>Bitcoin charts is a website dedicated to showing the most useful charts and stats related to Bitcoin.

                <p>We will later add other crypto currencies like Litecoin as well.</p>

                <p>If you have any suggestions, please email us at info@bitcoin-charts.info</p>
            </div>
            <div class="col-md-4">
                <div class="footer-widget">
                    <h3 class="footer-widget-title">Top currencies</h3>

                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-6 col-xs-12" style="height: 250px;" id="top-currencies-chart"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="footer-widget">
                    <h3 class="footer-widget-title">Top markets</h3>

                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-6 col-xs-12" style="height: 250px;" id="top-markets-chart"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- row -->
    </div>
    <!-- container -->
</aside> <!-- footer-widgets -->

<footer id="footer">
    <p>&copy; 2014 <a href="http://www.bitcoin-charts.info">Bitcoin-charts.info</a>, inc. All rights reserved.</p>
</footer>
@stop

{{-- Sripts --}}
@section('scripts')
<script type="text/javascript">
    (function () {
        var ENV = window.ENV = window.ENV || {};
        ENV.prices_url = "{{{ URL::route('prices') }}}";
        ENV.currencies = {{ json_encode($currencies) }};
        ENV.news = {{ json_encode($news) }};
        ENV.usd_chart = {{ json_encode($usd_avg) }};
        ENV.data_currencies = {{ json_encode($data_currencies) }};
        ENV.data_markets = {{ json_encode($data_markets) }};
    })();
</script>
@parent
@stop

@section('scripts_libs')
@parent
<script src="{{ asset('charts/js/highstock.js') }}"></script>
<script src="{{ asset('charts/js/modules/exporting.js') }}"></script>
<script src="https://www.google.com/jsapi"></script>
@stop