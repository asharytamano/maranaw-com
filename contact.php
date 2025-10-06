<?php include 'header.php'; ?>
<hr>

<!-- Embedded styles for Contact Page -->
<style>
    .site-content {
        max-width: 800px;   /* controls content width */
        margin: 20px auto;  /* center horizontally */
        padding: 0 20px;
    }
    .site-content h1 {
        font-size: 32px;
        text-align: center;
        margin: 40px 0 30px 0;
        font-weight: bold;
    }
    .site-content p {
        font-size: 18px;
        line-height: 1.8;
        margin-bottom: 20px;
        color: #333;
    }
    .contact-form label {
        display: block;
        font-weight: bold;
        margin-top: 15px;
        margin-bottom: 5px;
    }
    .contact-form input,
    .contact-form textarea {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }
    .contact-form textarea {
        resize: vertical;
        min-height: 120px;
    }
    .contact-form button {
        margin-top: 20px;
        padding: 10px 20px;
        font-size: 16px;
        background-color: #000;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    .contact-form button:hover { background-color: #444; }

    .site-footer {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px 0;
        text-align: center;
        color: #666;
        font-size: 14px;
    }
</style>

<main class="site-content">
    <h1>Contact Us</h1>
    <p>If you have any questions, feedback, or suggestions, please feel free to reach out to us using the form below. We appreciate your input and will get back to you as soon as possible.</p>

    <form class="contact-form" action="sendmail.php" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="message">Message:</label>
        <textarea id="message" name="message" required></textarea>

        <button type="submit">Send Message</button>
    </form>
</main>
<hr>
    <footer>
        <div class="site-footer">
            <p>&copy; <?php echo date("Y"); ?> Maranaw Tafsir by <strong>Abu Ahmad Tamano.</strong> | Site developed by <strong>Ashary Tamano</strong>. | All rights reserved.</p>
        </div>
    </footer>