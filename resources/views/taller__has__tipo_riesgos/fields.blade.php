<!-- Tiporiesgos Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('TipoRiesgos_id', 'Tiporiesgos Id:') !!}
    {!! Form::number('TipoRiesgos_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('tallerHasTipoRiesgos.index') !!}" class="btn btn-default">Cancel</a>
</div>
