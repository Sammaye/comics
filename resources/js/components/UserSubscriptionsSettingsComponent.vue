<template>
    <b-form class="mb-4" :action="action" method="post">
        <input type="hidden" name="_method" :value="method"/>
        <input type="hidden" name="_token" :value="token"/>

        <h2 class="mb-3">Subscriptions</h2>
        <b-row>
            <b-col sm="24" v-if="comicSubscriptions.length > 0">
                <b-form-group>
                <p>
                    Hold down (click or touch) on each row and move around to re-order your subscriptions
                </p>
                <draggable
                    group="comics"
                    @start="drag=true"
                    @end="drag=false"
                    class="list-group"
                    tag="ul"
                    ghost-class="ghost"
                >
                    <li
                        v-for="comic in comicSubscriptions"
                        :key="comic.id"
                        class="list-group-item"
                    >
                        <span class="h5 d-block">{{comic.title}}</span>
                        <b-button
                            @click="unsubscribe"
                            variant="outline-danger"
                            class="position-absolute btn-unsubscribe"
                        >
                            Unsubscribe
                        </b-button>
                        <input type="hidden" name="comic_subs[]" :value="comic.id"/>
                    </li>
                </draggable>
                    <b-form-invalid-feedback id="email_frequency" class="mt-2 d-block">
                        <strong>{{ getFieldErrorMessage('email_frequency') }}</strong>
                    </b-form-invalid-feedback>
                </b-form-group>
            </b-col>
            <b-col sm="24" v-else>
                <b-form-group>
                    <p>
                        You are currently not subscribed to any comics, pick some and return here to be able to manage them
                    </p>
                </b-form-group>
            </b-col>
            <b-col sm="24">
                <b-form-group
                    label="Email Frequency"
                    label-for="email_frequency"
                >
                    <b-form-select
                        id="email_frequency"
                        name="email_frequency"
                        v-model="frequency"
                        :options="frequencyOptions"
                    >
                    </b-form-select>
                    <b-form-invalid-feedback id="email_frequency">
                        <strong>{{ getFieldErrorMessage('email_frequency') }}</strong>
                    </b-form-invalid-feedback>
                </b-form-group>
            </b-col>
        </b-row>
        <div class="text-center">
            <b-button
                type="submit"
                variant="outline-success"
                size="lg"
                name="action"
                value="update_subscriptions"
            >
                Save Subscriptions
            </b-button>
        </div>
    </b-form>
</template>

<style>
    .ghost {
        opacity: 0.5;
        background: #c8ebfb;
    }
</style>

<script>
    import draggable from 'vuedraggable';
    export default {
        components: {
            draggable,
        },
        props: {
            action: {
                type: String,
                required: true,
                default: _ => {
                    return '';
                },
            },
            method: {
                type: String,
                required: false,
                default: _ => {
                    return 'PUT';
                },
            },
            comicSubscriptions: {
                type: Array,
                required: false,
                default: _ => {
                    return [];
                },
            },
            emailFrequency: {
                type: String,
                required: true,
                default: _ => {
                    return '';
                },
            },
            frequencyOptions: {
                type: Object,
                required: true,
                default: _ => {
                    return {};
                },
            },
            errors: {
                type: Object,
                required: false,
                default: _ => {
                    return {};
                },
            },
        },
        data() {
            return {
                drag: false,
                token: null,
                subscriptions: this.comicSubscriptions,
                frequency: this.emailFrequency,
            };
        },
        mounted() {
            this.token = document.head.querySelector('meta[name="csrf-token"]').content;
        },
        methods: {
            validateState(name) {
                if (this.errors.hasOwnProperty(name)) {
                    if (this.errors[name].length > 0) {
                        return false;
                    }
                    return true;
                }
                return null;
            },
            getFieldErrorMessage(field) {
                let errors = this.errors[field];
                if (Array.isArray(errors)) {
                    return this.errors[field][0];
                }
                return this.errors[field];
            },
            unsubscribe: function(event) {
                // Does not work on IE11!!!!
                event.target.closest('li').remove();
            },
        },
    }
</script>
