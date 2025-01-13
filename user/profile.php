<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location:signin.php");
}
?>
<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>SkillSprint - Profile</title>
    <link rel="icon" href="../images/logo/house-cleaning.png" type="image/icon type">

    <link rel='stylesheet' href='nav.css'>
    <link rel="stylesheet" href="profile.css" />

</head>

<body>
    <nav>
        <div class='logo-container'>
            <img src='../images/logo/house-cleaning.png' alt='SkillSprint Logo' class='logo' style='z-index: 1' />
        </div>
        <a href='home.php' class='hovers'>Home</a>
        <a href='service.php' class='hovers'>Services</a>
        <a href='signout.php' class='hovers'>Sign Out</a>

        <div class='profile-icon'>
            <a href='profile.php'>
                <img src='../images/profile-user.png' alt='profile' class='profile' style='z-index: 1' />
            </a>
        </div>
    </nav>

    <?php
    include "../connect.php";
    if (!isset($_SESSION['user_id'])) {
        header("location:signin.php");
        exit();
    } else {
        $userId = $_SESSION['user_id'];
        $semail = $_SESSION['semail'];

        $qry = "SELECT * FROM users WHERE id=$userId AND email='$semail'";
        $qry1 = "SELECT * FROM workers WHERE id=$userId AND email='$semail'";

        $result = $con->query($qry);
        $result1 = $con->query($qry1);

        if ($result->num_rows > 0) {
            $row = mysqli_fetch_assoc($result);
            $id = $row['id'];
            $name = $row['username'];
            $email = $row['email'];
            $location = $row['location'];
            $phone = $row['phone'];
            echo "
            <center>
                <div class='container'>
                    <div class='img_container'> </div>
                    <img src='../images/Black-profile-user.png' alt='Profile Picture' class='profileImage'><a href='edit.php'><img src='../images/editIcon.png' class='edit'></a>
                    <p><span class='det'>Name: </span>" . $name . "</p>
                    <p><span class='det'>Email: </span>" . $email . "</p>
                    <p><span class='det'>Phone Number: </span>" . $phone . " </p>
                    <p><span class='det'>Location: </span>" . $location . "</p>

                    <div class='books'>
                    <h2>Bookings</h2>
                    <table border='1'>
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Location</th>
                        <th>Skill</th>
                        <th>Date and Time</th>
                        <th >Status</th>
                      </tr>
                    </thead>
            </center>";
            $getBooking = "SELECT * FROM bookings WHERE user_id=$id ORDER BY dateTime ASC";
            $result2 = $con->query($getBooking);

            if (mysqli_num_rows($result2) > 0) {
                // Loop through each row of data
                while ($row2 = mysqli_fetch_assoc($result2)) {
                    $workerId = $row2['worker_id'];
                    $status = $row2['status'];
                    $dnt = $row2['dateTime'];
                    $bid = $row2['id'];

                    $getUserDetail = "SELECT * FROM workers WHERE id=$workerId";
                    $result3 = $con->query($getUserDetail);
                    $row3 = mysqli_fetch_assoc($result3);
                    $wname = $row3['name'];
                    $wmail = $row3['email'];
                    $wphone = $row3['phone'];
                    $wlocation = $row3['service_area'];
                    $wskill = $row3['skill'];
                    echo "<tr>
                        <td> <a href='aboutWorker.php?id=$workerId'>$wname</a> </td>
                        <td>$wphone</td>
                        <td>$wmail</td>
                        <td>$wlocation </td>
                        <td>$wskill </td>
                        <td>$dnt </td>";
                    if ($status == 1) {
                        echo "<td class='red'>Declined</td>";
                    } else if ($status == 2) {
                        echo "<td class='green'>Approved</td>";
                    } else {
                        echo "
                            <td class='cancel'>
                            <button type='button' onclick='cancelB($bid, this.closest(\"tr\"))'>Cancel</button>
                            </td>";
                    }
                }
                echo "</tr></table></div>";
            } else {
                // No data found in the database
                echo "<tr><td colspan='7' style='padding:10px;'>---No Bookings---</td></tr>";
            }
        }
        // =============worker profile=================
        else if ($result1->num_rows > 0) {
            header("Location:Wprofile.php");
        }
    }

    ?>
    <script>
        function cancelB(bid, rowElement) {
            if (confirm("Are you sure you want to cancel the booking?")) {
                let ajax = new XMLHttpRequest();
                ajax.open("POST", "cancelB.php", true);
                ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                ajax.onreadystatechange = function() {
                    if (ajax.readyState === 4) {
                        if (ajax.status == 200) {
                            rowElement.remove();
                            let table = document.querySelector('table tbody');
                            if (table.rows.length === 0) {
                                let noBookingsRow = document.createElement('tr');
                                let noBookingsCell = document.createElement('td');
                                noBookingsCell.setAttribute('colspan', '7');
                                noBookingsCell.style.padding = '10px';
                                noBookingsCell.textContent = "---No Bookings---";
                                noBookingsRow.appendChild(noBookingsCell);
                                table.appendChild(noBookingsRow);
                            }
                        } else {
                            alert("An error occurred while cancelling the booking.");
                        }
                    }
                };
                ajax.send("bid=" + encodeURIComponent(bid));
            }
        }
    </script>
</body>

</html>