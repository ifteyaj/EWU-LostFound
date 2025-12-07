document.addEventListener('DOMContentLoaded', () => {
    // Image Preview functionality
    const imageInput = document.getElementById('item-image');
    const imagePreview = document.getElementById('image-preview');

    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                imagePreview.src = '';
                imagePreview.style.display = 'none';
            }
        });
    }

    // Search Filter
    const searchInput = document.querySelector('.search-bar input');
    const itemCards = document.querySelectorAll('.item-card');

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase().trim();

            itemCards.forEach(card => {
                const title = card.querySelector('.card-title').textContent.toLowerCase();
                const category = card.querySelector('.badge').textContent.toLowerCase();
                const desc = card.querySelector('.card-info').textContent.toLowerCase();

                if (title.includes(searchTerm) || category.includes(searchTerm) || desc.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

    // Form Validation (Simple client-side)
    const reportForms = document.querySelectorAll('form');
    reportForms.forEach(form => {
        form.addEventListener('submit', (e) => {
            const requiredInputs = form.querySelectorAll('[required]');
            let isValid = true;

            requiredInputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.style.borderColor = 'red';
                } else {
                    input.style.borderColor = '#e0e0e0';
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });
});
