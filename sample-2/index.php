<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Developer Profile</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f4f4f9;
        }

        /* Container */
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: white;
            text-align: center;
            padding: 100px 20px;
        }

        .hero .highlight {
            color: #ffe600;
        }

        .hero .subtitle {
            margin: 20px 0;
            font-size: 1.2rem;
        }

        .hero .btn {
            background: #ffe600;
            color: #333;
            padding: 10px 20px;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .hero .btn:hover {
            background: #f7c400;
        }

        /* About Section */
        .about {
            background: white;
            padding: 50px 20px;
            text-align: center;
        }

        .about h2 {
            margin-bottom: 20px;
            color: #e54b4b;
        }

        .about p {
            font-size: 1rem;
            color: #666;
        }

        /* Skills Section */
        .skills {
            background: #e54b4b;
            color: white;
            padding: 50px 20px;
            text-align: center;
        }

        .skills h2 {
            margin-bottom: 20px;
        }

        .skill-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 20px;
        }

        .skill {
            background: white;
            color: #2575fc;
            padding: 15px;
            border-radius: 5px;
            font-weight: bold;
            text-transform: uppercase;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Contact Section */
        .contact {
            background: white;
            padding: 50px 20px;
            text-align: center;
        }

        .contact h2 {
            margin-bottom: 20px;
            color: #2575fc;
        }

        .contact .btn {
            background: #2575fc;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .contact .btn:hover {
            background: #6a11cb;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 20px;
            background: #333;
            color: white;
        }

        .db .connected {
            margin-bottom: 20px;
            color: #ffe600;
        }
    </style>
</head>

<body>
    <?php include 'db_connect.php'; ?>
    <header class="hero">
        <div class="container db">
            <p class="status connected"><?php echo $status; ?></p>
        </div>
        <div class="container">
            <h1>Assalamu Alaikkum, I'm <span class="highlight">Musab</span></h1>
            <p class="subtitle">A passionate Web Developer building interactive web experiences.</p>
            <a href="#about" class="btn">Learn More</a>
        </div>
    </header>

    <section id="about" class="about">
        <div class="container">
            <h2>About Me</h2>
            <p>
                I specialize in creating modern, responsive, and accessible websites.
                With a focus on user experience and clean code, I bring ideas to life.
            </p>
        </div>
    </section>

    <section id="skills" class="skills">
        <div class="container">
            <h2>My Skills</h2>
            <div class="skill-grid">
                <div class="skill">HTML</div>
                <div class="skill">CSS</div>
                <div class="skill">JavaScript</div>
                <div class="skill">React</div>
                <div class="skill">Node.js</div>
                <div class="skill">Flutter</div>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <h2>Contact Me</h2>
            <p>Feel free to reach out for collaboration or just a friendly chat.</p>
            <br>
            <a href="mailto:musab.dot@gmail.com" class="btn">Get in Touch</a>
        </div>
    </section>

    <footer class="footer">
        <p>&copy; 2024 Musab. All rights reserved.</p>
    </footer>
</body>

</html>