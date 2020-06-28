<template>
    <b-form method="post" :action="action">
        <input type="hidden" name="_method" :value="method"/>
        <input type="hidden" name="_token" :value="token"/>
        <input
            type="hidden"
            name="comic_id"
            id="comic_id"
            v-model="form.comic_id"
        />

        <div class="form-group">
            <label
                for="comic"
            >
                Comic
            </label>
            <b-link
                class="d-block"
               :href="comicUrl"
            >
                {{ comicTitle }}
            </b-link>
        </div>

        <b-form-group
            id="url"
            label="Url"
            label-for="url"
        >
            <b-form-input
                id="url"
                name="url"
                v-model="form.url"
                :required="isRequiredField('url')"
                :state="validateState('url')"
            ></b-form-input>
            <b-form-invalid-feedback id="url">
                {{ getFieldErrorMessage('url') }}
            </b-form-invalid-feedback>
        </b-form-group>

        <b-form-group
            id="date"
            label="Date"
            label-for="date"
        >
            <b-form-input
                id="date"
                name="date"
                v-model="form.date"
                :required="isRequiredField('date')"
                :state="validateState('date')"
            ></b-form-input>
            <b-form-invalid-feedback id="date">
                {{ getFieldErrorMessage('date') }}
            </b-form-invalid-feedback>
        </b-form-group>

        <b-form-group
            id="index"
            label="Index"
            label-for="index"
        >
            <b-form-input
                id="index"
                name="index"
                v-model="form.index"
                :required="isRequiredField('index')"
                :state="validateState('index')"
            ></b-form-input>
            <b-form-invalid-feedback id="index">
                {{ getFieldErrorMessage('index') }}
            </b-form-invalid-feedback>
        </b-form-group>

        <b-form-group>
            <b-form-checkbox
                id="skip"
                name="skip"
                v-model="form.skip"
                :required="isRequiredField('skip')"
                :state="validateState('skip')"
                value="1"
                unchecked-value="0"
            >
                Do not download this strip
            </b-form-checkbox>
            <b-form-invalid-feedback id="skip">
                {{ getFieldErrorMessage('skip') }}
            </b-form-invalid-feedback>
        </b-form-group>

        <b-form-group
            id="previous"
            label="Previous"
            label-for="previous"
        >
            <b-form-input
                id="previous"
                name="previous"
                v-model="form.previous"
                :required="isRequiredField('previous')"
                :state="validateState('previous')"
            ></b-form-input>
            <b-form-invalid-feedback id="previous">
                {{ getFieldErrorMessage('previous') }}
            </b-form-invalid-feedback>
        </b-form-group>

        <b-form-group
            id="next"
            label="Next"
            label-for="next"
        >
            <b-form-input
                id="next"
                name="next"
                v-model="form.next"
                :required="isRequiredField('next')"
                :state="validateState('next')"
            ></b-form-input>
            <b-form-invalid-feedback id="next">
                {{ getFieldErrorMessage('next') }}
            </b-form-invalid-feedback>
        </b-form-group>

        <div class="py-4" v-if="imageSrc && !isNewRecord">
            <img class="img-fluid" :src="imageSrc"/>
        </div>

        <div class="form-group mt-3">
            <b-button
                type="submit"
                variant="outline-success"
                size="lg"
            >
                <template v-if="isNewRecord">
                    Create Comic Strip
                </template>
                <template v-else>
                    Save Comic Strip
                </template>
            </b-button>
            <b-link
                :href="refreshUrl"
                class="btn btn-lg btn-outline-secondary ml-2"
            >
                Refresh
            </b-link>
        </div>
    </b-form>
</template>

<script>
    export default {
        props: {
            method: {
                type: String,
                required: false,
                default: _ => {
                    return 'POST';
                },
            },
            action: {
                type: String,
                required: true,
                default: _ => {
                    return '';
                },
            },
            requiredFields: {
                type: Array,
                required: false,
                default: _ => {
                    return [
                        'index',
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
            errors: {
                type: Object,
                required: false,
                default: _ => {
                    return {};
                },
            },
            imageSrc: {
                type: String,
                required: false,
                default: _ => {
                    return '';
                },
            },
            refreshUrl: {
                type: String,
                required: false,
                default: _ => {
                    return '';
                },
            },
            comicUrl: {
                type: String,
                required: true,
                default: _ => {
                    return '';
                },
            },
            comicTitle: {
                type: String,
                required: true,
                default: _ => {
                    return '';
                },
            },
        },
        mounted() {
            this.token = document.head.querySelector('meta[name="csrf-token"]').content;
        },
        data() {
            return {
                token: null,
            };
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
            isRequiredField(field) {
                return this.requiredFields.indexOf(field) >= 0;
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
            },
        },
    };
</script>
