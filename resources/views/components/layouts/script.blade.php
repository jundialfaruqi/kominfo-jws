    <!-- Tabler Core -->
    <script data-navigate-once src="{{ asset('theme/dist/js/tabler.min.js') }}" defer></script>
    <script data-navigate-once src="{{ asset('theme/dist/js/demo.min.js') }}" defer></script>
    <script data-navigate-once src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordField = document.getElementById('passwordField');
            const eyeIcon = document.getElementById('eyeIcon');

            togglePassword.addEventListener('click', function(e) {
                e.preventDefault();

                // Toggle password visibility
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);

                // Toggle icon
                if (type === 'text') {
                    // Show "eye-off" icon when password is visible
                    eyeIcon.innerHTML = `
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M10.585 10.587a2 2 0 0 0 2.829 2.828" />
                            <path d="M16.681 16.673a8.717 8.717 0 0 1 -4.681 1.327c-3.6 0 -6.6 -2 -9 -6c1.272 -2.12 2.712 -3.678 4.32 -4.674m2.86 -1.146a9.055 9.055 0 0 1 1.82 -.18c3.6 0 6.6 2 9 6c-.666 1.11 -1.379 2.067 -2.138 2.87" />
                            <path d="M3 3l18 18" />
                        `;
                    togglePassword.setAttribute('title', 'Hide password');
                } else {
                    // Show "eye" icon when password is hidden
                    eyeIcon.innerHTML = `
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                        `;
                    togglePassword.setAttribute('title', 'Show password');
                }
            });
        });
    </script>
