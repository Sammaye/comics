<template>
    <div>
        <b-button
            v-if="isSubscribed"
            @click="unsubscribe()"
            variant="outline-danger"
            size="lg"
        >
            <template v-if="isBusy">
                <b-spinner variant="danger" type="grow" label="Spinning"></b-spinner>
            </template>
            <template v-else>
                <span class="fas fa-times ml-2"></span>
                Unsubscribe
            </template>
        </b-button>
        <b-button
            v-else
            @click="subscribe()"
            variant="outline-success"
            size="lg"
        >
            <template v-if="isBusy">
                <b-spinner variant="danger" type="grow" label="Spinning"></b-spinner>
            </template>
            <template v-else>
                <span class="fas fa-check ml-2"></span>
                Subscribe
            </template>
        </b-button>
    </div>
</template>

<script>
    export default {
        props: {
            subscribeUrl: {
                type: String,
                required: true,
                default: _ => {
                    return '';
                },
            },
            unsubscribeUrl: {
                type: String,
                required: true,
                default:  _ => {
                    return '';
                },
            },
            hasSubscription: {
                type: Boolean,
                required: true,
                default: _ => {
                    return false;
                },
            },
        },
        data() {
            return {
                isSubscribed: this.hasSubscription,
                isBusy: false,
            };
        },
        mounted() {
        },
        methods: {
            subscribe: function() {
                this._call(this.subscribeUrl, true);
            },
            unsubscribe: function() {
                this._call(this.unsubscribeUrl, false);
            },
            _call: function(url, subscribe) {
                axios.post(url).then(response => {
                    this.isSubscribed = subscribe;
                }).catch(_ => {
                    // Empty
                }).then(response => {
                    this.isBusy = false;
                });
            }
        },
    }
</script>
