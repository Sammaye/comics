<template>
    <div class="col-sm-13 text-right pb-2">
        <b-button
            v-b-modal.comic-suggestion-modal
            size="lg"
            variant="outline-secondary"
        >
            <span class="fas fa-plus"></span>
            Demand Addition
        </b-button>

        <b-modal
            id="comic-suggestion-modal"
            title="Demand a comic/cartoon to be added"
        >
            <template v-slot:default="{ok, cancel, hide}">
                <b-form @submit.prevent="submit">
                    <b-alert
                        :show="successMessage.length > 0"
                        variant="success"
                    >{{successMessage}}</b-alert>
                    <b-form-group
                        label="Name"
                        label-for="name"
                    >
                        <b-form-input
                            type="text"
                            id="name"
                            name="name"
                            v-model="form.name"
                            :state="validateState('name')"
                            @keyup.enter="submit"
                            required
                        ></b-form-input>
                        <b-form-invalid-feedback id="name">
                            <strong>{{ getFieldErrorMessage('name') }}</strong>
                        </b-form-invalid-feedback>
                    </b-form-group>
                    <b-form-group
                        label="Homepage URL"
                        label-for="url"
                    >
                        <b-form-input
                            type="text"
                            id="url"
                            name="url"
                            v-model="form.url"
                            :state="validateState('url')"
                            @keyup.enter="submit"
                            required
                        ></b-form-input>
                        <b-form-invalid-feedback id="url">
                            <strong>{{ getFieldErrorMessage('url') }}</strong>
                        </b-form-invalid-feedback>
                    </b-form-group>

                    <p class="margined-p" v-if="isLogged">
                        Since you are not logged in, add your email address here if you would like to be notified of when your comic is added
                    </p>

                    <b-form-group
                        label="E-Mail Address"
                        label-for="email"
                    >
                        <b-form-input
                            type="text"
                            id="email"
                            name="email"
                            v-model="form.email"
                            required
                            :disabled="isLogged"
                            :state="validateState('email')"
                            @keyup.enter="submit"
                        ></b-form-input>
                        <b-form-invalid-feedback id="email">
                            <strong>{{ getFieldErrorMessage('email') }}</strong>
                        </b-form-invalid-feedback>
                    </b-form-group>
                </b-form>
            </template>
            <template v-slot:modal-footer="{ok, cancel, hide}">
                <b-button
                    @click="cancel()"
                    variant="outline-secondary"
                    :disabled="isBusy"
                >
                    <template v-if="isBusy">
                        <b-spinner variant="secondary" type="grow" label="Spinning"></b-spinner>
                    </template>
                    <template v-else>
                        Cancel
                    </template>
                </b-button>
                <b-button
                    variant="success"
                    @click="submit()"
                    type="submit"
                    :disabled="isBusy"
                >
                    <template v-if="isBusy">
                        <b-spinner variant="success" type="grow" label="Spinning"></b-spinner>
                    </template>
                    <template v-else>
                        Submit Demands
                    </template>
                </b-button>
            </template>
        </b-modal>
    </div>
</template>

<script>
    export default {
        props: {
            action: {
                type: String,
                required: true,
                default: _ => {
                    return '';
                }
            },
            form: {
                type: Object,
                required: true,
                default: _ => {
                    return {};
                },
            },
            isLogged: {
                type: Boolean,
                required: false,
                default: _ => {
                    return false;
                },
            },
        },
        mounted() {},
        data() {
            return {
                errors: {},
                isBusy: false,
                successMessage: '',
            };
        },
        methods: {
            submit: function() {
                this.isBusy = true;
                axios.post(this.action, this.form).then(response => {
                    if (response.data.success) {
                        this.successMessage = response.data.message;
                        setTimeout(_ => {
                            this.$bvModal.hide('comic-suggestion-modal');

                            delete this.form.name;
                            delete this.form.url;
                            this.successMessage = '';
                        }, 3000);
                    } else {
                        this.errors = response.data.errors;
                    }
                }).catch(response => {
                    // Nuttin'
                }).then(response => {
                    this.isBusy = false;
                })
            },
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
        },
    }
</script>
