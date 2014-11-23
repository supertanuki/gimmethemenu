$( document ).ready(function() {

    // On submit disable its submit button
    $('form').submit(function(){
        $('input[type=submit], button[type=submit]', this).attr('disabled', 'disabled');
        //$('button[type=submit]', this).attr('disabled', 'disabled');
    });


    // gestion des collections
    // Récupère le div qui contient la collection
    var collectionHolder = $('.collection');

    if(collectionHolder)
    {
        // ajoute un lien de suppression à tous les éléments li des formulaires existants
        collectionHolder.find('.collection_item').each(function() {
            addFormDeleteLink(collectionHolder, $(this));
        });

        // ajoute un lien « add »
        var $addLink = $('<a href="#" class="btn btn-default btn-sm">'+(collectionHolder.attr('data-label-add'))+'</a>');
        var $newLinkLi = $('<div></div>').append($addLink);

        // ajoute l'ancre « ajouter » et li à la balise ul
        collectionHolder.append($newLinkLi);

        $addLink.on('click', function(e) {
            // empêche le lien de créer un « # » dans l'URL
            e.preventDefault();

            // ajoute un nouveau formulaire
            addForm(collectionHolder, $newLinkLi);
        });
    }
});

function addForm(collectionHolder, $newLinkLi) {
    var prototype = collectionHolder.attr('data-prototype');

    var newForm = prototype.replace(/__name__/g, collectionHolder.children().length);

    // Affiche le formulaire dans la page dans un li, avant le lien "ajouter"
    var $newFormLi = $('<div class="collection_item"></div>').append(newForm);
    $newLinkLi.before($newFormLi);

    // ajoute un lien de suppression au nouveau formulaire
    addFormDeleteLink(collectionHolder, $newFormLi);
}

function addFormDeleteLink(collectionHolder, $FormLi) {
    var $removeFormA = $('<a href="#" class="btn btn-danger btn-xs pull-right">'+collectionHolder.attr('data-label-delete')+'</a>');
    $FormLi.prepend($removeFormA);

    $removeFormA.on('click', function(e) {
        // empêche le lien de créer un « # » dans l'URL
        e.preventDefault();

        if (confirm(collectionHolder.attr('data-label-delete-confirm'))) {
            // supprime l'élément li pour le formulaire
            $FormLi.remove();
        }
    });
}