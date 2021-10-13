/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

import $ from 'jquery';
import 'bootstrap'; // adds functions to jQuery


$('#detailModal').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget);
    const idMovie = button.data('id');


    let modal = $(this);
    modal.find('.modal-body').html(idMovie);
})