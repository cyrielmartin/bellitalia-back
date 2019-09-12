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
              <input class="form-control" v-model="interestName" :class="{'border-red': errors.name}">
              <p class="text-error" v-if="errors.name" v-text="errors.name[0]"></p>
            </div>

            <div class="form-group">
              <label>Description</label>
              <textarea cols="50" rows="5" class="form-control" v-model="interestDescription"></textarea>
            </div>

            <div class="form-group">
              <label>Lien</label>
              <input class="form-control" v-model="interestLink">
            </div>

            <div class="form-group">
              <label>Latitude *</label>
              <input type="number" min="0" max="100" step="1.0E-7" class="form-control" v-model="interestLatitude" :class="{'border-red': errors.latitude}">
              <p class="text-error" v-if="errors.latitude" v-text="errors.latitude[0]"></p>
            </div>

            <div class="form-group">
              <label>Longitude *</label>
              <input type="number" min="0" max="100" step="1.0E-8" class="form-control" v-model="interestLongitude" :class="{'border-red': errors.longitude}">
              <p class="text-error" v-if="errors.longitude" v-text="errors.longitude[0]"></p>
            </div>

            <div class="form-group">
              <label>Nom de la ville *</label>
              <input class="form-control" v-model="interestCity" :class="{'border-red': errors.city_id}">
              <p class="text-error" v-if="errors.city_id" v-text="errors.city_id[0]"></p>
            </div>

            <div class="form-group">
              <label>Nom de la région *</label>
              <input class="form-control" v-model="interestRegion" :class="{'border-red': errors.region_id}">
              <p class="text-error" v-if="errors.region_id" v-text="errors.region_id[0]"></p>
            </div>

            <div class="form-group">
              <label>Numéro du Bell'Italia *</label>
              <input type="number" class="form-control" v-model="interestNumber" :class="{'border-red': errors.bellitalia_id}">
              <p class="text-error" v-if="errors.bellitalia_id" v-text="errors.bellitalia_id[0]"></p>
            </div>

            <div class="form-group">
              <label>Date de publication du Bell'Italia *</label>
              <input type="date" class="form-control" v-model="interestDate" :class="{'border-red': errors.publication}">
              <p class="text-error" v-if="errors.publication" v-text="errors.publication[0]"></p>
            </div>

            <div class="form-group">
              <label>Catégorie</label>
              <input class="form-control" v-model="interestCategory">
              <p class="text-error" v-if="errors.category_id" v-text="errors.category_id[0]"></p>
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
      errors: {},
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
        publication: this.interestDate,
        category_id: this.interestCategory,

      })
      .then(() => {
        this.interestName = ""
        this.interestDescription = ""
        this.interestLink = ""
        this.interestLatitude = ""
        this.interestLongitude = ""
        this.interestCity = ""
        this.interestRegion = ""
        this.interestNumber = ""
        this.interestDate = ""
        this.interestCategory = ""
        this.errors = {}
      })
      .catch(error => {
        this.errors = error.response.data
      })
    },
  },
}
</script>
