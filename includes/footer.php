<body>

<div id="main-content">
    <!-- All your page content goes here -->
</div>

<hr class="footer-divider">

<footer id="site-footer">
    &copy; <?php echo date("Y"); ?> Tafsir sa Basa Maranao by <a href="https://www.facebook.com/AbuAhmad1964">AbuAhmad Tamano</a> | Site developed by <a href="https://www.facebook.com/Tatay.Ashary">Ashary Tamano</a> | All rights reserved.
</footer>

<!-- Scroll to Top Button -->
<button id="scrollTopBtn" title="Go to top">&#8679;</button>

<script>
// Show the button when user scrolls down 200px
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
    const btn = document.getElementById("scrollTopBtn");
    if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
        btn.style.display = "block";
    } else {
        btn.style.display = "none";
    }
}

// Scroll smoothly to top when clicked
document.getElementById("scrollTopBtn").addEventListener("click", function(){
    window.scrollTo({top: 0, behavior: 'smooth'});
});
</script>

</body>
</html>
