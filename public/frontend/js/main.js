document.addEventListener('click', async function (event) {
    const buttonaddToCart = event.target.closest('.add-to-cart');

    if (!buttonaddToCart) {
        return;
    }
    const productId = buttonaddToCart.dataset.productId;
    const quantityInput = buttonaddToCart.closest('.product-information')?.querySelector('#product-quantity');
    const quantity = quantityInput ? Number(quantityInput.value) : 1;
    const url = document.querySelector('meta[name="cart-add-url"]')
        .content
        .replace('__ID__', productId);

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ quantity: quantity })
        });
        if (!response.ok) {
            throw new Error('không thể thêm sản phẩm vào giỏ hàng');
        }
        const result = await response.json();
        document.getElementById('cart-count').textContent = result.count;
    } catch (error) {
        console.error('Error:', error);
        alert('Đã xảy ra lỗi khi thêm sản phẩm vào giỏ hàng. Vui lòng thử lại.');
    }
});

/*price range*/

if ($.fn.slider) {
    $('#sl2').slider();
}

var RGBChange = function () {
    $('#RGB').css('background', 'rgb(' + r.getValue() + ',' + g.getValue() + ',' + b.getValue() + ')')
};

/*scroll to top*/

$(document).ready(function () {
    $(function () {
        $.scrollUp({
            scrollName: 'scrollUp', // Element ID
            scrollDistance: 300, // Distance from top/bottom before showing element (px)
            scrollFrom: 'top', // 'top' or 'bottom'
            scrollSpeed: 300, // Speed back to top (ms)
            easingType: 'linear', // Scroll to top easing (see http://easings.net/)
            animation: 'fade', // Fade, slide, none
            animationSpeed: 200, // Animation in speed (ms)
            scrollTrigger: false, // Set a custom triggering element. Can be an HTML string or jQuery object
            //scrollTarget: false, // Set a custom target element for scrolling to the top
            scrollText: '<i class="fa fa-angle-up"></i>', // Text for element, can contain HTML
            scrollTitle: false, // Set a custom <a> title if required.
            scrollImg: false, // Set true to use image
            activeOverlay: false, // Set CSS color to display scrollUp active point, e.g '#00FFFF'
            zIndex: 2147483647 // Z-Index for the overlay
        });
    });
});
