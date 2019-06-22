<template>
  <v-layout>
    Template
  </v-layout>
</template>
<script>
export default {
  name: 'Dashboard',
  data: () => ({

  }),
  beforeMount() {
    this.axios.get("/server/list").then((data) => {
      console.log(data);
    });
  },
  computed: {

  }
}
</script>

<style>

</style>
