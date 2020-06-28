<template>
    <b-form @submit.prevent="onSubmit">
        <b-row>
            <b-col>
                <b-form-group
                    id="title"
                    label="Title"
                    label-for="title"
                >
                    <b-form-input
                        id="title"
                        v-model="form.title"
                        :required="isRequiredField('title')"
                        :state="validateState('title')"
                    ></b-form-input>
                    <b-form-invalid-feedback id="title">
                        {{ this.errors['title'] }}
                    </b-form-invalid-feedback>
                </b-form-group>

                <b-form-group
                    id="slug"
                    label="Slug"
                    label-for="slug"
                >
                    <b-form-input
                        id="slug"
                        v-model="form.slug"
                        :required="isRequiredField('slug')"
                        :state="validateState('slug')"
                    ></b-form-input>
                    <b-form-invalid-feedback id="slug">
                        {{ this.errors['slug'] }}
                    </b-form-invalid-feedback>
                </b-form-group>

                <b-form-group
                    id="homepage"
                    label="Homepage"
                    label-for="homepage"
                >
                    <b-form-input
                        id="homepage"
                        v-model="form.homepage"
                        :required="isRequiredField('homepage')"
                        :state="validateState('homepage')"
                    ></b-form-input>
                    <b-form-invalid-feedback id="homepage">
                        {{ this.errors['homepage'] }}
                    </b-form-invalid-feedback>
                </b-form-group>

                <b-form-group
                    id="description"
                    label="Description"
                    label-for="description"
                >
                    <b-form-textarea
                        id="description"
                        v-model="form.description"
                        :required="isRequiredField('description')"
                        :state="validateState('description')"
                    ></b-form-textarea>
                    <b-form-invalid-feedback id="description">
                        {{ this.errors['description'] }}
                    </b-form-invalid-feedback>
                </b-form-group>
            </b-col>
            <b-col>
                <b-form-group
                    id="abstract"
                    label="Abstract"
                    label-for="abstract"
                >
                    <b-form-input
                        id="abstract"
                        v-model="form.abstract"
                        :required="isRequiredField('abstract')"
                        :state="validateState('abstract')"
                    ></b-form-input>
                    <b-form-invalid-feedback id="abstract">
                        {{ this.errors['abstract'] }}
                    </b-form-invalid-feedback>
                </b-form-group>

                <b-form-group
                    id="author"
                    label="Author"
                    label-for="author"
                >
                    <b-form-input
                        id="author"
                        v-model="form.author"
                        :required="isRequiredField('author')"
                        :state="validateState('author')"
                    ></b-form-input>
                    <b-form-invalid-feedback id="author">
                        {{ this.errors['author'] }}
                    </b-form-invalid-feedback>
                </b-form-group>

                <b-form-group
                    id="author_homepage"
                    label="Author Homepage"
                    label-for="author_homepage"
                >
                    <b-form-input
                        id="author_homepage"
                        v-model="form.author_homepage"
                        :required="isRequiredField('author_homepage')"
                        :state="validateState('author_homepage')"
                    ></b-form-input>
                    <b-form-invalid-feedback id="author_homepage">
                        {{ this.errors['author_homepage'] }}
                    </b-form-invalid-feedback>
                </b-form-group>

                <b-form-group>
                    <b-form-checkbox
                        id="active"
                        v-model="form.active"
                        :required="isRequiredField('active')"
                        :state="validateState('active')"
                        value="1"
                        unchecked-value="0"
                    >
                        Active
                    </b-form-checkbox>
                    <b-form-invalid-feedback id="active">
                        {{ this.errors['active'] }}
                    </b-form-invalid-feedback>
                </b-form-group>

                <b-form-group>
                    <b-form-checkbox
                        id="live"
                        v-model="form.live"
                        :required="isRequiredField('live')"
                        :state="validateState('live')"
                        value="1"
                        unchecked-value="0"
                    >
                        Live
                    </b-form-checkbox>
                    <b-form-invalid-feedback id="live">
                        {{ this.errors['live'] }}
                    </b-form-invalid-feedback>
                </b-form-group>
            </b-col>
        </b-row>
        <hr/>
        <b-row>
            <b-col>
                <b-form-group
                    id="type"
                    label="Type"
                    label-for="type"
                >
                    <b-form-select
                        id="type"
                        v-model="form.type"
                        :required="isRequiredField('type')"
                        :options="comicTypes"
                        :state="validateState('type')"
                    ></b-form-select>
                    <b-form-invalid-feedback id="type">
                        {{ this.errors['type'] }}
                    </b-form-invalid-feedback>
                </b-form-group>

                <b-form-group
                    id="scraper"
                    label="Scraper"
                    label-for="scraper"
                >
                    <b-form-select
                        id="scraper"
                        v-model="form.scraper"
                        :required="isRequiredField('scraper')"
                        :options="comicScrapers"
                        :state="validateState('scraper')"
                    ></b-form-select>
                    <b-form-invalid-feedback id="scraper">
                        {{ this.errors['scraper'] }}
                    </b-form-invalid-feedback>
                </b-form-group>

                <b-form-group
                    id="scrape_url"
                    label="Scrape URL"
                    label-for="scrape_url"
                >
                    <b-form-input
                        id="scrape_url"
                        v-model="form.scrape_url"
                        :required="isRequiredField('scrape_url')"
                        :state="validateState('scrape_url')"
                    ></b-form-input>
                    <b-form-invalid-feedback id="scrape_url">
                        {{ this.errors['scrape_url'] }}
                    </b-form-invalid-feedback>
                </b-form-group>

                <b-form-group
                    id="base_url"
                    label="Base URL"
                    label-for="base_url"
                >
                    <b-form-input
                        id="base_url"
                        v-model="form.base_url"
                        :required="isRequiredField('base_url')"
                        :state="validateState('base_url')"
                    ></b-form-input>
                    <b-form-invalid-feedback id="base_url">
                        {{ this.errors['base_url'] }}
                    </b-form-invalid-feedback>
                </b-form-group>

                <b-form-group
                    id="image_dom_path"
                    label="Image DOM Path"
                    label-for="image_dom_path"
                >
                    <b-form-input
                        id="image_dom_path"
                        v-model="form.image_dom_path"
                        :required="isRequiredField('image_dom_path')"
                        :state="validateState('image_dom_path')"
                    ></b-form-input>
                    <b-form-invalid-feedback id="image_dom_path">
                        {{ this.errors['image_dom_path'] }}
                    </b-form-invalid-feedback>
                </b-form-group>
            </b-col>
            <b-col>
                <b-form-group
                    id="index_format"
                    label="Index Format"
                    label-for="index_format"
                >
                    <b-form-input
                        id="index_format"
                        v-model="form.index_format"
                        :required="isRequiredField('index_format')"
                        :state="validateState('index_format')"
                    ></b-form-input>
                    <b-form-invalid-feedback id="index_format">
                        {{ this.errors['index_format'] }}
                    </b-form-invalid-feedback>
                </b-form-group>

                <b-form-group
                    id="current_index"
                    label="Current Index"
                    label-for="current_index"
                >
                    <b-form-input
                        id="current_index"
                        v-model="form.current_index"
                        :required="isRequiredField('current_index')"
                        :state="validateState('current_index')"
                    ></b-form-input>
                    <b-form-invalid-feedback id="current_index">
                        {{ this.errors['current_index'] }}
                    </b-form-invalid-feedback>
                </b-form-group>

                <b-form-group>
                    <label for="scraper_user_agent">Scraper User Agent</label>
                    <p class="form-text text-muted">Selecting a user agent from the dropdown will pre-fill the scraper's user agent field with that option. You can also just type one in manually.</p>
                    <b-row>
                        <b-col sm="15">
                            <b-form-select
                                v-model="scraperUserAgentPrefill"
                                :options="comicScraperUserAgents"
                            >
                                <template v-slot:first>
                                    <b-select-option value="">Choose a Pre-fill</b-select-option>
                                </template>
                            </b-form-select>
                        </b-col>
                        <b-col sm="33">
                            <b-form-input
                                id="scraper_user_agent"
                                v-model="form.scraper_user_agent"
                                :required="isRequiredField('scraper_user_agent')"
                                :state="validateState('scraper_user_agent')"
                            ></b-form-input>
                        </b-col>
                    </b-row>
                    <b-form-invalid-feedback id="scraper_user_agent">
                        {{ this.errors['scraper_user_agent'] }}
                    </b-form-invalid-feedback>
                </b-form-group>

                <b-form-group>
                    <b-form-checkbox
                        id="classic_edition"
                        v-model="form.classic_edition"
                        :required="isRequiredField('classic_edition')"
                        :state="validateState('classic_edition')"
                        value="1"
                        unchecked-value="0"
                    >
                        Classic Edition
                    </b-form-checkbox>
                    <b-form-invalid-feedback id="classic_edition">
                        {{ this.errors['classic_edition'] }}
                    </b-form-invalid-feedback>
                </b-form-group>
            </b-col>
        </b-row>
        <hr/>
        <b-row>
            <b-col>
                <b-form-group
                    id="nav_url_regex"
                    label="Nav URL Regex"
                    label-for="nav_url_regex"
                >
                    <b-form-input
                        id="nav_url_regex"
                        v-model="form.nav_url_regex"
                        :required="isRequiredField('nav_url_regex')"
                        :state="validateState('nav_url_regex')"
                    ></b-form-input>
                    <b-form-invalid-feedback id="nav_url_regex">
                        {{ this.errors['nav_url_regex'] }}
                    </b-form-invalid-feedback>
                </b-form-group>

                <b-form-group
                    id="nav_previous_dom_path"
                    label="Nav Previous DOM Path"
                    label-for="nav_previous_dom_path"
                >
                    <b-form-input
                        id="nav_previous_dom_path"
                        v-model="form.nav_previous_dom_path"
                        :required="isRequiredField('nav_previous_dom_path')"
                        :state="validateState('nav_previous_dom_path')"
                    ></b-form-input>
                    <b-form-invalid-feedback id="nav_previous_dom_path">
                        {{ this.errors['nav_previous_dom_path'] }}
                    </b-form-invalid-feedback>
                </b-form-group>

                <b-form-group
                    id="nav_next_dom_path"
                    label="Nav Next DOM Path"
                    label-for="nav_next_dom_path"
                >
                    <b-form-input
                        id="nav_next_dom_path"
                        v-model="form.nav_next_dom_path"
                        :required="isRequiredField('nav_next_dom_path')"
                        :state="validateState('nav_next_dom_path')"
                    ></b-form-input>
                    <b-form-invalid-feedback id="nav_next_dom_path">
                        {{ this.errors['nav_next_dom_path'] }}
                    </b-form-invalid-feedback>
                </b-form-group>

                <b-form-group
                    id="nav_page_number_dom_path"
                    label="Nav Page Number DOM Path"
                    label-for="nav_page_number_dom_path"
                >
                    <b-form-input
                        id="nav_page_number_dom_path"
                        v-model="form.nav_page_number_dom_path"
                        :required="isRequiredField('nav_page_number_dom_path')"
                        :state="validateState('nav_page_number_dom_path')"
                    ></b-form-input>
                    <b-form-invalid-feedback id="nav_page_number_dom_path">
                        {{ this.errors['nav_page_number_dom_path'] }}
                    </b-form-invalid-feedback>
                </b-form-group>
            </b-col>
            <b-col>
                <b-form-group
                    id="first_index"
                    label="First Index"
                    label-for="first_index"
                >
                    <b-form-input
                        id="first_index"
                        v-model="form.first_index"
                        :required="isRequiredField('first_index')"
                        :state="validateState('first_index')"
                    ></b-form-input>
                    <b-form-invalid-feedback id="first_index">
                        {{ this.errors['first_index'] }}
                    </b-form-invalid-feedback>
                </b-form-group>

                <b-form-group
                    id="last_index"
                    label="Last Index"
                    label-for="last_index"
                >
                    <b-form-input
                        id="last_index"
                        v-model="form.last_index"
                        :required="isRequiredField('last_index')"
                        :state="validateState('last_index')"
                    ></b-form-input>
                    <b-form-invalid-feedback id="last_index">
                        {{ this.errors['last_index'] }}
                    </b-form-invalid-feedback>
                </b-form-group>

                <b-form-group
                    id="index_step"
                    label="Index Step"
                    label-for="index_step"
                >
                    <b-form-input
                        id="index_step"
                        v-model="form.index_step"
                        :required="isRequiredField('index_step')"
                        :state="validateState('index_step')"
                    ></b-form-input>
                    <b-form-invalid-feedback id="index_step">
                        {{ this.errors['index_step'] }}
                    </b-form-invalid-feedback>
                </b-form-group>
            </b-col>
        </b-row>
        <b-row>
            <b-col class="mt-3">
                <slot name="submit" v-bind:isBusy="isBusy"></slot>
            </b-col>
        </b-row>
    </b-form>
