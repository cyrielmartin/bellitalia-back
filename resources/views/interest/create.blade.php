@extends('layouts.app')
@include('layouts.header')
@section('content')
  <div id="app">
dfdfdf
  </div>
  <div class="container-rem">
<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="card">
      @if(isset($interest))
        <div class="card-header">{{"Modifier un point d'intérêt"}}</div>
      @else
        <div class="card-header">{{"Ajouter un nouveau point d'intérêt"}}</div>
      @endif
      <div class="card-body">

        {!! Form($form) !!}

@push('scripts')
  <script src="/assets/js/plugins/bootstrap_fileinput/bootstrap_fileinput.min.js" charset="utf-8"></script>


@endsection
