        </div> <!-- End row -->
    </div> <!-- End container-fluid -->
    
    <!-- Footer -->
    <footer class="footer mt-auto py-3 bg-white border-top">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 text-muted">
                    <small>&copy; <?php echo date('Y'); ?> SignED SPED System. All rights reserved.</small>
                </div>
                <div class="col-md-6 text-end text-muted">
                    <small>Version 1.0.0</small>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo URLROOT; ?>/assets/js/custom.js"></script>
    
    <!-- Page-specific scripts -->
    <?php if (isset($data['extra_js'])): ?>
        <?php echo $data['extra_js']; ?>
    <?php endif; ?>
</body>
</html>
