<template>

  <div id="mapid">

  </div>

</template>

<script>
import { log } from 'util';
export default {
}
</script>

<style lang="css" scoped>
</style>


<script>
export default{
  data() {
    return {
      map: null,
      interestList: [],
      loc: [],
      marker: null
    }
  },
  mounted() {

    // Création de la carte
    this.map = L.map('mapid').setView([40.853294, 14.305573], 5.5);

    //Surcouche de design
    L.tileLayer('https://maps.heigit.org/openmapsurfer/tiles/roads/webmercator/{z}/{x}/{y}.png').addTo(this.map);

    //Requête Axios pour récupérer les données en BDD
    axios
    .get('/api/interest')
    .then(response => (
        this.interestList = response.data,
        //Pour chacun des points d'intérêt en BDD, récupération des coordonnées
        this.interestList.forEach(element => {
         this.loc = [Number(element.latitude), Number(element.longitude)],
         //Création des marqueurs
         this.marker = L.marker(this.loc).addTo(this.map),
         //Création des popup et de leur contenu
         this.marker.bindPopup("<div>"+element.name+"</div><div>"+element.description+"</div><p><a target='_blank' rel='noopener noreferrer' href='"+element.link+"'>Photos</a></p>")
        })
    ));

  },
}


</script>
