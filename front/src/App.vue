<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
  <v-app id="inspire" dark>
    <v-toolbar color="blue-grey darken-4" fixed app right class="theme--dark">
      <v-toolbar-title>Dashboard</v-toolbar-title>
      <v-spacer></v-spacer>
    </v-toolbar>
    <v-content class="blue-grey darken-2">
      <v-container fluid fill-height>
        <v-layout>
          <v-flex text-xs-center>
            <dashboard/>
          </v-flex>
        </v-layout>
      </v-container>
    </v-content>
  </v-app>
</template>

<script>
    import Dashboard from "./views/Dashboard"

    export default {
        name: 'App',
        components: {
          dashboard: Dashboard
        },
        data: () => ({
            drawer: false
        }),
        props: {
            source: String
        },
    }
</script>
