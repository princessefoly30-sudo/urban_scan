<footer style="text-align: center; padding: 20px; margin-top: 50px; color: #7f8c8d; font-family: sans-serif;">
    <p>&copy; 2026 - <strong>Nvadigital</strong> | Projet Informatique de Gestion L2</p>
</footer>
<script>
    (function() {
        const body = document.body;
        const toggle = document.querySelector('.sidebar-toggle');
        const backdrop = document.querySelector('.sidebar-backdrop');

        if (!toggle) return;

        function closeSidebar() {
            body.classList.remove('sidebar-open');
        }

        function toggleSidebar() {
            if (window.innerWidth <= 980) {
                body.classList.toggle('sidebar-open');
            } else {
                body.classList.toggle('sidebar-collapsed');
            }
        }

        toggle.addEventListener('click', toggleSidebar);
        backdrop && backdrop.addEventListener('click', closeSidebar);

        window.addEventListener('resize', function() {
            if (window.innerWidth > 980) {
                body.classList.remove('sidebar-open');
            }
        });
    })();
</script>