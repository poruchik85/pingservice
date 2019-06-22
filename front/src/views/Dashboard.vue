<template>
  <v-layout row wrap>
    <v-flex class="column" xs7 md7 sm7>
      <v-layout row wrap>
    <v-flex class="column" xs6 md6 sm6>
      <v-expansion-panel expand="true">
        <v-expansion-panel-content
                v-for="(group, i) in groups"
                :key="i"
        >
          <template v-slot:header>
            <div><v-icon small @click.prevent="editGroup(group)" color="primary">edit</v-icon>  {{group.name}}</div>
          </template>
          <v-card>
            <v-card-text class="server-list-card">
              <v-list>
                <template v-for="(server, index) in group.servers">
                  <v-list-tile
                          :key="index"
                          @click="selectServer(server)"
                  >
                    <v-list-tile-content>
                      <v-list-tile-title class="server-list-title">
                        <v-icon class="server-list-info" v-if="server.pings.length===0">
                          brightness_1
                        </v-icon>
                        <v-icon class="server-list-info success--text" v-else-if="server.pings[0].success">
                          check_circle
                        </v-icon>
                        <v-icon class="server-list-info error--text" v-else>
                          cancel
                        </v-icon>{{server.host ? server.host : server.ip}}
                        <v-tooltip right v-if="server.host">
                          <template v-slot:activator="{ on }">
                            <span v-on="on" class="ip-helper">ip</span>
                          </template>
                          <span>{{server.ip}}</span>
                        </v-tooltip>
                      </v-list-tile-title>
                      <v-list-tile-sub-title v-if="server.pings.length > 0">
                        Last ping {{server.pings[0].created_at}}
                      </v-list-tile-sub-title>
                    </v-list-tile-content>
                  </v-list-tile>
                  <v-divider v-if="index+1 < group.servers.length"></v-divider>
                </template>
              </v-list>
            </v-card-text>
          </v-card>
        </v-expansion-panel-content>
      </v-expansion-panel>
    </v-flex>
    <v-flex class="column" xs6 md6 sm6>
      <v-card>
        <v-card-title>
          <template v-if="server===null"><v-icon class="error--text mr-3">arrow_back</v-icon> Please select server</template>
          <template v-else>
            <template v-if="server.host">
              {{server.host}} ({{server.ip}})
            </template>
            <template v-else-if="server.ip">
              {{server.ip}}
            </template>
            <v-spacer></v-spacer>
            <v-btn color="success" small @click="ping">PING</v-btn>
          </template>
        </v-card-title>
        <v-card-text class="server-list-card" v-if="server!==null">
          <div v-for="ping in server.pings">
            <v-icon class="ping-list-info success--text" v-if="ping.success">
              check_circle
            </v-icon>
            <v-icon class="ping-list-info error--text" v-else>
              cancel
            </v-icon>
            {{ping.created_at}}
          </div>
        </v-card-text>
      </v-card>
    </v-flex>
      </v-layout>
    </v-flex>
    <v-flex class="column" xs5 md5 sm5>
    <v-flex class="column" xs12 md12 sm12>
      <v-card>
        <v-card-title>
          <template v-if="editedGroup.id===null">Create group</template>
          <template v-else>Edit group &nbsp;<span class="accent--text font-weight-bold">{{editedGroupName}}</span></template>
        </v-card-title>
        <v-card-text>
          <v-flex xs12 sm12 md12>
            <v-text-field label="Group title" v-model="editedGroup.name"></v-text-field>
          </v-flex>
          <v-flex xs12 sm12 md12>
            <v-spacer></v-spacer>
            <v-btn color="accent" small @click="cancelEditGroup">Cancel</v-btn>
            <v-btn color="success" small>Save</v-btn>
          </v-flex>
        </v-card-text>
      </v-card>
    </v-flex>
    <v-flex class="column" xs12 md12 sm12>
      <v-card>
        <v-card-title>
          Create/edit server
        </v-card-title>
        <v-card-text>

        </v-card-text>
      </v-card>
    </v-flex>
    </v-flex>
  </v-layout>
</template>
<script>
export default {
  name: 'Dashboard',
  data: () => ({
    groups: {},
    server: null,
    defaultEditedGroup: {
      id: null,
      name: null
    },
    editedGroup: {
      id: null,
      name: null
    },
    editedGroupName: null,
  }),
  methods: {
    selectServer(server) {
      this.server = server;
    },
    editGroup(group) {
      this.editedGroup = {id: group.id, name: group.name};
      this.editedGroupName = group.name;
    },
    cancelEditGroup() {
      this.editedGroup = {...this.defaultEditedGroup};
      this.editedGroupName = null;
    },
    ping() {
      this.axios.post("/dashboard/ping/" + this.server.id).then((data) => {
        this.groups = data.data;
      });
    }
  },
  beforeMount() {
    this.axios.get("/dashboard").then((data) => {
      this.groups = data.data;
    });
  },
  computed: {

  }
}
</script>

<style>
  .server-list-title {
    font-size: 14px;
  }
  .server-list-card {
    padding: 0;
  }
  .server-list-info {
    font-size: 22px;
    margin-right: 10px;
  }
  .ping-list-info {
    font-size: 18px;
    margin-right: 10px;
  }
  .ip-helper {
    border: 1px dotted;
    padding: 1px 4px;
    color: rgba(255,255,255,0.7);
    cursor: help;
  }
  .column {
    padding: 10px;
  }
</style>
