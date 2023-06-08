

$('.payment-card .box-space .custom-checkbox').click(function() {
    $('.payment-card.current-cart').removeClass('current-cart');
    $(this).closest('.payment-card').addClass('current-cart');
});