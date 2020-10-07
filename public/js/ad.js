$('#add-image').click(function(){
    // Je récupère le numéro des futurs champs que je vais créer
    const index = +$('#widgets-counter').val();

    console.log(index);
    // Je récupère le prototype(le code html necessaire à génerer une nouvellle entré, un nouveau formulaire ) des entrées

    const tmpl = $('#ad_images').data('prototype').replace(/__name__/g,index);
    
    //j'inject ce code au sein de la div
    $('#ad_images').append(tmpl);

    $('#widgets-counter').val(index +1);
    
    // Je gère ke button supprimer
    handleDeleteButtons();
});

function handleDeleteButtons(){
    $('button[data-action="delete"]').click(function(){
        const target = this.dataset.target;
        $(target).remove();
    });
}

function updateCounter(){
    const count = +$('#ad_images div.form-group').length; // le "+" est la pour dire que l'on veut que ça soit un nombre!

    $('#widgets-counter').val(count);
}
updateCounter();

handleDeleteButtons();