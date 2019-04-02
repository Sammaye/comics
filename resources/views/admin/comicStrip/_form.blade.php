<input type="hidden" name="comic_id" id="comic_id" value="{{ $model->comic_id }}">
<div class="row">
    <div class="col">
        <div class="form-group">
            <label for="url">{{ __('Url') }}</label>
            <input id="url"
                   type="text"
                   class="form-control{{ $errors->has('url') ? ' is-invalid' : '' }}"
                   name="url"
                   value="{{ old('url', $model->url) }}"
            >
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('url') }}</strong>
            </span>
        </div>
        <div class="form-group">
            <label for="date">{{ __('Date') }}</label>
            <input id="date"
                   type="text"
                   class="form-control{{ $errors->has('date') ? ' is-invalid' : '' }}"
                   name="date"
                   value="{{ old('date', $model->date) instanceof \Carbon\Carbon
                    ? old('date', $model->date)->format('d/m/Y')
                    : old('date', $model->date) }}"
            >
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('date') }}</strong>
            </span>
        </div>
        <div class="form-group">
            <label for="index">{{ __('Index') }}</label>
            <input id="index"
                   type="text"
                   class="form-control{{ $errors->has('index') ? ' is-invalid' : '' }}"
                   name="index"
                   value="{{ old('index', $model->index) instanceof \Carbon\Carbon
                    ? old('index', $model->index)->format('d/m/Y')
                    : old('index', $model->index) }}"
                   required
            >
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('index') }}</strong>
            </span>
        </div>
        <div class="form-group">
            <div class="form-check">
                <input type="hidden"
                       name="skip"
                       id="skip"
                       value="0"
                >
                <input class="form-check-input"
                       type="checkbox"
                       name="skip"
                       id="skip" {{ old('skip', $model->skip) ? 'checked' : '' }}
                       value="1"
                >
                <label class="form-check-label" for="alert">
                    {{ __('Do not download this strip') }}
                </label>
            </div>
        </div>
        <div class="form-group">
            <label for="previous">{{ __('Previous') }}</label>
            <input id="previous"
                   type="text"
                   class="form-control{{ $errors->has('previous') ? ' is-invalid' : '' }}"
                   name="previous"
                   value="{{ old('previous', $model->previous) instanceof \Carbon\Carbon
                    ? old('previous', $model->previous)->format('d/m/Y')
                    : old('previous', $model->previous) }}"
            >
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('previous') }}</strong>
            </span>
        </div>
        <div class="form-group">
            <label for="next">{{ __('Next') }}</label>
            <input id="next"
                   type="text"
                   class="form-control{{ $errors->has('next') ? ' is-invalid' : '' }}"
                   name="next"
                   value="{{ old('next', $model->next) instanceof \Carbon\Carbon
                    ? old('next', $model->next)->format('d/m/Y')
                    : old('next', $model->next) }}"
            >
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('next') }}</strong>
            </span>
        </div>
    </div>
</div>
