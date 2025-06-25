<!-- Footer -->
<footer class="bg-dark text-white text-center py-3 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <h5>BasketProgress</h5>
                <p>Tehnologie pentru performanță în baschet</p>
            </div>
            <div class="col-md-3">
                <h5>Navigare Rapidă</h5>
                <ul class="list-unstyled">
                    <li><a href="logout.php" class="text-white">Ieșire</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h5>Informații Legale</h5>
                <ul class="list-unstyled">
                    <li><a href="terms.php" class="text-white">Termeni și Condiții</a></li>
                    <li><a href="confidentialitate.php" class="text-white">Politica de Confidențialitate</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h5>Contact</h5>
                <p>office@basketprogress.ro<br>+40 721 234 567</p>
            </div>
        </div>
        <hr class="my-2">
        <p>&copy; <?= date('Y') ?> BasketProgress. Toate drepturile rezervate.</p>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>

<?php
ob_end_flush();
?>
