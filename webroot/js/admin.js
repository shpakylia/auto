function confirmDelete(){
    if ( confirm("Delete this item?") ){
        return true;
    } else {
        return false;
    }
}
function ready() {
//    $("#inline").fancybox({
//        'hideOnContentClick': true
//    }
//    );

    new jBox('Modal', {
        width: 1200,
        height: 500,
        maxHeight:500,
        maxWidth:1200,
        attach: $('#myModal'),
        title: 'Картинки',
        content: $('#thumbnail')
    });
    $( ".radio_thumb" ).change(function(){
        $('.radio_thumb').next().css("border", 'none');
        var c = this.checked ? '2px solid blue' : 'none';
        $(this).next().css("border", c);


    });


};
document.addEventListener("DOMContentLoaded", ready);

