<template>
    <div>
        <b-row class="mb-1">
            <b-col sm="24">
                <b-form-group class="mb-0">
                    <b-input-group size="sm">
                        <b-form-input
                            v-model="filter"
                            type="search"
                            id="filterInput"
                            placeholder="Type to Search"
                            debounce="1000"
                        ></b-form-input>
                        <b-input-group-append>
                            <b-button :disabled="!filter" @click="filter = ''">Clear</b-button>
                        </b-input-group-append>
                    </b-input-group>
                </b-form-group>
            </b-col>
            <b-col sm="24">
                <b-form-group class="mb-1">
                    <b-form-checkbox-group v-model="filterOn" class="mt-1">
                        <b-form-checkbox
                            v-for="field in filterable"
                            :key="field.key"
                            :value="field.key"
                        >
                            {{field.label}}
                        </b-form-checkbox>
                    </b-form-checkbox-group>
                </b-form-group>
            </b-col>
        </b-row>

        <b-table
            :id="id"
            :api-url="apiUrl"
            :items="items"
            :fields="fields"
            :primary-key="primaryKey"
            striped bordered responsive="sm"
            show-empty
            :busy="isBusy"
            :sort-by.snc="sortBy"
            :sort-desc.sync="sortDesc"
            :currentPage="currentPage"
            :perPage="perPage"
            :filter="filter"
            :filterIncludedFields="filterOn"
        >
            <template v-slot:table-busy>
                <div class="text-center my-2">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Loading...</strong>
                </div>
            </template>
            <template v-slot:[`cell(${primaryKey})`]="row">
                <b>{{row.value}}</b>
            </template>
            <template v-slot:cell(actions)="row">
                <b-link href="#" @click.prevent="editItem(row.item, row.index)">Edit</b-link>
                <b-link href="#" @click.prevent="deleteItem(row.item, row.index)">Delete</b-link>
            </template>
        </b-table>

        <b-pagination
            v-model="currentPage"
            :total-rows="totalRows"
            :per-page="perPage"
            :aria-controls="id"
        ></b-pagination>
    </div>
</template>

<script>
    export default {
        props: {
            id: {
                type: String,
                required: true
            },
            primaryKey: {
                type: String,
                required: true
            },
            apiUrl: {
                type: String,
                required: false,
                default: ''
            },
            apiData: {
                type: Object,
                required: false,
                default: _ => {
                    return {};
                }
            },
            filterOn: {
                type: Array,
                required: false,
                default: _ => {
                    return [];
                }
            },
            sortBy: {
                type: String,
                required: false,
                default: '_id'
            },
            sortDesc: {
                type: Boolean,
                required: false,
                default: false
            },
            page: {
                type: Number,
                required: false,
                default: 1
            },
            perPage: {
                type: Number,
                required: false,
                default: 20
            },
            fields: {
                type: Array,
                required: true
            }
        },
        data() {
            return {
                currentPage: this.page,
                filter: null,
                isBusy: false,
                totalRows: 0,
                items: this.getItems,
            }
        },
        mounted() {
        },
        methods: {
            getItems: function (ctx, callback) {
                let data = Object.assign({}, ctx, this.apiData);
                delete data.apiUrl;
                data.filterOn = this.filterOn;
                this.isBusy = true;

                let response = axios.post(ctx.apiUrl, data).then(response => {
                    this.isBusy = false;
                    if (this.totalRows <= 0 || this.totalRows !== parseInt(response.data.items_count)) {
                        this.totalRows = response.data.items_count;
                    }
                    return callback(response.data.success && response.data.items ? response.data.items : []);
                });

                return null;
            },
            editItem: function (item, index) {
                window.location = item.edit_url;
            },
            deleteItem: function (item, index) {
                axios.post(item.delete_url, {
                    id: item['_id'],
                }).then(response => {
                    if (response.data.success) {
                        this.$root.$emit('bv::refresh::table', this.id);
                    }
                });
            },
        },
        computed: {
            filterable: function () {
                return this.fields.filter(function (field) {
                    return field.filterable === true;
                })
            }
        },
        watch: {
            filterOn: function (val, oldVal) {
                this.$root.$emit('bv::refresh::table', this.id);
            }
        },
    }
</script>
