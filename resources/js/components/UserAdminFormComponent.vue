<template>
    <b-form @submit.prevent="onSubmit" class="row justify-content-center">
        <b-col sm="24">
            <b-form-group
                id="username"
                label="Username"
                label-for="username"
            >
                <b-form-input
                    id="username"
                    v-model="form.username"
                    :required="isRequiredField('username')"
                    :state="validateState('username')"
                ></b-form-input>
                <b-form-invalid-feedback id="username">
                    {{ getFieldErrorMessage('username') }}
                </b-form-invalid-feedback>
            </b-form-group>

            <b-form-group
                id="email"
                label="E-Mail Address"
                label-for="email"
            >
                <b-form-input
                    id="email"
                    v-model="form.email"
                    :required="isRequiredField('email')"
                    :state="validateState('email')"
                ></b-form-input>
                <b-form-invalid-feedback id="email">
                    {{ getFieldErrorMessage('email') }}
                </b-form-invalid-feedback>
            </b-form-group>

            <b-form-group
                id="password"
                label="Password"
                label-for="password"
            >
                <b-form-input
                    type="password"
                    id="password"
                    v-model="form.password"
                    :required="isRequiredField('password')"
                    :state="validateState('password')"
                ></b-form-input>
                <b-form-invalid-feedback id="password">
                    {{ getFieldErrorMessage('password') }}
                </b-form-invalid-feedback>
            </b-form-group>

            <b-form-group>
                <b-button
                    type="submit"
                    variant="outline-success"
                    size="lg"
                    block
                    name="action"
                    value="save"
                >
                    Create User
                </b-button>
            </b-form-group>
            <pre>{{form}}</pre>
        </b-col>
    </b-form>
</template>

<script>
    export default {
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
                    return 'POST';
                }
            },
            requiredOnCreate: {
                type: Array,
                required: false,
                default: _ => {
                    return [
                        'username',
                        'email',
                        'password',
                    ];
                }
            },
            requiredOnUpdate: {
                type: Array,
                required: false,
                default: _ => {
                    return [
                        'username',
                        'email',
                    ];
                }
            },
            form: {
                type: Object,
                required: false,
                default: _ => {
                    return {};
                },
            },
        },
        data() {
            return {
                errors: {},
                isBusy: false,
            };
        },
        mounted() {
        },
        methods: {
            onSubmit: function() {
                this.isBusy = true;

                let request = null;
                if (this.method === 'PUT') {
                    request = axios.put(this.action, this.form);
                } else if (this.method === 'POST') {
                    request = axios.post(this.action, this.form);
                }

                if (!request) {
                    return;
                }

                request.then(response => {
                    if (response.data.success) {
                        this.form = response.data.data;
                        this.$root.$emit('bv-flash::show', 'User ' + this.form._id + ' Created', 'success');
                    } else {
                        window.scrollTo(0, 0);
                        this.errors = response.data.errors;
                    }
                }).catch(response => {
                    this.isBusy = false;
                });
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
            isRequiredField(field) {
                if (this.isNewRecord) {
                    return this.requiredOnCreate.indexOf(field) >= 0;
                }
                return this.requiredOnUpdate.indexOf(field) >= 0;
            },
            getFieldErrorMessage(field) {
                let errors = this.errors[field];
                if (Array.isArray(errors)) {
                    return this.errors[field][0];
                }
                return this.errors[field];
            }
        },
        computed: {
            isNewRecord: function() {
                return typeof this.form._id === 'undefined';
            }
        },
    }
</script>
