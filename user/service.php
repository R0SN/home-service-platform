<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SkillSprint - Skill Workers</title>
  <link rel="stylesheet" href="nav.css" />
  <link rel="icon" href="../images/logo/house-cleaning.png" type="image/icon type">
  <link rel="stylesheet" href="service.css" />
</head>

<body>
  <?php
  include "../connect.php";
  if (!isset($_SESSION['user_id'])) {
    echo "
  <!-- ------------------- NAVIGATION BAR ---------------------------- -->
  <nav style='position:fixed;'>
    <div class='logo-container'>
      <img src='../images\logo\house-cleaning.png' alt='SkillSprint Logo' class='logo' style='z-index: 1' />
    </div>
    <a href='home.php' class='hovers'>Home</a>
    <a href='service.php' class='hovers'>Services</a>
    <a href='apply.php' class='hovers'>Apply as a Worker</a>
    <a href='signin.php' class='hovers'>Sign In</a>
  </nav>
  <!-- ------------------- NAVIGATION BAR ---------------------------- -->";
  } else {
    echo "
  <!-- ------------------- NAVIGATION BAR ---------------------------- -->
  <nav style='position:fixed;'>
    <div class='logo-container'>
      <img src='../images\logo\house-cleaning.png' alt='SkillSprint Logo' class='logo' style='z-index: 1' />
    </div>
    <a href='home.php' class='hovers'>Home</a>
    <a href='service.php' class='hovers'>Services</a>
    <a href='signout.php' class='hovers'>Sign Out</a>

    <div class='profile-icon'>
      <a href='profile.php'> <img src='../images/profile-user.png' alt='profile' class='profile' style='z-index: 1' />
      </a>
    </div>
  </nav>
  <!-- ------------------- NAVIGATION BAR ------------------------------>";
  }

  ?>
  <!-- ------------------- SIDEBAR ---------------------------- -->
  <div class="sidebar">
    <h2>Filters</h2>
    <div>
      <input type="checkbox" id="plumber" name="filter" value="Plumber" onclick="filterskill()">
      <label for="plumber">Plumber</label>
    </div>
    <div>
      <input type="checkbox" id="painter" name="filter" value="Painter" onclick="filterskill()">
      <label for="painter">Painter</label>
    </div>
    <div>
      <input type="checkbox" id="carpenter" name="filter" value="Carpenter" onclick="filterskill()">
      <label for="carpenter">Carpenter</label>
    </div>
    <div>
      <input type="checkbox" id="electrician" name="filter" value="Electrician" onclick="filterskill()">
      <label for="electrician">Electrician</label>
    </div>
    <div>
      <input type="checkbox" id="interior design" name="filter" value="Interior designer" onclick="filterskill()">
      <label for="interior design">Interior Designer</label>
    </div>
    <div>
      <input type="checkbox" id="locksmith" name="filter" value="Locksmith" onclick="filterskill()">
      <label for="locksmith">Locksmith</label>
    </div>
    <div>
      <input type="checkbox" id="pest control operator" name="filter" value="Pest Control Operator" onclick="filterskill()">
      <label for="pest control operator">Pest control operator</label>
    </div>
    <div>
      <input type="checkbox" id="home inspector" name="filter" value="Home Inspector" onclick="filterskill()">
      <label for="home inspector">Home Inspector</label>
    </div>
  </div>
  <!-- ------------------- SIDEBAR ---------------------------- -->


  <div class="search-bar">
    <form method="GET" action="" id="searchForm">
      <input type="text" name="search" id="searchInput" placeholder="Search services..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
      <button type="submit" class="searchButton" id="searchButton">
      </button>
    </form>
  </div>

  <div class="main">
    <?php
    include "../connect.php";
    $searchQuery = "";
    if (isset($_GET['search'])) {
      $searchQuery = mysqli_real_escape_string($con, $_GET['search']);
    }
    $query = "SELECT * FROM workers";
    if (!empty($searchQuery)) {
      $query .= " WHERE name LIKE '%$searchQuery%' OR skill LIKE '%$searchQuery%' OR service_area LIKE '%$searchQuery%'";
    }
    $result = mysqli_query($con, $query);


    if (mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
        $photoPath = "../images/workers/photo/{$row['photo']}";
        $name = $row['name'];
        $phone = $row['phone'];
        $skill = $row['skill'];
        $sarea = $row['service_area'];
        $id = $row['id'];


        echo "<div class='card'>
        <center>
          <div class='image'><img src='$photoPath'></div>
        </center>
        <div class='content'>
          Name: $name<br>
          <span class='skill'>Skill: $skill</span><br>
          Service Area: $sarea<br>";

        if (!isset($_SESSION['user_id'])) {
          echo "
        <a href='signin.php'><button type='submit' name='about'>more</button></a>
        </div>
      </div>";
        } else {
          echo "<a href='aboutWorker.php?id=$id'>More</a>
          </div>
        </div>";
        }
      }
    } 

    // Close the database connection
    mysqli_close($con);
    ?>
  </div>
  <script>
    function filterskill() {
      var checkboxes = document.getElementsByName('filter');
      var selectedStatus = [];
      for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i].checked) {
          selectedStatus.push(checkboxes[i].value);
        }
      }

      var workers = document.getElementsByClassName('card');
      var found = false; // Flag to track if any workers are found
      for (var i = 0; i < workers.length; i++) {
        var skillElement = workers[i].querySelector('.skill');
        var skill = skillElement.textContent.split(': ')[1].trim();
        if (selectedStatus.length === 0 || selectedStatus.includes(skill)) {
          workers[i].style.display = 'block';
          found = true; // At least one worker is found
        } else {
          workers[i].style.display = 'none';
        }
      }

      // Remove "No workers found" message if workers are found
      var noWorkersMessage = document.querySelector('.no-workers-found');
      if (!found) {
        // Display "No workers found" message if no workers are found
        if (!noWorkersMessage) {
          noWorkersMessage = document.createElement('p');
          noWorkersMessage.textContent = 'No workers found';
          noWorkersMessage.className = 'no-workers-found';
          document.body.appendChild(noWorkersMessage);
          document.body.style.textAlign = "center";
        }
      } else {
        // Remove "No workers found" message if workers are found
        if (noWorkersMessage) {
          noWorkersMessage.remove();
        }
      }
    }
  </script>
</body>

</html>