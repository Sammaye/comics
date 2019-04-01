<div class="row justify-content-around">
    <div class="col-sm-23">
        <div class="form-group">
            <label for="title">{{ __('Title') }}</label>
            <input id="title"
                   type="text"
                   class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}"
                   name="title"
                   value="{{ old('title', $model->title) }}"
                   required
            >
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('title') }}</strong>
            </span>
        </div>
        <div class="form-group">
            <label for="slug">{{ __('Slug') }}</label>
            <input id="slug"
                   type="text"
                   class="form-control{{ $errors->has('slug') ? ' is-invalid' : '' }}"
                   name="slug"
                   value="{{ old('slug', $model->slug) }}"
            >
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('slug') }}</strong>
            </span>
        </div>
        <div class="form-group">
            <label for="homepage">{{ __('Homepage') }}</label>
            <input id="homepage"
                   type="text"
                   class="form-control{{ $errors->has('homepage') ? ' is-invalid' : '' }}"
                   name="homepage"
                   value="{{ old('homepage', $model->homepage) }}"
            >
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('homepage') }}</strong>
            </span>
        </div>
        <div class="form-group">
            <label for="description">{{ __('Description') }}</label>
            <textarea id="description"
                   type="text"
                   class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}"
                   name="description"
            >
                {{ old('description', $model->description) }}
            </textarea>
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('description') }}</strong>
            </span>
        </div>
    </div>
    <div class="col-sm-23">
        <div class="form-group">
            <label for="abstract">{{ __('Abstract') }}</label>
            <input id="abstract"
                   type="text"
                   class="form-control{{ $errors->has('abstract') ? ' is-invalid' : '' }}"
                   name="abstract"
                   value="{{ old('abstract', $model->abstract) }}"
            >
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('abstract') }}</strong>
            </span>
        </div>
        <div class="form-group">
            <label for="author">{{ __('Author') }}</label>
            <input id="author"
                   type="text"
                   class="form-control{{ $errors->has('author') ? ' is-invalid' : '' }}"
                   name="author"
                   value="{{ old('author', $model->author) }}"
            >
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('author') }}</strong>
            </span>
        </div>
        <div class="form-group">
            <label for="author_homepage">{{ __('Author Homepage') }}</label>
            <input id="author_homepage"
                   type="text"
                   class="form-control{{ $errors->has('author_homepage') ? ' is-invalid' : '' }}"
                   name="author_homepage"
                   value="{{ old('author_homepage', $model->author_homepage) }}"
            >
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('author_homepage') }}</strong>
            </span>
        </div>
        <div class="form-group">
            <div class="form-check">
                <input type="hidden"
                       name="active"
                       id="active"
                       value="0"
                >
                <input class="form-check-input"
                       type="checkbox"
                       name="active"
                       id="active" {{ old('active', $model->active) ? 'checked' : '' }}
                        value="1"
                >
                <label class="form-check-label" for="active">
                    {{ __('Active') }}
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="form-check">
                <input type="hidden"
                       name="live"
                       id="live"
                       value="0"
                >
                <input class="form-check-input"
                       type="checkbox"
                       name="live"
                       id="live" {{ old('live', $model->live) ? 'checked' : '' }}
                        value="1"
                >
                <label class="form-check-label" for="live">
                    {{ __('Live') }}
                </label>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="row justify-content-around">
    <div class="col-sm-23">
        <div class="form-group">
            <label for="type">{{ __('Type') }}</label>
            <select id="type"
                   type="text"
                   class="form-control{{ $errors->has('type') ? ' is-invalid' : '' }}"
                   name="type"
                   required
            >
                @foreach($model->getTypes() as $k => $v)
                    <option value="{{ $k }}"{{ old('type', $model->type) === $k ? ' selected' : '' }}>{{ __($v) }}</option>
                @endforeach
            </select>
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('type') }}</strong>
            </span>
        </div>
        <div class="form-group">
            <label for="scraper">{{ __('Scraper') }}</label>
            <select id="scraper"
                    type="text"
                    class="form-control{{ $errors->has('scraper') ? ' is-invalid' : '' }}"
                    name="scraper"
                    required
            >
                @foreach($model->getScrapers() as $k => $v)
                    <option value="{{ $k }}"{{ old('scraper', $model->scraper) === $k ? ' selected' : '' }}>{{ $k }}</option>
                @endforeach
            </select>
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('scraper') }}</strong>
            </span>
        </div>
        <div class="form-group">
            <label for="scrape_url">{{ __('Scrape URL') }}</label>
            <input id="scrape_url"
                   type="text"
                   class="form-control{{ $errors->has('scrape_url') ? ' is-invalid' : '' }}"
                   name="scrape_url"
                   value="{{ old('scrape_url', $model->scrape_url) }}"
                   required
            >
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('scrape_url') }}</strong>
            </span>
        </div>
        <div class="form-group">
            <label for="base_url">{{ __('Base URL') }}</label>
            <input id="base_url"
                   type="text"
                   class="form-control{{ $errors->has('base_url') ? ' is-invalid' : '' }}"
                   name="base_url"
                   value="{{ old('base_url', $model->base_url) }}"
            >
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('base_url') }}</strong>
            </span>
        </div>
        <div class="form-group">
            <label for="image_dom_path">{{ __('Image DOM Path') }}</label>
            <input id="image_dom_path"
                   type="text"
                   class="form-control{{ $errors->has('image_dom_path') ? ' is-invalid' : '' }}"
                   name="image_dom_path"
                   value="{{ old('image_dom_path', $model->image_dom_path) }}"
                   required
            >
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('image_dom_path') }}</strong>
            </span>
        </div>
    </div>
    <div class="col-sm-23">
        <div class="form-group">
            <label for="index_format">{{ __('Index Format') }}</label>
            <input id="index_format"
                   type="text"
                   class="form-control{{ $errors->has('index_format') ? ' is-invalid' : '' }}"
                   name="index_format"
                   value="{{ old('index_format', $model->index_format) }}"
                   required
            >
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('index_format') }}</strong>
            </span>
        </div>
        <div class="form-group">
            <label for="current_index">{{ __('Current Index') }}</label>
            <input id="current_index"
                   type="text"
                   class="form-control{{ $errors->has('current_index') ? ' is-invalid' : '' }}"
                   name="current_index"
                   value="{{ old('current_index', $model->current_index) instanceof \Carbon\Carbon
                    ? old('current_index', $model->current_index)->format('d/m/Y')
                    : old('current_index', $model->current_index) }}"
                   required
            >
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('current_index') }}</strong>
            </span>
        </div>
        <div class="form-group form-group-scraper_user_agent">
            <label for="scraper_user_agent">{{ __('Scraper User Agent') }}</label>
            <p class="form-text text-muted">{{ __('Selecting a user agent from the dropdown will pre-fill the scraper\'s user agent field with that option. You can also just type one in manually.') }}</p>
            <div class="row">
                <div class="col-sm-15">
                    <select id="scraper_user_agent_prefill"
                            class="form-control"
                            name="scraper_user_agent_prefill"
                    >
                        <option value selected>{{ __('Choose a Pre-fill') }}</option>
                        @foreach($model->getUserAgents() as $k => $v)
                            <option value="{{ $v }}">{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-33">
                    <input id="scraper_user_agent"
                           type="text"
                           class="form-control{{ $errors->has('scraper_user_agent') ? ' is-invalid' : '' }}"
                           name="scraper_user_agent"
                           value="{{ old('scraper_user_agent', $model->scraper_user_agent) }}"
                    >
                </div>
            </div>
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('scraper_user_agent') }}</strong>
            </span>
        </div>
        <div class="form-group">
            <div class="form-check">
                <input type="hidden"
                       name="classic_edition"
                       id="classic_edition"
                       value="0"
                >
                <input class="form-check-input"
                       type="checkbox"
                       name="classic_edition"
                       id="classic_edition"
                       {{ old('classic_edition', $model->classic_edition) ? 'checked' : '' }}
                       value="1"
                >
                <label class="form-check-label" for="classic_edition">
                    {{ __('Classic Edition') }}
                </label>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="row justify-content-around">
    <div class="col-sm-23">
        <div class="form-group">
            <label for="nav_url_regex">{{ __('Nav URL Regex') }}</label>
            <input id="nav_url_regex"
                   type="text"
                   class="form-control{{ $errors->has('nav_url_regex') ? ' is-invalid' : '' }}"
                   name="nav_url_regex"
                   value="{{ old('nav_url_regex', $model->nav_url_regex) }}"
            >
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('nav_url_regex') }}</strong>
            </span>
        </div>
        <div class="form-group">
            <label for="nav_previous_dom_path">{{ __('Nav Previous DOM Path') }}</label>
            <input id="nav_previous_dom_path"
                   type="text"
                   class="form-control{{ $errors->has('nav_previous_dom_path') ? ' is-invalid' : '' }}"
                   name="nav_previous_dom_path"
                   value="{{ old('nav_previous_dom_path', $model->nav_previous_dom_path) }}"
            >
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('nav_previous_dom_path') }}</strong>
            </span>
        </div>
        <div class="form-group">
            <label for="nav_next_dom_path">{{ __('Nav Next DOM Path') }}</label>
            <input id="nav_next_dom_path"
                   type="text"
                   class="form-control{{ $errors->has('nav_next_dom_path') ? ' is-invalid' : '' }}"
                   name="nav_next_dom_path"
                   value="{{ old('nav_next_dom_path', $model->nav_next_dom_path) }}"
            >
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('nav_next_dom_path') }}</strong>
            </span>
        </div>
        <div class="form-group">
            <label for="nav_page_number_dom_path">{{ __('Nav Page Number DOM Path') }}</label>
            <input id="nav_page_number_dom_path"
                   type="text"
                   class="form-control{{ $errors->has('nav_page_number_dom_path') ? ' is-invalid' : '' }}"
                   name="nav_page_number_dom_path"
                   value="{{ old('nav_page_number_dom_path', $model->nav_page_number_dom_path) }}"
            >
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('nav_page_number_dom_path') }}</strong>
            </span>
        </div>
    </div>
    <div class="col-sm-23">
        <div class="form-group">
            <label for="first_index">{{ __('First Index') }}</label>
            <input id="first_index"
                   type="text"
                   class="form-control{{ $errors->has('first_index') ? ' is-invalid' : '' }}"
                   name="first_index"
                   value="{{ old('first_index', $model->first_index) instanceof \Carbon\Carbon
                    ? old('first_index', $model->first_index)->format('d/m/Y')
                    : old('first_index', $model->first_index) }}"
            >
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('first_index') }}</strong>
            </span>
        </div>
        <div class="form-group">
            <label for="last_index">{{ __('Last Index') }}</label>
            <input id="last_index"
                   type="text"
                   class="form-control{{ $errors->has('last_index') ? ' is-invalid' : '' }}"
                   name="last_index"
                   value="{{ old('last_index', $model->last_index) instanceof \Carbon\Carbon
                    ? old('last_index', $model->last_index)->format('d/m/Y')
                    : old('last_index', $model->last_index) }}"
            >
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('last_index') }}</strong>
            </span>
        </div>
        <div class="form-group">
            <label for="index_step">{{ __('Index Step') }}</label>
            <input id="index_step"
                   type="text"
                   class="form-control{{ $errors->has('index_step') ? ' is-invalid' : '' }}"
                   name="index_step"
                   value="{{ old('index_step', $model->index_step) }}"
            >
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('index_step') }}</strong>
            </span>
        </div>
    </div>
</div>
