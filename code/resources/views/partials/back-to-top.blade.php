<!-- Back to Top Button -->
<button type="button" class="btn btn-primary btn-floating" id="backToTopBtn" title="{{ __('app.back_to_top') }}">
    <i class="bi bi-arrow-up"></i>
</button>

<style>
.btn-floating {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: none;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
}

.btn-floating:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.4);
}

.btn-floating.show {
    display: flex;
    animation: fadeInUp 0.3s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .btn-floating {
        width: 45px;
        height: 45px;
        bottom: 15px;
        right: 15px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const backToTopBtn = document.getElementById('backToTopBtn');

    // Show/hide button based on scroll position
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopBtn.classList.add('show');
        } else {
            backToTopBtn.classList.remove('show');
        }
    });

    // Smooth scroll to top when clicked
    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});
</script>