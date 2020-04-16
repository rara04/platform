<?php
 $server_name = 'localhost';
 $username = 'root';
 $password = '';
 $dbname = "platformDB";

 $user_ip =  $_SERVER['REMOTE_ADDR'];
 $requested_page = $_SERVER['SCRIPT_NAME'];
 //$last_entrance = ('Y-m-d H:i:s');
 preg_match('/(MSIE|(?!Gecko.+)Firefox|(?!AppleWebKit.+Chrome.+)Safari|(?!AppleWebKit.+)Chrome|AppleWebKit(?!.+Chrome|.+Safari)|Gecko(?!.+Firefox))(?: |\/)([\d\.apre]+)/',$_SERVER['HTTP_USER_AGENT'], $user_agent_array);
 $browser_info = $user_agent_array[0];

 preg_match('/(?:\(Windows)(?:[^\(]*)(?:\))/', $_SERVER['HTTP_USER_AGENT'], $matches);
 $os_info = $matches[0];
 /*/(?:\()(?:[^\(]*)(?:\))/ generic (?:\(Windows)(?:[^\(]*)(?:\)) only for windows os name and version regex*/

 
 // Create connection
 $conn = new mysqli($server_name, $username, $password, $dbname, 3308);
 // Check connection
 if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
 }
// automatically deletes all users no longer blocked after 5 minutes
 $delete_block = "DELETE FROM Block WHERE block_time<=DATE_SUB(NOW(), INTERVAL 5 MINUTE)";
 mysqli_query($conn, $delete_block);

//check if a user is blocked before activating the site
 $select_block = "SELECT * FROM Block WHERE user_ip='$user_ip'";
 $check_block = mysqli_query($conn, $select_block);
 if (mysqli_num_rows($check_block) > 0) {
    die("429 Too many requests, you are temporary blocked");
  }
 
 //check acceptable browser
 if (strpos($browser_info, 'Chrome') == TRUE and (int)$browser_info[8] < 7) {
    die("406 Unacceptable browser. Chrome/70 and up");
  }
//opening log file
 $logfile = fopen("log.txt", "a");


 function addRecord($sql_statement, $connection, $logsfile) {
  if ($connection->query($sql_statement) === TRUE) {
      $txt = "New record created successfully";
  } else {
    $txt = "Error: " . $sql_statement . "<br>" . $connection->error;
  }
  fwrite($logsfile, $txt);
 }

 /* dos table handaling*/
 $insert_dos = "INSERT INTO dosTBL (user_ip, page, last_entrance) VALUES ('$user_ip', '$requested_page', NOW())";
 addRecord($insert_dos, $conn, $logfile);
 
 // check if too many requests were made and block
 $select_dos = "SELECT * FROM dosTBL WHERE user_ip='$user_ip' AND DATE_ADD(last_entrance, INTERVAL 1 MINUTE) >= NOW()";
 $check_result = mysqli_query($conn, $select_dos);
 if (mysqli_num_rows($check_result) >= 5) {

  $block_insert = "INSERT INTO Block (user_ip, block_time) VALUES ('$user_ip', NOW())";
  addRecord($block_insert, $conn, $logfile);
  die("429 Too many requests, you are temporary blocked");
 }
// entrance table handaling
 $select_sql = "SELECT user_ip FROM entrenceTBL WHERE user_ip='$user_ip'";
 $result = mysqli_query($conn, $select_sql);

 if (mysqli_num_rows($result) > 0) {
  $sql = "UPDATE entrenceTBL SET page='$requested_page', browser_info='$browser_info', os_info='$os_info' WHERE user_ip='$user_ip'";

 if (mysqli_query($conn, $sql)) {
    fwrite($logfile, "Record updated successfully");
 } else {
    $txt = "Error updating record: " . mysqli_error($conn);
    fwrite($logfile, $txt);
 }
 } else {
    $insert_entrance = "INSERT INTO entrenceTBL (page, user_ip, browser_info, os_info) VALUES ('$requested_page', '$user_ip', '$browser_info', '$os_info')";

    addRecord($insert_entrance, $conn, $logfile);
    
 }
 fclose($logfile);
 $conn->close();
 
 ?>

