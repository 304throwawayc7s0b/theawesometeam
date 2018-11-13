<!DOCTYPE html>

<html>

<head>

  <link rel="stylesheet" type="text/css" href="style.css">

</head>
<body>

<div class="welcome">
  <span class="welcome">CPSC304 Team 5</span>
  <span class="welcome" style="font-size: 90%; padding:0px 10px 15px 100px;">
   Friendship Gym
  </span>
</div>

<ul class="header">
  <li class="dropdown header">
    <a href="member_add.php">Membership Management</a>
    <div class="dropdown_list">
      <a href="member_delete.php">View Members</a>
      <a href="member_add.php">Add Membership</a>
    </div>
  </li>

  <li class="dropdown header">
    <a href="schedule_mng.html">Schedule Management</a>
    <div class="dropdown_list">
      <a href="schedule_mng.html">View</a>
      <a href="schedule_mng.html">Modify</a>
    </div>
  </li>

  <li class="dropdown header">
    <a href="equip_mng.html">Equipment Management</a>
    <div class="dropdown_list">
      <a href="equip_mng.html">View</a>
      <a href="equip_mng.html">Modify</a>
    </div>
  </li>

  <li class="dropdown header">
    <a href="#">Customer View</a>
    <div class="dropdown_list">
      <a href="customer_reservation.php">Manage Reservation</a>
      <a href="#">View Class Schedule</a>
      <a href="#">View Fitness Measurements</a>
    </div>
  </li>
</ul>

<?php
$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_u2m0b", "a38920154", "dbhost.ugrad.cs.ubc.ca:1522/ug");
?>

</body>
</html>