
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

// window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

// Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
/**
const app = new Vue({
    el: '#app'
});
*/

$(document).on('keypress', '.gridview thead input',  function(e){
    if (e.which == 13) {
        window.location = $(this).parents('tr').attr('data-action') + '?' + $(this).parents('tr').find('input').serialize();
    }
});

$(document).on('change', '#scraper_user_agent_prefill', function(e){
    if ($(this).val()) {
        $('#scraper_user_agent').val($(this).val());
    }
});

if ($('.alert-summarise').length > 0) {
    $('.alert-summarise').summarise();
    $('.alert-summarise').summarise('close');
}

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

$(document).on('click', '.container-comic-view .btn-subscribe', function(e){
    e.preventDefault();

    var btn = $(this),
        otherBtn = $('.btn-unsubscribe');

    $.post(btn.attr('href'), {}, null, 'json')
    .done(function(data){
        if(data.success){
            btn.addClass('d-none');
            otherBtn.removeClass('d-none');
        }
    });
});

$(document).on('click', '.container-comic-view .btn-unsubscribe', function(e){
    e.preventDefault();

    var btn = $(this),
        otherBtn = $('.btn-subscribe');

    $.post(btn.attr('href'), {}, null, 'json')
    .done(function(data){
        if(data.success){
            btn.addClass('d-none');
            otherBtn.removeClass('d-none');
        }
    });
});

$(document).on('click', '.container-user-edit .btn-unsubscribe', function(e){
    e.preventDefault();
    $(this).parents('li').remove();
});

$( '#sortable' ).sortable();
$( '#sortable' ).disableSelection();
