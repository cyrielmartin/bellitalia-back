@extends('layouts.app')
@include('layouts.header')
@section('content')
  <div class="container-rem">
    {{-- @isset($event)
    {{ Breadcrumbs::render('event.edit', $event) }}
  @else
  {{ Breadcrumbs::render('event.create') }}
@endisset --}}
<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="card">
      @if(isset($info))
        <div class="card-header">{{"Modifier un point d'intérêt"}}</div>
      @else
        <div class="card-header">{{"Ajouter un nouveau point d'intérêt"}}</div>
      @endif
      <div class="card-body">

        {!! Form($form) !!}
        {{-- {!! form_start($form) !!}
        {!! form_until($form) !!} --}}

@push('scripts')
  <script src="/assets/js/plugins/bootstrap_fileinput/bootstrap_fileinput.min.js" charset="utf-8"></script>


@endsection
