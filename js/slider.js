/**
 * Slider Manager - Beheert card carousel op homepage
 */
class SliderManager {
    constructor() {
        this.slider = document.getElementById('activitySlider');
        this.slides = this.slider?.querySelectorAll('[data-slide]');
        this.currentIndex = 0;

        if (this.slides && this.slides.length > 0) {
            this.init();
        }
    }

    init() {
        this.attachClickHandlers();
        this.setupAutoPlay();
        this.setupResizeListener();
        this.showSlide(0);
    }

    getCardWidth() {
        if (!this.slides || this.slides.length === 0) return 0;
        const card = this.slides[0];
        const style = window.getComputedStyle(card);
        return card.offsetWidth + parseFloat(style.marginRight || 0);
    }

    showSlide(index) {
        if (!this.slides || this.slides.length === 0) return;
        this.currentIndex = (index + this.slides.length) % this.slides.length;
        const cardWidth = this.getCardWidth();
        const offset = this.currentIndex * cardWidth;
        this.slider.scrollTo({ left: offset, behavior: 'smooth' });
    }

    next() {
        this.showSlide(this.currentIndex + 1);
    }

    attachClickHandlers() {
        this.slides?.forEach(card => {
            card.style.cursor = 'pointer';
            card.addEventListener('click', () => {
                window.location.href = 'Activiteiten.php';
            });
        });
    }

    setupAutoPlay() {
        setInterval(() => this.next(), 7000);
    }

    setupResizeListener() {
        window.addEventListener('resize', () => this.showSlide(this.currentIndex));
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new SliderManager();
});
