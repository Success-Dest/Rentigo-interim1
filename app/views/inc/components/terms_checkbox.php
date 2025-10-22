<div class="terms-acceptance">
    <label class="terms-checkbox-label">
        <input type="checkbox"
            name="accept_terms"
            id="accept_terms"
            class="terms-checkbox"
            required>
        <span class="checkmark"></span>
        <span class="terms-text">
            I agree to the
            <a href="<?php echo URLROOT; ?>/pages/terms" target="_blank" class="terms-link">Terms and Conditions</a>
            and
            <a href="<?php echo URLROOT; ?>/pages/privacy" target="_blank" class="terms-link">Privacy Policy</a>
        </span>
    </label>
    <span class="error-message" id="terms-error" style="display: none;">You must accept the terms and conditions</span>
</div>

<style>
    .terms-acceptance {
        margin: 1.5rem 0;
        padding: 1rem;
        background: rgba(69, 169, 234, 0.05);
        border: 1px solid rgba(69, 169, 234, 0.2);
        border-radius: 8px;
    }

    .terms-checkbox-label {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        cursor: pointer;
        position: relative;
        padding-left: 2rem;
    }

    .terms-checkbox {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    .checkmark {
        position: absolute;
        left: 0;
        top: 0;
        height: 20px;
        width: 20px;
        background-color: white;
        border: 2px solid #d1d5db;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .terms-checkbox:hover~.checkmark {
        border-color: #45a9ea;
    }

    .terms-checkbox:checked~.checkmark {
        background-color: #45a9ea;
        border-color: #45a9ea;
    }

    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
        left: 6px;
        top: 2px;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    .terms-checkbox:checked~.checkmark:after {
        display: block;
    }

    .terms-text {
        font-size: 0.875rem;
        color: #374151;
        line-height: 1.5;
    }

    .terms-link {
        color: #45a9ea;
        text-decoration: none;
        font-weight: 500;
    }

    .terms-link:hover {
        text-decoration: underline;
    }

    .error-message {
        display: block;
        color: #ef4444;
        font-size: 0.813rem;
        margin-top: 0.5rem;
    }
</style>

<script>
    // Terms validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const termsCheckbox = document.getElementById('accept_terms');
        const termsError = document.getElementById('terms-error');

        if (form && termsCheckbox) {
            form.addEventListener('submit', function(e) {
                if (!termsCheckbox.checked) {
                    e.preventDefault();
                    termsError.style.display = 'block';
                    termsCheckbox.focus();
                    alert('Please accept the Terms and Conditions to continue');
                    return false;
                }
                termsError.style.display = 'none';
            });

            termsCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    termsError.style.display = 'none';
                }
            });
        }
    });
</script>