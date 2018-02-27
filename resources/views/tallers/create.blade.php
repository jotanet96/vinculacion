@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Tipos de Taller
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'tallers.store']) !!}

                        @include('tallers.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
