@extends('layouts.app')
@section('content')
  <div id="app">


    <div class="container-rem">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card">

            <interest-form></interest-form>
            {{-- <div class="ancien">

            @if(isset($interest))
            <div class="card-header">{{"Modifier un point d'intérêt"}}</div>
          @else
          <div class="card-header">{{"Ajouter un nouveau point d'intérêt"}}</div>
        @endif
        <div class="card-body">

        {!! Form($form) !!}

                    </div> --}}

      </div>
    </div>
  </div>
</div>

</div>
@endsection
@push('scripts')
  <script src="/assets/js/plugins/bootstrap_fileinput/bootstrap_fileinput.min.js" charset="utf-8"></script>
  @endpush
