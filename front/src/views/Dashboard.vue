<template>
  <v-layout row wrap>
    <v-flex class="column" xs7 md7 sm7>
      <v-layout row wrap>
    <v-flex class="column" xs6 md6 sm6>
      <v-expansion-panel :expand="true">
        <v-expansion-panel-content
                v-for="(group, groupIndex) in groups"
                :key="groupIndex"
        >
          <template v-slot:header>
            <div>{{group.name}}</div>
          </template>
          <v-card>
            <v-card-text class="server-list-card">
              <v-list>
                <v-list-tile class="server-group-dashboard">
                  <v-tooltip top>
                    <template v-slot:activator="{ on }">
                      <v-icon v-on="on" small @click.prevent="editGroup(group)" color="primary" class="mr-1">edit</v-icon>
                    </template>
                    <span>Edit group</span>
                  </v-tooltip>
                  <v-tooltip top>
                    <template v-slot:activator="{ on }">
                      <v-icon v-on="on" small @click.prevent="createServerForGroup(group)" color="success">add_circle</v-icon>
                    </template>
                    <span>Add server</span>
                  </v-tooltip>
                    <v-spacer></v-spacer>
                  <v-tooltip top>
                    <template v-slot:activator="{ on }">
                      <v-icon v-on="on" small @click.prevent="deleteGroup(group)" color="error">delete</v-icon>
                    </template>
                    <span>Remove group</span>
                  </v-tooltip>
                </v-list-tile>
                <v-divider></v-divider>
                <template v-if="group.servers.length > 0" v-for="(server, serverIndex) in group.servers">
                  <v-list-tile
                          :key="serverIndex"
                          @click="selectServer(groupIndex, serverIndex)"
                  >
                    <v-list-tile-content>
                      <v-list-tile-title class="server-list-title">
                        <v-tooltip top>
                          <template v-slot:activator="{ on }">
                            <v-icon v-on="on" small class="server-list-info primary--text mr-1" @click="editServer(server)">edit</v-icon>
                          </template>
                          <span>Edit server</span>
                        </v-tooltip>
                        <v-icon
                                small
                                class="server-list-info mr-1"
                                :class="{'success--text': server.pings.length>0 && server.pings[0].success === true, 'error--text': server.pings.length>0 && server.pings[0].success === false}"
                        >
                          brightness_1
                        </v-icon>{{server.name ? server.name : server.ip}}
                        <v-tooltip right v-if="server.name && server.name !== server.ip">
                          <template v-slot:activator="{ on }">
                            <span v-on="on" class="ip-helper">?</span>
                          </template>
                          <span>{{server.ip}}</span>
                        </v-tooltip>
                        <v-tooltip top>
                          <template v-slot:activator="{ on }">
                            <v-icon v-on="on" small class="server-list-info server-list-info-right server-list-info error--text" @click="deleteServer(server)">delete</v-icon>
                          </template>
                          <span>Remove server</span>
                        </v-tooltip>
                      </v-list-tile-title>
                      <v-list-tile-sub-title v-if="server.pings.length > 0">
                        Last ping {{server.pings[0].created_at}}
                      </v-list-tile-sub-title>
                    </v-list-tile-content>
                  </v-list-tile>
                  <v-divider v-if="serverIndex+1 < group.servers.length"></v-divider>
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
          <template v-if="selectedServer===null"><v-icon small class="error--text mr-2">arrow_back</v-icon> Please select server</template>
          <template v-else>
            <template v-if="selectedServer.name">
              {{selectedServer.name}} ({{selectedServer.ip}})
            </template>
            <template v-else-if="selectedServer.ip">
              {{selectedServer.ip}}
            </template>
            <v-spacer></v-spacer>
            <v-btn
                    color="success"
                    small
                    @click="ping"
                    :loading="pingLoading"
                    :disabled="pingLoading"
            >PING</v-btn>
          </template>
        </v-card-title>
        <v-card-text class="server-list-card" v-if="selectedServer!==null">
          <div v-for="ping in selectedServer.pings">
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
            <v-text-field id="edited-group-name" label="Group title *" v-model="editedGroup.name"></v-text-field>
          </v-flex>
          <v-flex xs12 sm12 md12>
            <v-spacer></v-spacer>
            <v-btn color="accent" :disabled="editedGroup.name===null" small @click="cancelEditGroup">Cancel</v-btn>
            <v-btn color="success" :disabled="editedGroup.name===null" small @click="saveGroup">Save</v-btn>
          </v-flex>
          <div class="caption pt-4 form-caption">* required parameters</div>
        </v-card-text>
      </v-card>
    </v-flex>
    <v-flex class="column" xs12 md12 sm12>
      <v-card>
        <v-card-title>
          <template v-if="editedServer.id===null">Create server</template>
          <template v-else>Edit server &nbsp;<span class="accent--text font-weight-bold">{{editedServerName}}</span></template>
        </v-card-title>
        <v-card-text>
          <v-flex xs12 sm12 md12>
            <v-select
                    v-if="groups.length > 0"
                    label="Server group *"
                    :items="groups"
                    item-text="name"
                    item-value="id"
                    v-model="editedServer.group_id"
            ></v-select>
          </v-flex>
          <v-flex xs12 sm12 md12>
            <v-text-field id="server-title" label="Server title" v-model="editedServer.name"></v-text-field>
          </v-flex>
          <v-flex xs12 sm12 md12>
            <v-text-field label="Host name / IP *" v-model="editedServer.ip"></v-text-field>
          </v-flex>
          <v-flex xs12 sm12 md12>
            <v-spacer></v-spacer>
            <v-btn color="accent" :disabled="editedServer.name===null && editedServer.ip===null && editedServer.group_id===null" small @click="cancelEditServer">Cancel</v-btn>
            <v-btn color="success" :disabled="editedServer.ip===null || editedServer.group_id===null" small @click="saveServer">Save</v-btn>
          </v-flex>
          <div class="caption pt-4 form-caption">* required parameters</div>
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
    selectedServerParams: {
      groupIndex: null,
      serverIndex: null,
    },
    defaultEditedGroup: {
      id: null,
      name: null,
    },
    editedGroup: {
      id: null,
      name: null,
    },
    editedGroupName: null,
    defaultEditedServer: {
      id: null,
      name: null,
      ip: null,
      group_id: null,
    },
    editedServer: {
      id: null,
      name: null,
      ip: null,
      group_id: null,
    },
    editedServerName: null,
    pingLoading: false,
  }),
  methods: {
    selectServer(groupIndex, serverIndex) {
      this.selectedServerParams = {
        groupIndex: groupIndex,
        serverIndex: serverIndex,
      };
    },
    editGroup(group) {
      this.editedGroup = {id: group.id, name: group.name};
      this.editedGroupName = group.name;
      this.$el.querySelector('#edited-group-name').focus();
    },
    cancelEditGroup() {
      this.editedGroup = {...this.defaultEditedGroup};
      this.editedGroupName = null;
    },
    saveGroup() {
      let url = this.editedGroup.id === null ? "/group" : "/group/" + this.editedGroup.id;

      let params = new URLSearchParams();
      Object.keys(this.editedGroup).forEach((k) => {
        this.editedGroup[k] !== null && params.append(k, this.editedGroup[k]);
      });

      this.axios({
        method: 'post',
        url: url,
        data: params,
      }).then(() => {
        this.refreshData();
        this.cancelEditGroup();
      });
    },
    deleteGroup(group) {
      this.axios.delete("/group/" + group.id).then(() => {
        this.refreshData();
      });
    },
    editServer(server) {
      this.editedServer = {id: server.id, name: server.name, ip: server.ip, group_id: server.group_id};
      this.editedServerName = server.name ? server.name : server.ip;
      this.$el.querySelector('#server-title').focus();
    },
    cancelEditServer() {
      this.editedServer = {...this.defaultEditedServer};
      this.editedServerName = null;
    },
    saveServer() {
      let url = this.editedServer.id === null ? "/server" : "/server/" + this.editedServer.id;

      let params = new URLSearchParams();
      Object.keys(this.editedServer).forEach((k) => {
        this.editedServer[k] !== null && params.append(k, this.editedServer[k]);
      });

      this.axios({
        method: 'post',
        url: url,
        data: params,
      }).then(() => {
        this.refreshData();
        this.cancelEditServer();
      });
    },
    deleteServer(server) {
      this.axios.delete("/server/" + server.id).then(() => {
        this.refreshData();
        this.selectedServerParams = {
          groupIndex: null,
          serverIndex: null,
        };
      });
    },
    createServerForGroup(group) {
      this.cancelEditServer();
      this.editedServer.group_id = group.id;
      this.$el.querySelector('#server-title').focus();
    },
    ping() {
      this.pingLoading = true;
      this.axios.post("/dashboard/ping/" + this.selectedServer.id).then(() => {
        this.refreshData();
        this.pingLoading = false;
      });
    },
    refreshData() {
      this.axios.get("/dashboard").then((data) => {
        this.groups = data.data;
      });
    },
  },
  beforeMount() {
    this.refreshData();
  },
  computed: {
    selectedServer() {
      if (this.selectedServerParams.groupIndex !== null &&
              this.selectedServerParams.serverIndex !== null &&
              this.groups[this.selectedServerParams.groupIndex] &&
              this.groups[this.selectedServerParams.groupIndex]["servers"][this.selectedServerParams.serverIndex]) {
        return this.groups[this.selectedServerParams.groupIndex]["servers"][this.selectedServerParams.serverIndex];
      }

      return null;
    },
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
  .server-list-card .v-list {
    padding-bottom: 0;
  }
  .ping-list-info {
    font-size: 18px;
    margin-right: 10px;
  }
  .server-list-info-right {
    float: right;
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
  .v-card, .v-expansion-panel__container, .v-expansion-panel__body {
    background-color: #263238!important;
  }
  .v-list {
    background-color: #2c3a41!important;
  }
  .server-group-dashboard .v-list__tile {
      height: 20px;
      padding-bottom: 8px;
  }
  .form-caption {
    text-align: right;
  }
</style>
