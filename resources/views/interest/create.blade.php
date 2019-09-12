@extends('layouts.app')
@include('layouts.header')
@section('content')

  <div class="container-rem">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div id="app">


            {{-- Composant Vue --}}
            <interest-form></interest-form>
          </div>
        </div>
      </div>
    </div>
  </div>
  @push('scripts')
    <script src="/assets/js/plugins/bootstrap_fileinput/bootstrap_fileinput.min.js" charset="utf-8"></script>
    <script src="/js/app.js"></script>

@endsection