</template>
<script>
    export default {
        props: {
            action: {
                type: String,
                required: false,
                default: _ => {
                    // Dunno
                    return '';
                }
            },
            actionType: {
                type: String,
                required: false,
                default: _ => {
                    return 'POST';
                }
            },
            requiredFields: {
                type: Array,
                required: false,
                default: _ => {
                    return [
                        'title',
                        'type',
                        'scraper',
                        'scrape_url',
                        'image_dom_path',
                        'current_index'
                    ];
                }
            },
            comicTypes: {
                type: Array,
                required: true,
                default: _ => {
                    return [];
                }
            },
            comicScrapers: {
                type: Array,
                required: true,
                default: _ => {
                    return [];
                }
            },
            comicScraperUserAgents: {
                type: Array,
                required: true,
                default: _ => {
                    return [];
                },
            },
            form: {
                type: Object,
                required: false,
                default: _ => {
                    return {
                        type: 2,
                        scraper: 'BaseScraper',
                        scraper_user_agent: 'Googlebot/2.1 (http://www.googlebot.com/bot.html)',
                        active: true,
                        live: true,
                    };
                },
            },
        },
        mounted() {
        },
        data() {
            return {
                scraperUserAgentPrefill: '',
                errors: {},
                isBusy: false,
            };
        },
        methods: {
            onSubmit: function() {
                this.isBusy = true;

                let request = null;
                if (this.actionType === 'PUT') {
                    request = axios.put(this.action, this.form);
                } else if (this.actionType === 'POST') {
                    request = axios.post(this.action, this.form);
                }

                if (!request) {
                    return;
                }

                request.then(response => {
                    if (response.data.success) {
                        this.form.data = response.data.data;
                        window.location.href = response.data.redirect_to;
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
                    if (this.errors[name].trim().length > 0) {
                        return false;
                    }
                    return true;
                }
                return null;
            },
            isRequiredField(field) {
                return this.requiredFields.indexOf(field) >= 0;
            }
        },
        computed: {
            isNewRecord: function() {
                return typeof this.form._id === 'undefined';
            }
        },
        watch: {
            scraperUserAgentPrefill: function() {
                this.form.scraper_user_agent = this.scraperUserAgentPrefill;
            }
        },
    }
</script>
