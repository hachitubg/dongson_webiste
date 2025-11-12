// Contact form submission
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contact-form');
    const submitButton = document.getElementById('form-submit');
    const messageContainer = document.getElementById('message-container');

    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Disable submit button to prevent double submission
            submitButton.disabled = true;
            submitButton.textContent = 'Đang gửi...';

            // Clear previous messages
            messageContainer.innerHTML = '';

            // Get form data
            const formData = new FormData(contactForm);

            // Send AJAX request
            fetch('api/contact.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Success message
                    messageContainer.innerHTML = `
                        <div class="alert alert-success" role="alert" style="padding: 15px; margin-bottom: 20px; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; color: #155724;">
                            <strong>Thành công!</strong> ${data.message}
                        </div>
                    `;
                    
                    // Reset form
                    contactForm.reset();

                    // Scroll to message
                    messageContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    // Error message
                    let errorHtml = `
                        <div class="alert alert-danger" role="alert" style="padding: 15px; margin-bottom: 20px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; color: #721c24;">
                            <strong>Lỗi!</strong> ${data.message}
                    `;
                    
                    if (data.errors && data.errors.length > 0) {
                        errorHtml += '<ul style="margin-top: 10px; margin-bottom: 0;">';
                        data.errors.forEach(error => {
                            errorHtml += `<li>${error}</li>`;
                        });
                        errorHtml += '</ul>';
                    }
                    
                    errorHtml += '</div>';
                    messageContainer.innerHTML = errorHtml;

                    // Scroll to message
                    messageContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            })
            .catch(error => {
                // Network or other error
                messageContainer.innerHTML = `
                    <div class="alert alert-danger" role="alert" style="padding: 15px; margin-bottom: 20px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; color: #721c24;">
                        <strong>Lỗi!</strong> Không thể kết nối đến máy chủ. Vui lòng thử lại sau.
                    </div>
                `;
                console.error('Error:', error);
            })
            .finally(() => {
                // Re-enable submit button
                submitButton.disabled = false;
                submitButton.textContent = 'Gửi yêu cầu';
            });
        });
    }
});
