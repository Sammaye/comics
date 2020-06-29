<template>
    <div class="container py-4">
        <h1 v-if="isNewRecord" class="text-center mb-4 text-truncate">Create User</h1>
        <h1 v-else class="text-center mb-4 text-truncate">Edit User #{{ form._id }}</h1>
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

                <template v-if="!isNewRecord">
                    <b-form-group
                        id="facebook_id"
                        label="Facebook ID"
                        label-for="facebook_id"
                    >
                        <b-form-input
                            id="facebook_id"
                            v-model="form.facebook_id"
                            readonly
                            :required="isRequiredField('facebook_id')"
                            :state="validateState('facebook_id')"
                        ></b-form-input>
                        <b-form-invalid-feedback id="facebook_id">
                            {{ getFieldErrorMessage('facebook_id') }}
                        </b-form-invalid-feedback>
                    </b-form-group>

                    <b-form-group
                        id="google_id"
                        label="Google ID"
                        label-for="google_id"
                    >
                        <b-form-input
                            id="google_id"
                            v-model="form.google_id"
                            readonly
                            :required="isRequiredField('google_id')"
                            :state="validateState('google_id')"
                        ></b-form-input>
                        <b-form-invalid-feedback id="google_id">
                            {{ getFieldErrorMessage('google_id') }}
                        </b-form-invalid-feedback>
                    </b-form-group>

                    <b-row>
                        <b-col sm="24">
                            <b-form-group label="Roles">
                                <b-form-checkbox-group
                                    id="role"
                                    name="role"
                                    v-model="form.role"
                                    stacked
                                >
                                    <b-form-checkbox
                                        v-for="role in form.roles"
                                        :key="role.id"
                                        :value="role.value"
                                    >
                                        {{ role.name }}
                                    </b-form-checkbox>
                                </b-form-checkbox-group>
                            </b-form-group>
                        </b-col>
                        <b-col sm="24">
                            <b-form-group label="Permissions">
                                <b-form-checkbox-group
                                    id="permission"
                                    name="permission"
                                    v-model="form.permission"
                                    stacked
                                >
                                    <b-form-checkbox
                                        v-for="permission in form.permissions"
                                        :key="permission.id"
                                        :value="permission.value"
                                    >
                                        {{ permission.name }}
                                    </b-form-checkbox>
                                </b-form-checkbox-group>
                            </b-form-group>
                        </b-col>
                    </b-row>
                </template>

                <b-form-group>
                    <b-button
                        type="submit"
                        variant="outline-success"
                        size="lg"
                        block
                        @click="setAction('save')"
                        :disabled="isBusy"
                    >
                        <template v-if="isBusy">
                            <b-spinner variant="success" type="grow" label="Spinning"></b-spinner>
                        </template>
                        <template v-else-if="isNewRecord">
                            Create User
                        </template>
                        <template v-else>
                            Save User
                        </template>
                    </b-button>
                </b-form-group>

                <template v-if="!isNewRecord">
                    <template v-if="!form.has_verified_email">
                        <b-form-group>
                            <b-button
                                type="submit"
                                variant="outline-info"
                                size="lg"
                                block
                                @click="setAction('verify_email')"
                                :disabled="isBusy"
                            >
                                <template v-if="isBusy">
                                    <b-spinner variant="info" type="grow" label="Spinning"></b-spinner>
                                </template>
                                <template v-else>
                                    Verify E-Mail Address
                                </template>
                            </b-button>
                        </b-form-group>
                    </template>

                    <template v-if="form.is_blocked">
                        <b-form-group>
                            <b-button
                                type="submit"
                                variant="outline-warning"
                                size="lg"
                                block
                                @click="setAction('unblock')"
                                :disabled="isBusy"
                            >
                                <template v-if="isBusy">
                                    <b-spinner variant="warning" type="grow" label="Spinning"></b-spinner>
                                </template>
                                <template v-else>
                                    Unblock User
                                </template>
                            </b-button>
                        </b-form-group>
                    </template>
                    <template v-else>
                        <b-form-group>
                            <b-button
                                type="submit"
                                variant="outline-warning"
                                size="lg"
                                block
                                @click="setAction('block')"
                                :disabled="isBusy"
                            >
                                <template v-if="isBusy">
                                    <b-spinner variant="warning" type="grow" label="Spinning"></b-spinner>
                                </template>
                                <template v-else>
                                    Block User
                                </template>
                            </b-button>
                        </b-form-group>
                    </template>
                    <b-form-group>
                        <b-button
                            type="button"
                            variant="outline-danger"
                            size="lg"
                            @click.prevent="submitDelete()"
                            block
                        >
                            Delete User
                        </b-button>
                    </b-form-group>
                </template>
                <pre>{{form}}</pre>
            </b-col>
        </b-form>
    </div>
</template>

<script>
    export default {
        props: {
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
                    request = axios.put(this.form.action_url, this.form);
                } else if (this.method === 'POST') {
                    request = axios.post(this.form.action_url, this.form);
                }

                if (!request) {
                    return;
                }

                request.then(response => {
                    if (response.data.success) {
                        if (this.isNewRecord) {
                            history.pushState({}, '', response.data.data.edit_url);
                        }
                        this.form = response.data.data;
                        document.title = this.form.page_title;
                        this.$root.$emit('bv-flash::show', response.data.flash, 'success');
                    } else {
                        window.scrollTo(0, 0);
                        this.errors = response.data.errors;
                    }
                }).catch(response => {
                    // error
                }).then(repsonse => {
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
            },
            setAction(value) {
                this.form.action = value;
            },
            submitDelete() {
                axios.delete(this.form.delete_url).then(response => {
                    if (response.data.success) {
                        window.location.href = response.data.redirect_to;
                    }
                });
            }
        },
        computed: {
            isNewRecord: function() {
                return typeof this.form._id === 'undefined';
            },
            method: function() {
                return this.isNewRecord ? 'POST' : 'PUT';
            }
        },
    }
</script>
