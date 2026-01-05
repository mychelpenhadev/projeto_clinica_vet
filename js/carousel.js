document.addEventListener('DOMContentLoaded', function () {
    const container = document.querySelector('.carousel-container');
    const btnLeft = document.querySelector('.carousel-btn.left');
    const btnRight = document.querySelector('.carousel-btn.right');
    const scrollAmount = 300; // Adjust scroll distance

    if (container && btnLeft && btnRight) {
        btnLeft.addEventListener('click', () => {
            container.scrollBy({
                left: -scrollAmount,
                behavior: 'smooth'
            });
        });

        btnRight.addEventListener('click', () => {
            container.scrollBy({
                left: scrollAmount,
                behavior: 'smooth'
            });
        });

        // Optional: Hide arrows if not overflowing
        // const checkScroll = () => {
        //     btnLeft.style.display = container.scrollLeft > 0 ? 'block' : 'none';
        //     btnRight.style.display = 
        //         (container.scrollWidth - container.clientWidth - container.scrollLeft) > 1 
        //         ? 'block' : 'none';
        // };
        // container.addEventListener('scroll', checkScroll);
        // window.addEventListener('resize', checkScroll);
        // checkScroll();
    }
});
