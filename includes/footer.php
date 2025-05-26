    </main>
    <footer>
        <p>© <?php echo date("Y"); ?> KIT Shopping. All rights reserved. 2023-24</p>
    </footer>
    <script src="js/script.js"></script>
</body>
</html>
<?php
if (isset($conn)) {
    $conn->close(); 
}
?>