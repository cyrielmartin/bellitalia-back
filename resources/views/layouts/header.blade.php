
<div class="content">
    <div class="title m-b-md">
        <a href="/">Bell'Italia</a>
    </div>


  <ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Carte</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Liste</a>
  </li>
</ul>
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">@yield('map')</div>
  <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">@yield('list')</div>
</div>
</div>
