<template>
    <admin-table-component
        :id="id"
        :primary-key="primaryKey"
        :api-url="apiUrl"
        :filter-on="filterOn"
        :sort-by="sortBy"
        :sort-desc="sortDesc"
        :page="page"
        :per-page="perPage"
        :fields="fields"
    ></admin-table-component>
</template>

<script>
    import AdminTableComponent from "./AdminTableComponent";
    export default {
        components: {AdminTableComponent},
        data() {
            return {
                id: 'logs-admin-table',
                primaryKey: '_id',

                apiUrl: '/admin/comic/logs-admin-table-data',
                apiData: {},

                filterOn: ['message'],

                sortBy: '_id',
                sortDesc: true,
                page: 1,
                perPage: 20,
                fields: [
                    {
                        key: '_id',
                        label: '#',
                        sortable: true,
                        filterable: true
                    },
                    {
                        key: 'message',
                        label: 'Message',
                        sortable: true,
                        filterable: true
                    },
                    {
                        key: 'trace',
                        label: 'Trace',
                        sortable: true,
                        filterable: true
                    },
                    {
                        key: 'created_at',
                        label: 'Created At',
                        sortable: true
                    },
                ],
            }
        },
        mounted() {
        },
    }
</script>
