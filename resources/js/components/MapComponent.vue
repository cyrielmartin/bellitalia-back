<template>
  <div id="mapid">

  </div>

</template>

<script>
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
      interestList: []
    }
  },
  mounted() {
    this.map = L.map('mapid').setView([40.853294, 14.305573], 5.5);
    L.tileLayer('https://maps.heigit.org/openmapsurfer/tiles/roads/webmercator/{z}/{x}/{y}.png').addTo(this.map);

    axios
    .get('/api/interest')
    .then(response => (this.interestList = response))

    var loc = [51.5076, -0.1276];
    var marker = L.marker(loc).addTo(this.map);
    marker.bindTooltip("Pop up");
    console.log(this.interestList);
  },

}


</script>
