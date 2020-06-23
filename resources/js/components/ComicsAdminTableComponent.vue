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
                id: 'admin-comics-table',
                primaryKey: '_id',

                apiUrl: '/admin/comic/get-admin-comics-table-data',
                apiData: {},

                filterOn: ['title'],

                sortBy: '_id',
                sortDesc: true,
                page: 1,
                perPage: 2,
                fields: [
                    {
                        key: '_id',
                        label: '#',
                        sortable: true,
                        filterable: true
                    },
                    {
                        key: 'title',
                        label: 'Title',
                        sortable: true,
                        filterable: true
                    },
                    {
                        key: 'abstract',
                        label: 'Abstract',
                        sortable: true,
                        filterable: true
                    },
                    {
                        key: 'strips',
                        label: 'Strips',
                        sortable: false,
                    },
                    {
                        key: 'created_at',
                        label: 'Created At',
                        sortable: true
                    },
                    {
                        key: 'updated_at',
                        label: 'Updated At',
                        sortable: true
                    },
                    {
                        key: 'actions',
                        label: ''
                    },
                ],
            }
        },
        mounted() {
        },
    }
</script>
