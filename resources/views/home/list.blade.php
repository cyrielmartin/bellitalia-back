@extends('layouts.app')
@include('layouts.header')
@section('content')
  <div class="container list">
    <table id="interestlist">
      <thead>
        <tr>
          <th>Catégorie</th>
          <th>Point d'intérêt</th>
          <th>Ville</th>
          <th>Région</th>
          <th>Bell'Italia</th>
          <th>Date de publication</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($interests as $interest)
          <tr>
            <td>
              @foreach ($interest->tags as $tag)
                {{$tag->name}}</small>
              @endforeach
            </td>
            <td>{{$interest->name}}</td>
            <td>{{$interest->city->name}}</td>
            <td>{{$interest->city->region->name}}</td>
            <td>{{$interest->bellitalia->number}}</td>
            <td>{{\Carbon\Carbon::parse($interest->bellitalia->publication)->format('m/Y')}}</td>
            <td></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <script>
  $(document).ready(function() {
    $('#interestlist').DataTable(
{
  "language": {
    "lengthMenu": "Afficher _MENU_ résultats par page",
    "search": "Rechercher",
    "zeroRecords": "Aucun résultat",
    "info": "Page _PAGE_ sur _PAGES_",
    "infoEmpty": "Aucun résultat",
    "infoFiltered": "(parmi _MAX_ enregistrements)",
    "paginate": {
      "previous": "précédent",
      "next": "suivant",
    }
  }
}


    );
  } );
  </script>

@endsection
