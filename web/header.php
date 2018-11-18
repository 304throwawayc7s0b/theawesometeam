<!DOCTYPE html>

<html>
<head>

    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" type="text/css" href="style_management.css">

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
            <a href="member_add.php">Add Membership</a>
            <a href="measurements_add.php">Add Member Measurements</a>
        </div>
    </li>
    <li class="dropdown header">
        <a href="class_add.php">Manage Classes</a>
        <div class="dropdown_list">
            <a href="class_add.php">Add Classes</a>
            <a href="classtype_add.php">Add New Class Type</a>
        </div>
    </li>

    <li class="dropdown header">
        <a href="reservation_add">Schedule Management</a>
    </li>

    <li class="dropdown header">
        <a href="Equipment_add.php">Equipment Management</a>
    </li>

    <li class="dropdown header">
        <a href="#">Customer View</a>
        <div class="dropdown_list">
            <a href="customer_reservation.php">Manage Reservation</a>
            <a href="customer_schedule.php">View Class Schedule</a>
            <a href="customer_fitness.php">View Fitness Measurements</a>
        </div>
    </li>

</ul>

</body>
</html>