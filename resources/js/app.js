
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
Vue.use(require('bootstrap-vue'));
Vue.use(require('./BvFlash/src'));

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

const files = require.context('./', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

//Vue.component('user-admin-form-component', require('./components/UserAdminFormComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app'
});

$(window).on('resize scroll', function(e){
    var docH = parseInt($(document).height()),
        viewPortH = parseInt($(window).height());

    if (docH > viewPortH) {
        $('.container-index').removeClass('full-height');
    } else {
        $('.container-index').addClass('full-height');
    }
});
$(window).trigger('resize');

$(document).on('keydown', function(e) {
    if ($(':focus').is('input, select, textarea')) {
        return;
    }

    switch(e.which) {
        case 37: // left
            if ($('.btn-previous-comic').length > 0){
                $('.btn-previous-comic')[0].click();
            }
            break;
        case 39: // right
            if ($('.btn-next-comic').length > 0){
                $('.btn-next-comic')[0].click();
            }
            break;
        case 13:
        // Would require too many is() checks to be useful
        default:
            return;
    }
    e.preventDefault();
});

if ($('.alert-summarise').length > 0) {
    $('.alert-summarise').summarise();
    $('.alert-summarise').summarise('close');
}

$('#requestComicForm').on('submit', function(e){
    e.preventDefault();

    var modal = $(this).parents('.modal');

    $.post($(this).attr('action'), $('#requestComicForm').serialize(), null, 'json')
    .done(function(data){

        modal.find('.alert-summarise').summarise(
            'set',
            data.success ? 'success' : 'error',
            {
                message: data.message,
                list: data.errors
            }
        );

        if (data.success) {
            setTimeout(function () {
                modal.modal('hide');
            }, 3000);
        }
    });
});

$('#request-comic-modal').on('hidden.bs.modal', function () {
    $(this).find('.alert-summarise').summarise('close');
});

$('#comicSelector').select2({
    width: '100%',
    placeholder: 'Select a Comic',
    dropdownCssClass: 'layout-comic-select-comic-results',
    templateSelection: function (selection) {
        try{
            var o = $.parseJSON(selection.text);
            return $('<span/>').html(o.title + (o.author ? '<span>By ' + o.author + '</span>' : ''));
        }catch(e){
            return selection.text;
        }
    },
    templateResult: function (result) {
        try{
            var o = $.parseJSON(result.text);
            return $('<span/>').html(o.title + (o.author ? '<span>By ' + o.author + '</span>' : ''));
        }catch(e){
            return result.text;
        }
    },
});

$('#comicSelector').on('change', function(e){
    window.location.href = $(this).find(':selected').data('url');
});

$('.datepicker').datepicker({
    dateFormat : 'dd-mm-yy',
    changeMonth: true,
    changeYear: true,
    maxDate: $('#comic-strip-index').data('latestindex'),
});

$('.datepicker').on('change', function(e){
    $(this).parents('form').submit();
});
