@extends('layouts.app')
@section('title', 'Monitor Hasil')

@section('assets')
<style>
body {
    background: #EEEEEE;
    padding: 20px;
}

.icon {
    width: 100px;
    height: 100px;
}

.shadow {
    box-shadow: 0 1px 20px 3px rgba(0,0,0,.1);
}
/* tip style */
.d3-tip {
    line-height: 1;
    padding: 12px;
    background: rgba(0, 0, 0, 0.8);
    color: #fff;
    border-radius: 2px;
    font-size: 10px;
}

/* Style northward tooltips differently */
.d3-tip.n:after {
    margin: -1px 0 0 0;
    top: 100%;
    left: 0;
}

/* Prevent long list data */
#panel-harga {
    height: 408px;
}
</style>

<!-- used to config map -->
<script type="text/javascript" src="/js/d3.min.js"></script>
<script type="text/javascript" src="/js/topojson.min.js"></script>
<script type="text/javascript" src="/js/d3-tip.js"></script>

<!-- used to make date range picker. no dependency, lightweight -->
<link rel="stylesheet" href="/css/flatpickr.min.css" />
<script type="text/javascript" src="/js/flatpickr.min.js"></script>

@endsection

@section('content')
<div class="container">
    <div class="columns">
        <div class="column col-6 col-mx-auto">
            <div class="tile">
                <div class="tile-icon">
                <img src="/img/icon.svg" class="icon">
                </div>
                <div class="tile-content">
                    <p class="tile-title h1">Grafik Monitoring</p>
                    <p class="tile-subtitle text-gray h3">Data Harga Barang menurut SHK</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="columns">
        <div class="column col-8">
            <div class="card shadow">
                <div class="card-header">
                    <div class="card-title h4">Rata-Rata Harga Berdasar Provinsi</div>
                </div>
                <div class="card-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <div class="col-3">
                                <label class="form-label" for="input-date">Pilih tanggal</label>
                            </div>
                            <input id="input-date" class="col-9 form-input" placeholder="Click this..."
                            readonly>
                        </div>
                    </form>
                </div>
                <div class="card-image">
                    <svg id="map" width="800" height="400">
                        <g id="threshold" transform="translate(20,350)"></g>
                    </svg>
                </div>
            </div>
        </div>

        <div class="column col-4">
            <div class="card shadow">
                <div class="panel">
                    <div class="panel-header text-center">
                        <figure class="avatar avatar-lg">
                            <img id="prov-img" src="/img/prov/blank.png" alt="Avatar">
                        </figure>
                        <div id="prov-nama" class="panel-title h5 mt-10">-</div>
                    </div>
                    <div id="panel-harga" class="panel-body">
                    </div>
                    <div class="panel-footer">
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection

@section('endjs')
<script>
    // init reset
    resetSelection();

    // config range date picker
    const fp = flatpickr(document.getElementById('input-date'), {
        mode: 'range',
        dateFormat: 'd-m-Y',
        defaultDate: [
            "{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}", 
            "{{ Carbon\Carbon::now()->format('d-m-Y') }}"
        ],
        maxDate: 'today',
        onChange: function(selectedDates, dateStr, instance) {
            resetSelection();
        }
    });

    // membuat tooltip untuk peta
    var mapTip = d3.tip()
        .attr('class', 'd3-tip')
        .offset([-10, 0])
        .html(function(d) {
            return 'Provinsi ' + d.properties.NAME_1;
        });

    // buat batas skema warna peta
    var threshold = d3.scaleThreshold()
        .domain([1900, 5000, 15000, 35000])
        .range(['#B3E5FC', '#4FC3F7', '#03A9F4', '#0288D1', '#01579B']);

    // render map and its function
    d3.queue()
        .defer(d3.json, '/map/indo-quantized.json')
        .await(visualize);

    function resetSelection() {
        d3.select('#map')
            .selectAll('.map-path')
            .classed('selected-path', false)
            .attr('fill', 'blue');

        document.getElementById('prov-nama').innerHTML = '-';
        document.getElementById('prov-img').src = '/img/prov/blank.png';
        document.getElementById('panel-harga').innerHTML =
            '<h4 class="text-center">Tidak ada data</h4>'
            + '<p>Pilih tangal dan provinsi untuk menampilkan data</p>';
    }

    function visualize(error, data) {

        if (error) { throw error; }

        // load topo
        const indoMap = topojson.feature(data, {
            type: 'GeometryCollection',
            geometries: data.objects.indo.geometries
        });

        // menggunakan proyeksi Mercator
        const projection = d3.geoMercator()
            .fitExtent([ [20,20], [800,400] ], indoMap);

        // generate path
        const geoPath = d3.geoPath()
            .projection(projection);

        // load map svg
        var map = d3.select('#map')
            .call(mapTip);

        // insert path
        map.selectAll('.map-path')
            .data(indoMap.features)
            .enter().append('path')
            .attr('class', 'map-path')
            .attr('d', geoPath)
            .attr('fill', 'blue')
            .on('click', function(d){
                var current = d3.select(this);
                if (!current.classed('selected-path')) {
                    d3.selectAll('.map-path')
                        .classed('selected-path', false)
                        .attr('fill', 'grey');

                    current
                        .attr('fill', 'blue')
                        .classed('selected-path', true);

                    //tampilkan profil provinsi
                    changeProvData(d.properties.NAME_1);
                }
            })
            .on('mouseover', mapTip.show)
            .on('mouseout', mapTip.hide);
    }

    function changeProvData(name) {
        toggleLoading(true, name);

        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState == 4) {
                var result = JSON.parse(this.responseText);
                toggleLoading(false);
                renderResponse(result);
            }
        }
        const start = fp.selectedDates[0].getTime();
        const end = fp.selectedDates[1].getTime();
        var slug = name.toLowerCase().replace(new RegExp(' ', 'g'), '-');
        xhr.open('GET', '/api/data/' + name + '/' + start + '/' + end, true);
        xhr.send();

        document.getElementById('prov-nama').innerHTML = name;
        document.getElementById('prov-img').src = '/img/prov/' + slug + '.png';
    }

    function toggleLoading(activate, prov = '') {
        if (activate) {
            var load = '<div class="loading loading-lg"></div>';
            load += '<div class="text-center">Silahkan tunggu</div>';
            load += '<p class="text-center">Memproses data ' + prov + '</p>';
            document.getElementById('panel-harga').innerHTML = load;
        } else {
            document.getElementById('panel-harga').innerHTML = '';
        }
    }

    function renderResponse(result) {
        var load = '';

        if (result.status == 'fail') {
            load = '<div class="text-center">Data tidak tersedia</div>';
            load += '<p class="text-center">' + result.data + '</p>';
        } else {
            result.data.forEach(function(entry) {
                load += '<div class="tile tile-centered">'
                +          '<div class="tile-content">'
                +              '<div class="tile-title">' + entry.nama + '</div>'
                +              '<div class="tile-subtitle">' + entry.kualitas + '</div>'
                +          '</div>'
                +          '<div class="tile-action">'
                +              Math.round(entry.harga)
                +          '</div>'
                +      '</div>';
            });
        }
        document.getElementById('panel-harga').innerHTML = load;
    }
</script>
@endsection