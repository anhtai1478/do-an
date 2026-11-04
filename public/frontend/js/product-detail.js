const gallery = document.querySelector('.product-details');
const thumbnails = gallery?.querySelectorAll('.product-thumbnail') ?? [];
const track = document.getElementById('product-thumbnails');
const mainImage = document.getElementById('product-main-image');
const mainLink = document.getElementById('product-main-image-link');
const modal = document.getElementById('product-image-modal');
const modalImage = document.getElementById('product-image-modal-image');

let currentImage = 0;
let thumbnailOffset = 0;

function showImage(index, direction = 'next') {
    if (!thumbnails.length || !track || !mainImage) return;

    currentImage = (index + thumbnails.length) % thumbnails.length;
    const selected = thumbnails[currentImage];
    const maxOffset = Math.max(0, thumbnails.length - 3);

    if (currentImage < thumbnailOffset) thumbnailOffset = currentImage;
    if (currentImage >= thumbnailOffset + 3) thumbnailOffset = currentImage - 2;

    thumbnailOffset = Math.min(thumbnailOffset, maxOffset);

    track.style.transform = `translateX(-${thumbnailOffset * 102}px)`;

    mainImage.classList.remove('slide-prev', 'slide-next');
    void mainImage.offsetWidth;
    mainImage.src = selected.dataset.image;
    mainImage.classList.add(`slide-${direction}`);

    if (mainLink) mainLink.href = selected.dataset.image;
    if (modalImage) modalImage.src = selected.dataset.image;

    thumbnails.forEach((thumbnail, i) => {
        thumbnail.classList.toggle('active', i === currentImage);
        thumbnail.setAttribute('aria-pressed', String(i === currentImage));
    });
}

function closeImage() {
    modal?.classList.remove('is-open');
    modal?.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('product-image-lightbox-open');
}

gallery?.addEventListener('click', (event) => {
    const target = event.target;
    const thumbnail = target.closest('.product-thumbnail');

    if (thumbnail) {
        const index = Number(thumbnail.dataset.index);
        showImage(index, index < currentImage ? 'prev' : 'next');
    } else if (target.closest('[data-thumbnail-prev]')) {
        showImage(currentImage - 1, 'prev');
    } else if (target.closest('[data-thumbnail-next]')) {
        showImage(currentImage + 1, 'next');
    } else if (target.closest('#product-main-image-link') && modal) {
        event.preventDefault();
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('product-image-lightbox-open');
    }
});

modal?.addEventListener('click', (event) => {
    if (event.target === modal || event.target.closest('[data-close-product-image]')) {
        closeImage();
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && modal?.classList.contains('is-open')) {
        closeImage();
    }
});