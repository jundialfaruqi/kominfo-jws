<!-- Tabler Core -->
    <script data-navigate-once src="{{ asset('theme/dist/js/tabler.min.js') }}" defer></script>
    <script data-navigate-once src="{{ asset('theme/dist/js/demo.min.js') }}" defer></script>
    <script data-navigate-once src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>
    
    <!-- Toggle Password Script -->
    <script>
        function togglePassword(inputId, button) {
            // If button is not provided, use event.target (for onclick calls)
            if (!button && event && event.target) {
                button = event.target.closest('button');
            }
            
            const input = document.getElementById(inputId);
            const eyeIcon = button.querySelector('.icon-eye');
            const eyeOffIcon = button.querySelector('.icon-eye-off');
            
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.style.display = 'none';
                eyeOffIcon.style.display = 'inline';
            } else {
                input.type = 'password';
                eyeIcon.style.display = 'inline';
                eyeOffIcon.style.display = 'none';
            }
        }
    </script>
