<template>
    <b-alert
        :show="show"
        :dismissible="false"
        :variant="type"
        :fade="fade"
        @dismissed="this.hideFlash"
    >
        <b-container>
            <b-row>
                <b-col>
                    {{msg}}
                    <b-button-close @click="this.hideFlash" v-if="dismissible"></b-button-close>
                </b-col>
            </b-row>
        </b-container>
    </b-alert>
</template>

<style scoped>
    .alert {
        border-radius: 0;
        border: 0;
    }
</style>

<script>
    export default {
        props: {
            dismissible: {
                type: Boolean,
                required: false,
                default:  _ => {
                    return false;
                },
            },
            dismissSecs: {
                type: Number,
                required: false,
                default: _ => {
                    return 10;
                },
            },
            fade: {
                type: Boolean,
                required: false,
                default: _ => {
                    return true;
                },
            },
        },
        data() {
            return {
                show: false,
                type: null,
                msg: null,
                dismissCountDown: 0,
            };
        },
        created() {
            this.$root.$on('bv-flash::show', this.showFlash);
            this.$root.$on('bv-flash::hide', this.hideFlash);
        },
        beforeDestroy() {
            this.$root.$off('bv-flash::show');
            this.$root.$off('bv-flash::hide');
        },
        methods: {
            showFlash: function(msg, type) {
                this.show = this.dismissSecs > 0 ? this.dismissSecs : true;
                this.dismissCountDown = this.dismissSecs;
                this.msg = msg;
                this.type = type;
            },
            hideFlash: function() {
                this.show = false;
                this.dismissCountDown = 0;
                this.msg = null;
                this.type = null;
            }
        },
    };
</script>
