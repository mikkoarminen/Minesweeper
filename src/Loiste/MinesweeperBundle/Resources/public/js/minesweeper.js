/*!
 * This file contains the front-end logic for the Minesweeper game.
 */

$(document).ready(function() {
    $('.undiscovered').mousedown(function() {
        $(this).addClass("pressed"); 
        $(this).removeClass("active");
    });
    
    $('.undiscovered').mouseup(function() {
        $(this).removeClass("pressed");
        $(this).addClass("active");
    });

    $('.undiscovered').mouseover(function() {
        $(this).addClass("active")    
    });

    $('.undiscovered').mouseout(function() {
        $(this).removeClass("pressed active");
    });
    
    $('.number').mousedown(function() {
        var id = $(this).attr('id');
        var point = id.split('_');
        var row = parseInt(point[0]);
        var column = parseInt(point[1]);

        // Set all surrounding undiscovered cells pressed.
        $('#' + (row-1) + '_' + (column-1) + '.undiscovered').addClass('pressed');
        $('#' + (row-1) + '_' + column + '.undiscovered').addClass('pressed');
        $('#' + (row-1) + '_' + (column+1) + '.undiscovered').addClass('pressed');
        $('#' + row + '_' + (column-1) + '.undiscovered').addClass('pressed');
        $('#' + row + '_' + (column+1) + '.undiscovered').addClass('pressed');
        $('#' + (row+1) + '_' + (column-1) + '.undiscovered').addClass('pressed');
        $('#' + (row+1) + '_' + column + '.undiscovered').addClass('pressed');
        $('#' + (row+1) + '_' + (column+1) + '.undiscovered').addClass('pressed');
    });

    function removePressed(row, column) {
        // Set all surrounding undiscovered cells pressed.
        $('#' + (row-1) + '_' + (column-1) + '.undiscovered').removeClass('pressed');
        $('#' + (row-1) + '_' + column + '.undiscovered').removeClass('pressed');
        $('#' + (row-1) + '_' + (column+1) + '.undiscovered').removeClass('pressed');
        $('#' + row + '_' + (column-1) + '.undiscovered').removeClass('pressed');
        $('#' + row + '_' + (column+1) + '.undiscovered').removeClass('pressed');
        $('#' + (row+1) + '_' + (column-1) + '.undiscovered').removeClass('pressed');
        $('#' + (row+1) + '_' + column + '.undiscovered').removeClass('pressed');
        $('#' + (row+1) + '_' + (column+1) + '.undiscovered').removeClass('pressed');
    }

    $('.number').mouseup(function() {
        var id = $(this).attr('id');
        var point = id.split('_');
        var row = parseInt(point[0]);
        var column = parseInt(point[1]);

        removePressed(row, column);
    });

    $('.number').mouseout(function() {
        var id = $(this).attr('id');
        var point = id.split('_');
        var row = parseInt(point[0]);
        var column = parseInt(point[1]);

        removePressed(row, column);
    });
});

$(function() {
    // Find out the route to the makeMove -action.
    var routeMakeMove = $('#game').data('route-make-move');

    $('.game-cell').click(function() {
        // Find out the index of column & row.
        var column = $(this).index();
        var $tr = $(this).parents('tr');
        var row = $tr.index();

        // Make a move.
        window.location = routeMakeMove + '?column=' + column + '&row=' + row; // Simple URL param concatenation.
    });
    
    var routeMarkCell = $('#game').data('route-mark-cell');

    $('.game-cell').bind("contextmenu",function(e){
        // Find out the index of column & row.
        var column = $(this).index();
        var $tr = $(this).parents('tr');
        var row = $tr.index();

        window.location = routeMarkCell + '?column=' + column + '&row=' + row;
        return false;
    });
    
});
