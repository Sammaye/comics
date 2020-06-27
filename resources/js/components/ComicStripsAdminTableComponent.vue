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
        props: {
            comicId: {
                type: String,
                required: true,
                default: _ => {
                    return '';
                },
            }
        },
        data() {
            return {
                id: 'comic-strips-admin-table',
                primaryKey: '_id',

                apiUrl: '/admin/comic/strips-admin-table-data/' + this.comicId,
                apiData: {
                },

                filterOn: ['url'],

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
                        key: 'url',
                        label: 'Url',
                        sortable: true,
                        filterable: true
                    },
                    {
                        key: 'image_url',
                        label: 'Image Url',
                        sortable: true,
                        filterable: true
                    },
                    {
                        key: 'image_md5',
                        label: 'image_md5',
                        sortable: true
                    },
                    {
                        key: 'index',
                        label: 'index',
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
