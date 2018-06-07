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
</style>

<script type="text/javascript" src="/js/d3.min.js"></script>
<script type="text/javascript" src="/js/topojson.min.js"></script>
<script type="text/javascript" src="/js/d3-tip.js"></script>

@endsection

@section('content')
<div class="container">
    <div class="columns">
        <div class="column col-12">
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
                            <div class="col-9">
                                <input class="form-input" type="date" id="input-date"
                                value="{{ Carbon\Carbon::now()->toDateString() }}">
                            </div>
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
                        <div id="prov-nama" class="panel-title h5 mt-10">Kalimantan Timur</div>
                    </div>
                    <div class="panel-body">
                        <div class="tile tile-centered">
                            <div class="tile-content">
                                <div class="tile-title">Beras</div>
                                <div class="tile-subtitle">IR III</div>
                            </div>
                            <div class="tile-action">
                                10000
                            </div>
                        </div>
                        <div class="tile tile-centered">
                            <div class="tile-content">
                                <div class="tile-title">Daging Ayam Kampung</div>
                                <div class="tile-subtitle">Tanpa Jeroan</div>
                            </div>
                            <div class="tile-action">
                                10000
                            </div>
                        </div>
                        <div class="tile tile-centered">
                            <div class="tile-content">
                                <div class="tile-title">Bawang Merah</div>
                                <div class="tile-subtitle">Bersih Sedang</div>
                            </div>
                            <div class="tile-action">
                                10000
                            </div>
                        </div>
                        <div class="tile tile-centered">
                            <div class="tile-content">
                                <div class="tile-title">Gabus Kering</div>
                                <div class="tile-subtitle">Besar</div>
                            </div>
                            <div class="tile-action">
                                10000
                            </div>
                        </div>
                        <div class="tile tile-centered">
                            <div class="tile-content">
                                <div class="tile-title">Minyak Goreng</div>
                                <div class="tile-subtitle">Tanpa Merk (Kuning)</div>
                            </div>
                            <div class="tile-action">
                                10000
                            </div>
                        </div>
                        <div class="tile tile-centered">
                            <div class="tile-content">
                                <div class="tile-title">Gula Pasir</div>
                                <div class="tile-subtitle">SHS/Putih</div>
                            </div>
                            <div class="tile-action">
                                10000
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <button class="btn btn-primary btn-block">Percobaan Pasbeli</button>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection

@section('endjs')
<script>
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

    d3.queue()
        .defer(d3.json, "/map/indo-quantized.json")
        .await(visualize)

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
                    changeProvProfile(d.properties.ID_1);
                    //update chart lain
                    updateBarChart(d.properties.ID_1);
                }
            })
            .on('mouseover', mapTip.show)
            .on('mouseout', mapTip.hide);
    }
</script>
@endsection