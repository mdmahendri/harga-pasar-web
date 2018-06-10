@extends('layouts.app')
@section('title', 'Reverse Geocoding')

@section('content')
<div class="container">
    <div class="columns">
        <div class="column col-12">
            <h1>Reverse Geocoding</h1>
            <span>Terdapat {{ $count }} data belum dilakukan reverse geocoding</span>
        </div>
    </div>
</div>

<div class="container">
    <div class="columns">
        <div class="column col-10">
            <div class="bar">
                <div id="bar-proc" class="bar-item tooltip" data-tooltip="50%" role="progressbar" style="width:1%;"></div>
            </div>
            <span id="num-proc">0</span>/{{ $count }}
        </div>
        <div class="column col-2">
            <button id="btn-act" class="btn btn-sm btn-primary">Lakukan Rev Geo</button>
        </div>
    </div>
</div>
@endsection

@section('endjs')
<script>
var counter = {{ $count }};

document.getElementById('btn-act').onclick = function(){
    var buttonSubmit = this;
    buttonSubmit.classList.add('loading');

    try {
        var xhr = new XMLHttpRequest();
        xhr.addEventListener('progress', function(evt) {
            var lines = evt.currentTarget.response.split('n');
            if(lines.length) {
                var progress = lines[lines.length-1];
            } else {
                var progress = 0;
            }

            document.getElementById('bar-proc').style.width = progress*100 / counter + '%';
            document.getElementById('num-proc').innerHTML = progress;
        });
        xhr.addEventListener('load', function(evt) {
            buttonSubmit.classList.replace('loading', 'disabled');
        });
        xhr.open('GET', '/api/revgeo', true);
        xhr.send();
    } catch (e) {
        console.log(e);
    }
}
</script>
@endsection