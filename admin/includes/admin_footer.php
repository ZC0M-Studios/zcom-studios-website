    </div> <!-- End admin-wrapper -->

    <?php
    /* ========================================================
        //ANCHOR [ADMIN_FOOTER_BATCOMPUTER]
        FUNCTION: Admin Footer - Batcomputer UI Style
    -----------------------------------------------------------
        UniqueID: 793103
    =========================================================== */
    ?>

    <!-- BATCOMPUTER Toast Notification Container -->
    <div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/admin/js/admin.js"></script>
    <script>
        // BATCOMPUTER: Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });

            // Add bat-theme class to ensure consistent styling
            document.body.classList.add('bat-theme');
        });
    </script>
</body>
</html>
