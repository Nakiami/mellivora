<?php

require('../include/mellivora.inc.php');

login_session_refresh();

send_cache_headers('graph', CONFIG_CACHE_TIME_GRAPH);

head(lang_get('graph'));

if (cache_start(CONST_CACHE_NAME_GRAPH, CONFIG_CACHE_TIME_GRAPH)) {

    ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/2.10.0/d3.v2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/rickshaw/1.6.3/rickshaw.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/rickshaw/1.6.3/rickshaw.min.css" rel="stylesheet">

    <style>

        #chart_container {
            width: 960px;
        }

        .swatch {
            display: inline-block;
            width: 10px;
            height: 10px;
            margin: 0 8px 0 0;
        }

        .label {
            color: #000000;
            display: inline-block;
        }

        .line {
            display: block;
            margin: 0 0 0 30px;
        }

        #legend {
            text-align: center;
        }

        .rickshaw_graph .detail {
            background: none;
        }

    </style>

    <div id="chart"></div>
    <div id="legend"></div>

    <script>

        var stringToColour = function (str) {
            var hash = 0;
            for (var i = 0; i < str.length; i++) {
                hash = str.charCodeAt(i) + ((hash << 5) - hash);
            }
            var colour = '#';
            for (var i = 0; i < 3; i++) {
                var value = (hash >> (i * 8)) & 0xFF;
                colour += ('00' + value.toString(16)).substr(-2);
            }
            return colour;
        };

        var seriesData = [[], [], [], [], [], [], [], [], [], []];
        var random = new Rickshaw.Fixtures.RandomData(150);

        for (var i = 0; i < 150; i++) {
            random.addData(seriesData);
        }

        var series = [];
        for (var i = 0; i < 10; i++) {
            var name = Math.random().toString(36).substring(7);
            series[i] = {
                color: stringToColour(name),
                data: seriesData[i],
                name: name
            };
        }

        var graph = new Rickshaw.Graph({
            element: document.getElementById("chart"),
            width: 960,
            height: 500,
            renderer: 'line',
            series: series
        });

        graph.render();

        var legend = document.querySelector('#legend');

        var Hover = Rickshaw.Class.create(Rickshaw.Graph.HoverDetail, {

            render: function (args) {

                legend.innerHTML = args.formattedXValue;

                args.detail.sort(function (a, b) {
                    return a.order - b.order
                }).forEach(function (d) {

                    var line = document.createElement('div');
                    line.className = 'line';

                    var swatch = document.createElement('div');
                    swatch.className = 'swatch';
                    swatch.style.backgroundColor = d.series.color;

                    var label = document.createElement('div');
                    label.className = 'label';
                    label.innerHTML = d.name + ": " + d.formattedYValue;

                    line.appendChild(swatch);
                    line.appendChild(label);

                    legend.appendChild(line);

                    var dot = document.createElement('div');
                    dot.className = 'dot';
                    dot.style.top = graph.y(d.value.y0 + d.value.y) + 'px';
                    dot.style.borderColor = d.series.color;

                    this.element.appendChild(dot);

                    dot.className = 'dot active';

                    this.show();

                }, this);
            }
        });

        var hover = new Hover({graph: graph});
    </script>

    <?php

    cache_end(CONST_CACHE_NAME_GRAPH);
}

foot();
