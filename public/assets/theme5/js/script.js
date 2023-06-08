

$('.p-color ul li a').click(function() {
    $('ul li.current').removeClass('current');
    $(this).closest('li').addClass('current');
});

$('.card .box-space .custom-checkbox').click(function() {
    $('.card.current-cart').removeClass('current-cart');
    $(this).closest('.card').addClass('current-cart');
});