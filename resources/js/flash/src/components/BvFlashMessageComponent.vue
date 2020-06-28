<template>
    <b-alert
        :show="show"
        :dismissible="dismissible"
        :variant="type"
        @dismissed="dismissCountDown=0"
    >
        {{msg}}
    </b-alert>
</template>

<script>
    export default {
        props: {
            dismissible: {
                type: Boolean,
                required: false,
                default:  _ => {
                    return false;
                }
            },
            dismissSecs: {
                type: Number,
                required: false,
                default: _ => {
                    return 0;
                }
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
            this.$flash.on('show', this.show);
            this.$flash.on('hide', this.hide);
        },
        beforeDestroy() {
            this.$flash.$off('show');
            this.$flash.$off('hide');
        },
        methods: {
            show: function(msg, type) {
                this.show = this.dismissible && this.dismissSecs > 0 ? this.dismissSecs : true;
                this.dismissCountDown = this.dismissSecs;
                this.msg = msg;
                this.type = type;
            },
            hide: function() {
                this.show = false;
                this.dismissCountDown = 0;
                this.msg = null;
                this.type = null;
            }
        },
    };
</script>
