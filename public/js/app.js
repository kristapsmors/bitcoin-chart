/**
 * Currency object
 *
 * @name CurrencyObject
 * @type {object}
 * @property {string} symbol - Symbol
 * @property {string} short - Short name
 * @property {string} long - Long name
 */
/**
 * Prices response Object
 *
 * @name PricesResponse
 * @type {object}
 * @property {Array} chart_data
 * @property {SummaryObject} summary
 */
/**
 * Summary object
 *
 * @name SummaryObject
 * @type {object}
 * @property {object} periods
 * @property {object} data
 */
/**
 * @name Env
 * @type {object}
 * @property {string} prices_url=
 * @property {CurrencyObject[]} currencies=
 * @property {object} news=
 * @property {Array} usd_chart=
 * @property {Array} data_currencies=
 * @property {Array} data_markets=
 */
/**
 *
 */
(function (/*Window*/window, undefined) {
    "use strict";
    /**
     * @type {Env}
     */
    var ENV = window.ENV || {}, App, rangeButtons;

    /**
     *
     * @param {(number|string)} num
     * @returns {number}
     */
    function float(num) {
        return parseFloat(('' + num).replace(',', '.')) || 0.0;
    }

    /**
     *
     * @param {(number|string)} num
     * @returns {number}
     */
    function int(num) {
        return parseInt(num) || 0;
    }

    /**
     *
     * @param {number} num
     * @param {number} [decimals]
     * @returns {string}
     */
    function toFixed(num, decimals) {
        if (decimals === undefined) {
            decimals = 2;
        }
        return parseFloat(num).toFixed(decimals);
    }

    /**
     *
     * @param {number} amount
     * @param {number} [decimals]
     * @returns {string}
     */
    function currency(amount, decimals) {
        if (decimals === undefined) {
            decimals = 2
        }
        return App.Currency.format(toFixed(amount, decimals), App.currentCurrency);
    }

    // Common values
    rangeButtons = [
        {type: 'day', count: 1, text: '1d'},
        {type: 'day', count: 7, text: '1w'},
        {type: 'month', count: 1, text: '1m'},
        {type: 'month', count: 3, text: '3m'},
        {type: 'ytd', text: 'YTD'},
        {type: 'year', count: 1, text: '1y'},
        {type: 'year', count: 5, text: '5y'},
        {type: 'all', text: 'All'}
    ];

    /**
     * @namespace
     */
    App = {
        currentCurrency: null,
        pricesUrl: ENV.prices_url,
        lastRequest: null,
        colors: [
            '#2f7ed8',
            '#0d233a',
            '#8bbc21',
            '#910000',
            '#1aadce',
            '#492970',
            '#f28f43',
            '#77a1e5',
            '#c42525',
            '#a6c96a'
        ],
        init: function initApp() {
            App.Currency.init();
            App.MarketTable.init();
            App.News.init();
            App.PieCharts.init();
            this.loadCurrency('USD');
        },
        ready: function appReady() {
            App.Chart.ready();
            App.Tabs.ready();
            App.MarketTable.ready();
            App.News.ready();
        },
        /**
         * @param {string} currency
         */
        loadCurrency: function loadCurrency(currency) {
            var _this = this;
            this.currentCurrency = currency;
            if (this.lastRequest) {
                this.lastRequest.abort();
            }
            App.Chart.onLoadingData();
            this.lastRequest = $.getJSON(this.getPricesUrl(currency), function parseCurrencyResponse(/*PricesResponse*/data) {
                _this.lastRequest = null;
                App.Chart.loadChartData(data.chart_data);
                App.MarketTable.loadSummary(data.summary);
            });
            this.updateFields();
        },
        /**
         * @private
         */
        updateFields: function updateCurrencyFields() {
            var details = App.Currency.details(this.currentCurrency);
            $('[data-current-currency]').each(function () {
                var $this = $(this);
                $this.text(details[$this.data('current-currency')]);
            });
        },
        /**
         * @private
         * @param {string} to_currency
         * @returns {string}
         */
        getPricesUrl: function appGetPricesUrl(to_currency) {
            return this.pricesUrl + (to_currency ? '?to_currency=' + to_currency : '');
        }
    };

    /**
     * @namespace
     */
    App.Currency = {
        /**
         * @property {CurrencyObject[]} currencyDetails
         */
        currencyDetails: {},
        init: function initCurrency() {
            var _this = this;
            $.each(ENV.currencies, function parseCurrency(i, /*CurrencyObject*/currency) {
                _this.currencyDetails[currency.short] = currency;
            });
        },
        /**
         *
         * @param {string} currency
         * @returns {CurrencyObject}
         */
        details: function currencyDetails(currency) {
            return this.currencyDetails[currency];
        },
        /**
         * Returns short name for currency
         *
         * @param {string} currency
         * @returns {string}
         */
//        shortName: function (currency) {
//            return this.details(currency).short;
//        },
//        /**
//         *
//         * @param {string} currency
//         * @returns {string}
//         */
//        longName: function (currency) {
//            return this.details(currency).long;
//        },
        /**
         *
         * @param {string} currency
         * @returns {string}
         */
        symbol: function (currency) {
            return this.details(currency).symbol;
        },
        /**
         *
         * @param {(number|string)} amount
         * @param {string} currency
         * @returns {string}
         */
        format: function (amount, currency) {
            if (this[currency]) {
                return this[currency](amount, currency);
            }
            return this.default(amount, currency);
        },
        /**
         *
         * @param {(number|string)} amount
         * @param {string} currency
         * @returns {string}
         */
        'default': function (amount, currency) {
            return [this.symbol(currency), amount].join('');
        }
    };

    /**
     * @namespace
     */
    App.Tabs = {
        ready: function readyTabs() {
            var $container = $('#currency-tabs');
            $container.on('click', 'a', function currencyTabsClick(e) {
                var $parent = $(this).parent(),
                    currency;
                e.preventDefault();
                if ($parent.hasClass('active')) {
                    return;
                }
                currency = $(this).data('currency');
                $parent.addClass('active').siblings().removeClass('active');
                App.loadCurrency(currency);
            });
        }
    };

    /**
     * @namespace
     */
    App.Chart = {
        $container: null,
        chart: null,
        ready: function readyCallback() {
            this.$container = $('#container_markets');
            this.initChart();
        },
        initChart: function initChartObject() {
            this.chart = new Highcharts.StockChart({
                chart: {
                    renderTo: this.$container[0]
                },
                rangeSelector: {
                    selected: 0,
                    buttons: rangeButtons
                },
                plotOptions: {
                    series: {
                        dataGrouping: {
                            units: [
                                ['minute', [1]],
                                ['hour', [1]],
                                ['day', [1]]
                            ],
                            groupPixelWidth: 15
                        }
                    }
                },
                yAxis: {
                    plotLines: [
                        {value: 0, width: 2, color: 'silver'}
                    ]
                },
                xAxis: {type: 'datetime', ordinal: false},
                tooltip: {
                    pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> <br/>',
                    valueDecimals: 2
                }
            });
        },
        onLoadingData: function () {
            if (!this.chart) {
                return;
            }
            this.chart.showLoading();
            App.MarketTable.clear();
        },
        /**
         * @param {Array} data
         */
        loadChartData: function loadChartData(data) {
            var chart = this.chart;
            this.clear();
            $.each(data, function (i, series) {
                series.color = App.colors[series.index];
                chart.addSeries(series, false);
            });
            chart.redraw();
            chart.hideLoading();
        },
        clear: function () {
            for (var i = this.chart.series.length - 1; i >= 0; i--) {
                this.chart.series[i].remove(false);
            }
        }
    };

    /**
     * @namespace
     */
    App.MarketTable = {
        tables: null,
        $container: null,
        $tabs: null,
        $template: null,
        init: function initMarketTable() {
            this.tables = [];
        },
        ready: function readyMarketTable() {
            this.$container = $('#summary-content');
            this.$tabs = $('#summary-tabs');
            this.$template = $('#summary-tab-template');
        },
        /**
         * @param {SummaryObject} data
         */
        loadSummary: function loadSummary(data) {
            this.clear();
            this.renderTabs(data.periods);
            this.renderTables(data.data);
        },
        clear: function clearSummaryTable() {
            this.$tabs.empty();
            this.$container.empty();
        },
        renderTabs: function renderTabs(periods) {
            var $cont = this.$tabs;
            $.each(periods, function (key, label) {
                var $tab = $('<li>').append($('<a data-toggle="tab">').attr('href', '#' + key).text(label));
                if (!$cont.children().length) {
                    $tab.addClass('active');
                }
                $cont.append($tab);
            });
        },
        renderTables: function renderTables(periods_data) {
            var $cont = this.$container,
                $template = this.$template;
            $.each(periods_data, function (period, markets) {
                var $tab = $template.clone(),
                    $body = $tab.find('tbody');
                $tab.attr('id', period).removeClass('hide');
                if (!$cont.children().length) {
                    $tab.addClass('active');
                }
                $.each(markets.data, function (i, row) {
                    var $row = $('<tr>');

                    /** @namespace row.market */
                    $row.append($('<td>').text(row.market));
                    /** @namespace row.close */
                    $row.append($('<td>').text(currency(row.close)));
                    /** @namespace row.avg */
                    $row.append($('<td>').text(currency(row.avg)));
                    /** @namespace row.low */
                    $row.append($('<td>').text(currency(row.low)));
                    /** @namespace row.high */
                    $row.append($('<td>').text(currency(row.high)));
                    /** @namespace row.change */
                    $row.append($('<td>').append($('<span class="label">').text(toFixed(row.change) + '%').addClass(row.change >= 0 ? 'label-success' : 'label-danger')));
                    /** @namespace row.volume */
                    $row.append($('<td>').text(currency(row.volume, 4)));

                    $body.append($row);
                });
                $cont.append($tab);
            });
        }
    };

    App.News = {
        $newsContainer: null,
        chart: null,
        news: null,
        type: 0, // -1 = negative, 0 = all, +1 = positive
        lastRange: [0, Math.MAX_VALUE],
        init: function initNews() {
            this.news = ENV.news;
        },
        ready: function readyNews() {
            this.$newsContainer = $('#news-table').find('tbody');
            this.initTabs();
            this.initChart(this.getNewsSeries());
            this.filterNews(this.chart.xAxis[0].min, this.chart.xAxis[0].max);
        },
        initTabs: function initNewsTabs() {
            var _this = this;
            $('#news-tabs').on('click', 'a', function (event) {
                event.preventDefault();
                var $this = $(this);
                _this.type = int($this.data('type'));
                $this.parent().addClass('active').siblings().removeClass('active');
                _this.refilterNews()
            });
        },
        initChart: function initNewsChart(series) {
            this.chart = new Highcharts.StockChart({
                chart: {
                    renderTo: $('#container_chart')[0]
                },
                rangeSelector: {
                    selected: 2,
                    buttons: rangeButtons
                },
                plotOptions: {
                    series: {
                        dataGrouping: {
                            units: [
                                ['minute', [1]],
                                ['hour', [1]],
                                ['day', [1]]
                            ]
                        }
                    }
                },
                title: {
                    text: '',
                    floating: true

                },
                xAxis: {
                    type: 'datetime',
                    ordinal: false,
                    events: {
                        afterSetExtremes: function () {
                            App.News.filterNews(this.min, this.max);
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: 'Bitcoin avg price of USD exchanges'

                    }
                },
                series: series
            });
        },
        getNewsSeries: function getNewsSeries() {
            return ENV.usd_chart;
        },
        refilterNews: function () {
            this._filterNews(this.lastRange[0], this.lastRange[1]);
        },
        filterNews: function filterNews(min, max) {
            if (this._updateTimeout) {
                clearTimeout(this._updateTimeout);
            }
            var _this = this;
            this._updateTimeout = setTimeout(function () {
                _this._updateTimeout = null;
                _this._filterNews(min, max);
            }, 500);
        },
        _filterNews: function filterNewsRender(min, max) {
            this.lastRange = [min, max];

            var $cont = this.$newsContainer,
                type = this.type,
                valid = this.news.filter(function (row) {
                    return row.timestamp >= min && row.timestamp <= max && (!type || (row.change_24h * type >= 0));
                }).sort(function (a, b) {
                    return Math.abs(a.change) < Math.abs(b.change) ? 1 : -1;
                }).slice(0, 5);
            this.$newsContainer.find('tr').addClass('hide');
            $.each(valid, function (i, row) {
                $cont.find('[data-id="' + row.id + '"]').removeClass('hide').find('td:first').text(String.fromCharCode(65 + i));
            });
            var ids = ['positive_news', 'negative_news'],
                id, positive_news = [], negative_news = [], series;
            while (id = ids.pop()) {
                series = this.chart.get(id);
                if (series) {
                    series.remove(false);
                }
            }
            $.each(valid, function (i, row) {
                var container = row.change_24h >= 0 ? positive_news : negative_news;
                var data = {
                    x: row.timestamp,
                    title: String.fromCharCode(65 + i),
                    text: 'Shape: "squarepin"'
                };

                container.push(data);
            });

            this.chart.addSeries({
                id: 'positive_news',
                type: 'flags',
                data: positive_news,
                onSeries: 'all',
                shape: 'squarepin',
                fillColor: '#0b9a00',
                width: 16,
                style: { color: 'white' } // text style
            }, false);

            this.chart.addSeries({
                id: 'negative_news',
                type: 'flags',
                data: negative_news,
                color: '#5F86B3',
                shape: 'squarepin',
                fillColor: '#bf0000',
                onSeries: 'all',
                width: 16,
                style: { color: 'white' }, // text style
                states: { hover: { fillColor: '#395C84' } } // darker
            }, false);

            this.chart.redraw();
        }
    };

    App.PieCharts = {
        init: function initPieCharts() {
            var _this = this;
            google.load("visualization", "1", {packages: ["corechart"]});
            google.setOnLoadCallback(function () {
                _this.drawChart()
            });
        },
        drawChart: function () {
            var dataCurrencies = google.visualization.arrayToDataTable(ENV.data_currencies),
                dataMarkets = google.visualization.arrayToDataTable(ENV.data_markets),
                options = {
                    bar: {groupWidth: '95%'},
                    legend: 'none',
                    pieSliceText: 'label',
                    sliceVisibilityThreshold: 1 / 90,
                    chartArea: {height: '90%', left: 10},
                    backgroundColor: { fill: 'none' }
                };

            var $marketsContainer = $('#top-markets-chart'),
                $currenciesContainer = $('#top-currencies-chart'),
                chartMarkets = new google.visualization.PieChart($marketsContainer[0]),
                chartCurrencies = new google.visualization.PieChart($currenciesContainer[0]);

            chartCurrencies.draw(dataCurrencies, options);
            chartMarkets.draw(dataMarkets, options);
        }
    };

    $(document).on('click', 'a.smooth', function (e) {
        e.preventDefault();
        var $link = $(this);
        var anchor = $link.attr('href');
        $('html, body').stop().animate({
            scrollTop: $(anchor).offset().top
        }, 1000);
    });

    App.init();

    /* MixItUp */
    $(function () {
        App.ready();

        $('#Grid').mixitup();

        $('.hover').on('touchstart', function (e) {
            e.preventDefault();
            $(this).toggleClass('cs-hover');
        });

        // hide #back-top first
        $("#back-top").hide();

        // fade in #back-top
        $(window).scroll(function () {
            if ($(this).scrollTop() > 100) {
                $('#back-top').fadeIn();
            }
            else {
                $('#back-top').fadeOut();
            }
        });

        // scroll body to 0px on click
        $('#back-top').on('click', 'a', function () {
            $('body,html').animate({
                scrollTop: 0
            }, 500);
            return false;
        });
    });
})(window);