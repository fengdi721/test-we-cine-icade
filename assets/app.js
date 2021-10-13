/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

import $ from 'jquery';
import 'bootstrap';
import 'autocomplete.js/dist/autocomplete.jquery';

$('#detailModal').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget);
    const idMovie = button.data('id');
    let modal = $(this);

    $.ajax({
        url: `/view/${idMovie}`,
        success: function (html) {
            modal.find('.modal-body').html(html);
        }
    });
})

// Autocomplete search
$("#search").autocomplete({hint: false, minLength: 3}, [{
    source: function(query, cb) {
        $.ajax({
            url: '/search' +'?query=' + query
        }).then((data) => {
            if(data.results !== undefined) {
                data = data.results;
            } else {
                data = [];
            }
            cb(data);
        });

    },
    displayKey: "title",
    debounce: 500,
    templates: {
        suggestion: function(suggestion) {
            const baseUri = $("#search").data('uri');
            return `<div class="suggestion"><img src="${baseUri}${suggestion.poster_path}" alt="${suggestion.id}"><span class="title">${suggestion.title}</span></div>`;
        }
    }
}]);

// Filter by genre
$(document).on("click", "#filters input[type=radio]", function (event) {
    const target = $(event.target);
    $.ajax({
        url: "/discover",
        type: "GET",
        data: {
            with_genres: target.data('genre')
        },
        beforeSend: function() {
            $('#loader').show();
        },
        complete: function(){
            $('#loader').hide();
        },
        success: function(response) {
            if (response) {
                $('#movie_list').html(response);
            }
        }
    });
});
