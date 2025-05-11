<?php
    session_start();
    include '../db_connect.php'; 
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home | MMU Food</title>
    <link rel="stylesheet" href="../css/styledesign.css">
    <link rel="stylesheet" href="../css/slideshow.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <!-- header section -->
    <?php include '../header_customer.php'; ?>

    <!-- main content -->
    <section class="hero">
        <div class="hero-slider slideshow-container">
            <div class="slide mySlides fade">
                <div class="image">
                    <img src="../img/an-1.jpg" alt="">
                </div>
            </div>

            <div class="slide mySlides fade">
                <div class="image">
                    <img src="../img/an-2.jpg" alt="">
                </div>
            </div>

            <div class="slide mySlides fade">
                <div class="image">
                    <img src="../img/an-3.jpg" alt="">
                </div>
            </div>

            <div class="slide mySlides fade">
                <div class="image">
                    <img src="../img/an-4.jpg" alt="">
                </div>
            </div>

            <!-- Next and previous buttons -->
            <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
            <a class="next" onclick="plusSlides(1)">&#10095;</a>
        </div>
        <br>

        <!-- The dots/circles -->
        <div style="text-align:center">
            <span class="dot" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
            <span class="dot" onclick="currentSlide(3)"></span>
            <span class="dot" onclick="currentSlide(4)"></span>
        </div>
    </section>
    
    <!-- category section -->
    <section class="category">
      <h1 class="title">Food Category</h1>
      <div class="box-container">
         <a href="foodcatalogue.php" class="box">
            <img src="../img/cat-1.jpg" alt="">
            <h3>Local</h3>
         </a>

         <a href="foodcatalogue.php" class="box">
            <img src="../img/cat-2.jpg" alt="">
            <h3>Western</h3>
         </a>

         <a href="foodcatalogue.php" class="box">
            <img src="../img/cat-3.jpg" alt="">
            <h3>Korean</h3>
         </a>

         <a href="foodcatalogue.php" class="box">
            <img src="../img/cat-4.jpg" alt="">
            <h3>Japanese</h3>
         </a>
      </div>
    </section>

    <!-- footer section -->
    <?php include '../footer.php'; ?>
    
    <script src="../js/script.js"></script>
    <script src="../js/slideshow.js"></script>

</body>
</html>
