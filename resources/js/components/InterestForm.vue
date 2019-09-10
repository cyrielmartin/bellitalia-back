<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-10">
        <div class="card">
          <div class="card-header">Ajouter un nouveau point d'intérêt</div>

          <div class="card-body">

            <form
            @submit.prevent="submitForm"
            novalidate="true">
            <div class="form-group">
              <label>Nom du point d'intérêt *</label>
              <input class="form-control" v-model="interestName">
              <!-- <input name="name" type="text" id="name" class="form-control"> -->
            </div>
            <div class="form-group">
              <label>Description</label>
              <textarea cols="50" rows="5" class="form-control" v-model="interestDescription"></textarea>
              <!-- <textarea name="description" cols="50" rows="10" id="description" class="form-control"></textarea> -->
            </div>
            <div class="form-group">
              <label>Lien</label>
              <input class="form-control" v-model="interestLink">
              <!-- <input name="link" type="url" id="link" class="form-control"> -->
            </div>
            <div class="form-group">
              <label>Latitude *</label>
              <input type="number" min="0" max="100" step="any" class="form-control" v-model="interestLatitude">
              <!-- <label for="latitude" class="control-label required">Latitude *</label> -->
              <!-- <input step="1.0E-7" name="latitude" type="number" id="latitude" class="form-control"> -->
            </div>
            <div class="form-group">
              <label>Longitude *</label>
              <input type="number" min="0" max="100" step="any" class="form-control" v-model="interestLongitude">
              <!-- <label for="longitude" class="control-label required">Longitude *</label>
              <input step="1.0E-8" name="longitude" type="number" id="longitude" class="form-control"> -->
            </div>
            <div class="form-group">
              <label>Nom de la ville *</label>
              <input class="form-control" v-model="interestCity">
              <!-- <label for="city[name]" class="control-label required">Nom de la ville *</label>
              <input name="city[name]" type="text" id="city[name]" class="form-control"> -->
            </div>
            <div class="form-group">
              <label>Nom de la région *</label>
              <input class="form-control" v-model="interestRegion">
              <!-- <label for="region[name]" class="control-label required">Nom de la région *</label>
              <input name="region[name]" type="text" value="" id="region[name]" class="form-control"> -->
            </div>
            <div class="form-group">
              <label>Numéro du Bell'Italia *</label>
              <input type="number" class="form-control" v-model="interestNumber">
              <!-- <label for="bellitalia[number]" class="control-label required">Numéro du Bell'Italia *</label>
              <input name="bellitalia[number]" type="number" id="bellitalia[number]" class="form-control"> -->
            </div>
            <div class="form-group">
              <label>Date de publication du Bell'Italia *</label>
              <input type="date" class="form-control" v-model="interestDate">
              <!-- <label for="bellitalia[publication]" class="control-label required">Date de publication du Bell'Italia *</label>
              <input name="bellitalia[publication]" type="date" value="" id="bellitalia[publication]" class="form-control"> -->
            </div>
            <div class="form-group">
              <label>Catégorie</label>
              <input class="form-control" v-model="interestCategory">
              <!-- <label for="tag[name]" class="control-label">Catégorie</label>
              <input name="tag[name]" type="text" value="" id="tag[name]" class="form-control"> -->
            </div>
            <div class="d-flex justify-content-center">
              <button type="submit" class="btn btn-fill btn-blue">Sauvegarder</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</template>

<script>
export default {
  data() {
    return {
      interestName: '',
      interestDescription:'',
      interestLink:'',
      interestLatitude:'',
      interestLongitude:'',
      interestCity:'',
      interestRegion:'',
      interestNumber:'',
      interestDate:'',
      interestCategory:'',
      interestDate:'',
    }
  },
  methods: {
    submitForm() {
      axios.post('/api/interest', {
        name: this.interestName,
        description: this.interestDescription,
        link: this.interestLink,
        latitude: this.interestLatitude,
        longitude: this.interestLongitude,
        city_id: this.interestCity,
        region_id: this.interestRegion,
        bellitalia_id: this.interestNumber,
        publication: this.interestDate

      })
    },
  },
}
</script>
