<?php
// index.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FarmFresh - Organic Farm Website Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Meta descriptions -->
    <meta name="keywords" content="Organic Farm, Fresh Produce, FarmFresh">
    <meta name="description" content="FarmFresh - Providing fresh and organic produce for a healthy lifestyle.">

    <!-- Favicon -->
    <link rel="icon" href="img/favicon.ico">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">

    <!-- Include the new fonts in your HTML head -->
    <link href="https://fonts.googleapis.com/css2?family=Vidaloka&family=Libre+Baskerville&family=Neuton&display=swap" rel="stylesheet">

    <!-- Inline CSS -->
    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html, body {
            scroll-behavior: smooth;
            font-family: 'Neuton', serif;
            background-color: #FEFAE0;
            color: #283618;
            overflow-x: hidden;
            background-image: url('img/backgroundimg3.jpg');
            background-color: #FEFAE0;
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        /* Navbar */
        .navbar {
            position: fixed;
            width: 100%;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: transparent;
            z-index: 1000;
            transition: background 0.3s, padding 0.3s;
        }
        .navbar.scrolled {
            background: #283618;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 10px 40px;
        }
        .navbar .logo {
            font-size: 24px;
            font-weight: 700;
            text-transform: uppercase;
            color: #FEFAE0;
            font-family: 'Montserrat', sans-serif;
        }
        .navbar .nav-links {
            display: flex;
            align-items: center;
        }
        .navbar ul {
            display: flex;
            list-style: none;
            margin-right: 20px;
        }
        .navbar ul li {
            margin-left: 30px;
        }
        .navbar ul li a {
            text-decoration: none;
            color: #FEFAE0;
            font-size: 16px;
            transition: color 0.3s;
            position: relative;
            font-family: 'Neuton', serif;
        }
        .navbar ul li a::after {
            content: '';
            width: 0%;
            height: 2px;
            background: #BC6C25;
            position: absolute;
            left: 0;
            bottom: -5px;
            transition: width 0.3s;
        }
        .navbar ul li a:hover {
            color: #DDA15E;
        }
        .navbar ul li a:hover::after {
            width: 100%;
        }
        .navbar .login-btn {
            background: #BC6C25;
            color: #FEFAE0;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 16px;
            transition: background 0.3s;
            font-family: 'Neuton', serif;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
        }
        .navbar .login-btn:hover {
            background: #DDA15E;
        }
        /* Mobile Menu */
        .menu-toggle {
            display: none;
            flex-direction: column;
            cursor: pointer;
        }
        .menu-toggle span {
            width: 30px;
            height: 3px;
            background: #FEFAE0;
            margin: 5px 0;
            transition: all 0.3s;
        }
        .menu-toggle.open span:nth-child(1) {
            transform: translateY(8px) rotate(45deg);
        }
        .menu-toggle.open span:nth-child(2) {
            opacity: 0;
        }
        .menu-toggle.open span:nth-child(3) {
            transform: translateY(-8px) rotate(-45deg);
        }
        /* Hero Section */
        .hero {
            height: 100vh;
            background: url('img/carousel-1.jpg') center center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
        }
        .hero::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(40, 54, 24, 0.6);
        }
        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            color: #FEFAE0;
        }
        .hero h1 {
            font-size: 50px;
            margin-bottom: 20px;
            animation: fadeInDown 1s forwards;
            font-family: 'Libre Baskerville', serif;
        }
        .hero p {
            font-size: 20px;
            margin-bottom: 40px;
            animation: fadeInUp 1s forwards;
            font-family: 'Neuton', serif;
        }
        .hero .btn {
            padding: 15px 30px;
            font-size: 16px;
            margin: 10px;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
            border-radius: 30px;
            font-family: 'Neuton', serif;
            text-decoration: none;
        }
        .btn-primary {
            background: #BC6C25;
            color: #FEFAE0;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
        }
        .btn-secondary {
            background: #FEFAE0;
            color: #BC6C25;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
        }
        .btn-primary:hover {
            background: #DDA15E;
        }
        .btn-secondary:hover {
            background: #F0EDE5;
        }
        /* Sections */
        section {
            padding: 100px 40px;
        }
        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }
        .section-title h2 {
            font-size: 36px;
            margin-bottom: 20px;
            position: relative;
            display: inline-block;
            color: #606C38;
            font-family: 'Libre Baskerville', serif;
        }
        .section-title h2::after {
            content: '';
            width: 50px;
            height: 3px;
            background: #BC6C25;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            bottom: -10px;
        }
        /* About Section */
        .about {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }
        .about-img {
            flex: 1;
            min-width: 300px;
            margin-right: 40px;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3);
            border: 1px solid #DDA15E;
            border-radius: 15px;
        }
        .about-content {
            flex: 1;
            min-width: 300px;
        }
        .about-content h2 {
            font-size: 32px;
            margin-bottom: 20px;
            color: #606C38;
            font-family: 'Libre Baskerville', serif;
        }
        .about-content p {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 20px;
            color: #666666;
            font-family: 'Neuton', serif;
        }
        /* Features Section */
        .features {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .feature-item {
            flex: 1 1 250px;
            max-width: 250px;
            margin: 20px;
            text-align: center;
            background: #FEFAE0;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3);
            border: 1px solid #DDA15E;
        }
        .feature-item i {
            font-size: 50px;
            margin-bottom: 20px;
            color: #BC6C25;
        }
        .feature-item h4 {
            margin-bottom: 10px;
            font-size: 22px;
            color: #606C38;
            font-family: 'Libre Baskerville', serif;
        }
        .feature-item p {
            color: #666666;
            font-family: 'Neuton', serif;
        }
        /* Products Section */
        .products {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .product-item {
            flex: 1 1 300px;
            max-width: 300px;
            margin: 20px;
            text-align: center;
            background: #FEFAE0;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.3);
            border: 1px solid #DDA15E;
        }
        .product-item img {
            width: 100%;
            border-radius: 10px;
            transition: transform 0.3s;
        }
        .product-item img:hover {
            transform: scale(1.05);
        }
        .product-item h6 {
            margin-top: 15px;
            font-size: 20px;
            color: #606C38;
            font-family: 'Libre Baskerville', serif;
        }
        .product-item h5 {
            margin-top: 5px;
            font-size: 18px;
            color: #BC6C25;
            font-family: 'Neuton', serif;
        }
        /* Footer */
        footer {
            background: #283618;
            padding: 40px;
            text-align: center;
            color: #FEFAE0;
            font-family: 'Neuton', serif;
        }
        footer p {
            margin: 10px 0;
        }
        footer .social-links a {
            color: #FEFAE0;
            margin: 0 10px;
            font-size: 20px;
            transition: color 0.3s;
        }
        footer .social-links a:hover {
            color: #DDA15E;
        }
        /* Animations */
        @keyframes fadeInDown {
            0% { opacity: 0; transform: translateY(-50px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(50px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        /* Responsive */
        @media (max-width: 992px) {
            .about {
                flex-direction: column;
            }
            .about-img, .about-content {
                margin: 0;
            }
            .about-content {
                margin-top: 40px;
            }
        }
        @media (max-width: 768px) {
            .navbar ul {
                position: fixed;
                top: 0;
                right: -100%;
                height: 100vh;
                width: 200px;
                background: #FEFAE0;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
                transition: right 0.3s;
            }
            .navbar ul.open {
                right: 0;
            }
            .navbar ul li {
                margin: 20px 0;
            }
            .menu-toggle {
                display: flex;
            }
            .menu-toggle span {
                background: #FEFAE0;
            }
            .navbar.scrolled .menu-toggle span {
                background: #283618;
            }
        }
    </style>

</head>
<body>

    <!-- Navbar -->
    <nav class="navbar" id="navbar">
        <div class="logo">NeighbourGoods</div>
        <div class="menu-toggle" id="menu-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="nav-links" id="nav-menu">
            <ul>
                <li><a href="#hero">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#features">Features</a></li>
                <li><a href="#listings">Listings</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
            <a href="login.php" class="login-btn">Login</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="hero">
        <div class="hero-content">
            <h1>Connecting Neighbors, Sharing Goods</h1>
            <p>Join NeighbourGoods to share surplus items and build a stronger community.</p>
            <a href="#products" class="btn btn-primary">Explore Listings</a>
            <a href="#contact" class="btn btn-secondary">Contact Us</a>
        </div>
    </section>

    <!-- About Section -->
    <section id="about">
        <div class="about">
            <div class="about-img">
                <img src="img/about.png" alt="About Us">
            </div>
            <div class="about-content">
                <h2>Our Mission</h2>
                <p>NeighbourGoods aims to reduce waste and foster community relationships by connecting neighbors who have surplus goods with those who need them. We believe in the power of sharing and the positive impact it has on our environment and social connections.</p>
                <p>Whether it's sharing tools, appliances, or everyday items, NeighbourGoods makes it easy to list items you're willing to lend or give away, and find what you need from people nearby.</p>
                <div class="features">
                    <div class="feature-item">
                        <i class="fas fa-handshake"></i>
                        <h4>Community Focused</h4>
                        <p>Strengthening neighborhood bonds through sharing.</p>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-recycle"></i>
                        <h4>Sustainable Living</h4>
                        <p>Promoting reuse to reduce waste and environmental impact.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="features">
        <div class="section-title">
            <h2>Organic Farm Services</h2>
        </div>
        <div class="features">
        <div class="feature-item">
                <i class="fas fa-map-marker-alt"></i>
                <h4>Local Listings</h4>
                <p>Find items available in your neighborhood.</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-plus-circle"></i>
                <h4>Easy Posting</h4>
                <p>Quickly list items you want to share or give away.</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-comments"></i>
                <h4>Secure Messaging</h4>
                <p>Communicate with neighbors safely within the platform.</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-users"></i>
                <h4>Community Events</h4>
                <p>Participate in local events and meetups.</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-star"></i>
                <h4>User Ratings</h4>
                <p>Build trust through ratings and reviews.</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-shield-alt"></i>
                <h4>Privacy Protection</h4>
                <p>Your personal information is safe with us.</p>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section id="products">
        <div class="section-title">
            <h2>Our Fresh & Organic Products</h2>
        </div>
        <div class="products">
            <div class="product-item">
                <img src="img/product-1.png" alt="Product">
                <h6>Organic Vegetable</h6>
                <h5>$19.00</h5>
            </div>
            <div class="product-item">
                <img src="img/product-2.png" alt="Product">
                <h6>Organic Fruit</h6>
                <h5>$25.00</h5>
            </div>
            <div class="product-item">
                <img src="img/product-1.png" alt="Product">
                <h6>Organic Vegetable</h6>
                <h5>$19.00</h5>
            </div>
            <!-- Add more products as needed -->
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact">
        <div class="section-title">
        <h2>Contact Us</h2>
        <p>Have questions or feedback? We'd love to hear from you.</p>
        </div>
        <div style="text-align: center;">
            <p><i class="fas fa-map-marker-alt"></i> 123 Street, New York, USA</p>
            <p><i class="fas fa-envelope"></i> info@example.com</p>
            <p><i class="fas fa-phone-alt"></i> +012 345 67890</p>
            <!-- Add a contact form if desired -->
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2023 NeighbourGoods. All rights reserved.</p>
        <div class="social-links">
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-linkedin-in"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Navbar scroll background change
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Mobile menu toggle
        const menuToggle = document.getElementById('menu-toggle');
        const navMenu = document.getElementById('nav-menu');
        menuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('open');
            menuToggle.classList.toggle('open');
        });
    </script>

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/yourfontawesomekit.js" crossorigin="anonymous"></script>

</body>
</html>