<!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Business Casual - Start Bootstrap Theme</title>

  <!-- Bootstrap core CSS -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom fonts for this template -->
  <link href="https://fonts.googleapis.com/css?family=Raleway:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Lora:400,400i,700,700i" rel="stylesheet">

  <!-- Custom styles for this template -->
  <link href="css/business-casual.min.css" rel="stylesheet">

</head>

<body>

  <h1 class="site-heading text-center text-white d-none d-lg-block">
    <span class="site-heading-upper text-primary mb-3">A Free Bootstrap 4 Business Theme</span>
    <span class="site-heading-lower">Business Casual</span>
  </h1>

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-dark py-lg-4" id="mainNav">
    <div class="container">
      <a class="navbar-brand text-uppercase text-expanded font-weight-bold d-lg-none" href="#">Start Bootstrap</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav mx-auto">
          <li class="nav-item px-lg-4">
            <a class="nav-link text-uppercase text-expanded" href="index.html">Home
              <span class="sr-only">(current)</span>
            </a>
          </li>
          <li class="nav-item px-lg-4">
            <a class="nav-link text-uppercase text-expanded" href="about.html">About</a>
          </li>
          <li class="nav-item active px-lg-4">
            <a class="nav-link text-uppercase text-expanded" href="products.html">Products</a>
          </li>
          <li class="nav-item px-lg-4">
            <a class="nav-link text-uppercase text-expanded" href="store.html">Store</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <section class="page-section">
    <div class="container">
      <div class="product-item">
        <div class="product-item-title d-flex">
          <div class="bg-faded p-5 d-flex ml-auto rounded">
            <h2 class="section-heading mb-0">
              <span class="section-heading-upper">Blended to Perfection</span>
              <span class="section-heading-lower">Coffees &amp; Teas</span>
            </h2>
          </div>
        </div>
        <img class="product-item-img mx-auto d-flex rounded img-fluid mb-3 mb-lg-0" src="img/products-01.jpg" alt="">
        <div class="product-item-description d-flex mr-auto">
          <div class="bg-faded p-5 rounded">
            <p class="mb-0">We take pride in our work, and it shows. Every time you order a beverage from us, we guarantee that it will be an experience worth having. Whether it's our world famous Venezuelan Cappuccino, a refreshing iced herbal tea, or something as simple as a cup of speciality sourced black coffee, you will be coming back for more.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="page-section">
    <div class="container">
      <div class="product-item">
        <div class="product-item-title d-flex">
          <div class="bg-faded p-5 d-flex mr-auto rounded">
            <h2 class="section-heading mb-0">
              <span class="section-heading-upper">Delicious Treats, Good Eats</span>
              <span class="section-heading-lower">Bakery &amp; Kitchen</span>
            </h2>
          </div>
        </div>
        <img class="product-item-img mx-auto d-flex rounded img-fluid mb-3 mb-lg-0" src="img/products-02.jpg" alt="">
        <div class="product-item-description d-flex ml-auto">
          <div class="bg-faded p-5 rounded">
            <p class="mb-0">Our seasonal menu features delicious snacks, baked goods, and even full meals perfect for breakfast or lunchtime. We source our ingredients from local, oragnic farms whenever possible, alongside premium vendors for specialty goods.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="page-section">
    <div class="container">
      <div class="product-item">
        <div class="product-item-title d-flex">
          <div class="bg-faded p-5 d-flex ml-auto rounded">
            <h2 class="section-heading mb-0">
              <span class="section-heading-upper">From Around the World</span>
              <span class="section-heading-lower">Bulk Speciality Blends</span>
            </h2>
          </div>
        </div>
        <img class="product-item-img mx-auto d-flex rounded img-fluid mb-3 mb-lg-0" src="img/products-03.jpg" alt="">
        <div class="product-item-description d-flex mr-auto">
          <div class="bg-faded p-5 rounded">
            <p class="mb-0">Travelling the world for the very best quality coffee is something take pride in. When you visit us, you'll always find new blends from around the world, mainly from regions in Central and South America. We sell our blends in smaller to large bulk quantities. Please visit us in person for more details.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <footer class="footer text-faded text-center py-5">
    <div class="container">
      <p class="m-0 small">Copyright &copy; Your Website 2019</p>
    </div>
  </footer>

  <!-- Bootstrap core JavaScript -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
 
</body>

</html>
