<template>
    <admin-table-component
        :slots="slots"
        :id="id"
        :primary-key="primaryKey"
        :api-url="apiUrl"
        :filter-on="filterOn"
        :sort-by="sortBy"
        :sort-desc="sortDesc"
        :page="page"
        :per-page="perPage"
        :fields="fields"
    >
        <template #email="props">
            {{ props.row.item.email }}
            <template v-if="props.row.item.has_verified_email">
                <span class="text-success pl-2">Verified: {{ props.row.item.email_verified_at }}</span>
            </template>
            <template v-else>
                <span class="text-danger pl-2">Not Verified</span>
            </template>
        </template>
    </admin-table-component>
</template>

<script>
    import AdminTableComponent from "./AdminTableComponent";
    export default {
        components: {AdminTableComponent},
        data() {
            return {
                slots: [
                    {
                        name: 'email',
                        field: 'email'
                    }
                ],
                id: 'users-admin-table',
                primaryKey: '_id',

                apiUrl: '/admin/user/admin-table-data',
                apiData: {},

                filterOn: ['username'],

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
                        key: 'username',
                        label: 'Username',
                        sortable: true,
                        filterable: true
                    },
                    {
                        key: 'email',
                        label: 'Email',
                        sortable: true,
                        filterable: true
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
