@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">Area Influencias</h1>
        <h1 class="pull-right">
           <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('areaInfluencias.create') !!}">Agregar Nuevo</a>
        </h1>
    </section>
    <section>
    <a href="{{ route('areaInfluenciasHTMLPDF',['descargar'=>'pdf']) }}" target="_blank" class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px">Ver PDF</a>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
  <div class="box box-primary">
    <ul class="nav nav-tabs" role="tablist">
      <li role="presentation" class="active"><a href="#list" aria-controls="list" role="tab" data-toggle="tab">Lista</a></li>
      @if (!empty($chart) and  !empty($chart->datasets))
      <li role="presentation"><a href="#graph" aria-controls="graph" role="tab" data-toggle="tab">Gráfico</a></li>
      @endif
    </ul>
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="list">
        <div class="box-body">
                    @include('area_influencias.table')
        </div>
      </div>
      @if (!empty($chart) and  !empty($chart->datasets))
      <div role="tabpanel" class="tab-pane" id="graph">

        {!! Form::open(['route' => 'areaInfluencias.index', 'method' => 'GET','class' => 'navbar-form navbar-left pull-right', 'role' => 'search', 'id' => 'areaInfuenciaForm']) !!}
                        <div class="form-group">
                          {!! Form::select('selectareaInfluencias', ['Manejo Ambiental','Permeabilidad del suelo','Clima','Condicion de Drenaje','Ecosistema'] ,null, ['class' => 'form-control', 'placeholder'=>'Seleccione un parámetro', 'id' => 'mySelect']) !!}
                        </div>
        {!! Form::close() !!}

        <div>{!! $chart->container() !!}</div>
        <div id="container"></div>
        <script src="//cdnjs.cloudflare.com/ajax/libs/highcharts/6.0.6/highcharts.js" charset="utf-8"></script>
        {!! $chart->script() !!}
      </div>
      @endif

    </div>
  </div>
  <div class="text-center">

  </div>
</div>
@endsection

@section('scripts')
<script>
    $('#mySelect').change(function(e) {
        $( "#areaInfuenciaForm" ).submit();
        e.preventDefault();
   });

    var map;
    var marker;
    var coords_lat = new Array();
    var coords_lng = new Array();
    var nombres = new Array();
    var cont_lat=0;
    var cont_lng=0, cont_nomb=0;

    var datos = document.getElementById('areaInfluencias-table').getElementsByTagName('td');
    for(var i=0;i<datos.length;i++){

        if(((i-1)%32)==0){
            coords_lat[cont_lat]=datos[i].innerHTML;
            cont_lat++;
        }else{
            if(((i-2)%32)==0){
                coords_lng[cont_lng]=datos[i].innerHTML;
                cont_lng++;
            }else{
                if(((i)%32)==0){
                    nombres[cont_nomb]=datos[i].innerHTML;
                    cont_nomb++;
                }
            }
        }
    }

    function initMap() {

        map = new google.maps.Map(document.getElementById('map_index'), {
            center: {lat: 0.14309590647516032, lng: -78.80012830863114},
            zoom: 10,
            draggable: true,
            zoomControl: true,
            scrollwheel: true,
            disableDoubleClickZoom: true
        });

        var infowindow = new google.maps.InfoWindow();
        var marker, i;

        for (i = 0; i < coords_lng.length; i++) {
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(coords_lat[i], coords_lng[i]),
                map: map
            });
            google.maps.event.addListener(marker, 'click', (function(marker, i) {
              return function() {
                infowindow.setContent(nombres[i]);
                infowindow.open(map, marker);
              }
            })(marker, i));
        }
    }

</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBTIGv1Whkt5IaGpQe6iifIx3RKz798tr8&callback=initMap"></script>
@endsection


@section('css')
<style type="text/css">
#map_index {
    width: 100%;
    height: 300px;
}
</style>
@endsection
