$(document).on('click', '.delete-button', function(){
    return confirm('Confirmez cette suppression ?');
});

$(document).on('click', '.btn-search-open', function(e){
    e.preventDefault();
    $('.search-zone').fadeToggle();
});

$(document).on('click', '.click-check tbody tr', function (e){
    e.stopPropagation();
    e.preventDefault();
    $(this).addClass('green');
});

/**
 * Collection
 */
/**
 * Collections form
 */
function addFormDeleteLink($formLi) {
    var $removeFormA = $('<a class="btn btn-danger delete-button" href="#"><i class="fa fa-trash-o"></i> ' + delete_label + '</a>');
    if($formLi.find('.delete-button').length === 0) {
        var $td = $('<td></td>');
        $td.append($removeFormA);
        $formLi.append($td);

        $removeFormA.on('click', function(e) {
            e.preventDefault();
            $formLi.remove();
        });
    }
}

function addForm($collectionHolder, $newLinkLi) {
    var prototype = $collectionHolder.data('prototype');
    var index = parseInt($collectionHolder.data('index')) + 1;
    var newForm = prototype;
    newForm = newForm.replace(/__name__/g, index);
    $collectionHolder.data('index', index);
    var $newFormLi = $('<tr></tr>').append(newForm);
    $newLinkLi.before($newFormLi);
    addFormDeleteLink($newFormLi);
}

function addFormAddLink($collectionHolder)
{
    var $addLink = $('<a href="#" class="add_link btn btn-success"><i class="fa fa-plus"></i> ' + add_label + '</a>');
    var $tdNewLinkLi = $('<td colspan="4"></td>');
    $tdNewLinkLi.append($addLink);
    var $newLinkLi = $('<tr class="addbtn"></tr>').append($tdNewLinkLi);
    $addLink.on('click', function(e) {
        e.preventDefault();
        addForm($collectionHolder, $newLinkLi);
    });
    $collectionHolder.append($newLinkLi);

}

function initCollection()
{
    $('table.collections').each(function() {

        var $collectionHolder = $(this);
        $collectionHolder.find('tr:not(.addbtn):not(.title)').each(function() {
            addFormDeleteLink($(this));
        });

        addFormAddLink($collectionHolder);
        $collectionHolder.data('index', $collectionHolder.children('tbody').children('tr:not(.addbtn):not(.title)').length);
    });
}

jQuery(document).ready(function() {
    initCollection();
});

