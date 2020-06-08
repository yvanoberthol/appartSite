$('#add-image').click(function (e) {
    e.preventDefault();
    const index = +$('#counter').val();

    const template_image = $('#annonce_images').data('prototype').replace(/__name__/g,index);
    $('#annonce_images').append(template_image);

    $('#counter').val(index+1);

    handleDeleteButtonsForImage();
});

function handleDeleteButtonsForImage() {
    $('button[data-action="delete"]').click(function () {
        const target = this.dataset.target;
        $(target).remove();
    });
}

function updateCounter() {
    const count = +$('#annonce_images div.form-group').length;
    $('#counter').val(count);
}


updateCounter();
handleDeleteButtonsForImage();