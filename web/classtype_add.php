<!DOCTYPE html>
<?php include 'header.php';?>

<html>

<head>

<link rel="stylesheet" type="text/css" href="style.css">

</head>
<body>
<ul class="header">
    <li class="dropdown header">
        <a href="member_mng.php">Membership Management</a>
        <div class="dropdown_list">
            <a href="member_delete.php">View Members</a>
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
  <?php
    // define variables and set to empty values
    $nameErr = $emailErr = $genderErr = $websiteErr = "";
    $name = $email = $gender = $phoneNumber = "";
  ?>


  <div class="column content">
    <h3>Add Class Type</h3>
    <label><span class="error">* required</span></label>
    <form method="post" action="classtype_add.php">  
      <label for="name">ClassTypeID:</label> <input type="text" name="ClassTypeID">
      <br><br>
      <label for="name">Description:</label> 
        <input type="text" name="Description">
      <br><br>
      <label for="name">HrRate:</label>
        <input type="text" name="HrRate">
      <br><br>
      <label for="name">Features:</label>
      <input type="text" name="Features"> 
      <br><br>
      <div class="button">
        <input type="submit" name="typesubmit" value="Submit">  
        </div>
      
    </form>

  </div>

<?php

//this tells the system that it's no longer just parsing 
//html; it's now parsing PHP

$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_u2m0b", "a38920154", "dbhost.ugrad.cs.ubc.ca:1522/ug");

function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
  //echo "<br>running ".$cmdstr."<br>";
  global $db_conn, $success;
  $statement = OCIParse($db_conn, $cmdstr); //There is a set of comments at the end of the file that describe some of the OCI specific functions and how they work

  if (!$statement) {
    echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
    $e = OCI_Error($db_conn); // For OCIParse errors pass the       
    // connection handle
    echo htmlentities($e['message']);
    $success = False;
  }

  $r = OCIExecute($statement, OCI_DEFAULT);
  if (!$r) {
    echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
    $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
    echo htmlentities($e['message']);
    $success = False;
  } else {

  }
  return $statement;

}

function executeBoundSQL($cmdstr, $list) {
  /* Sometimes the same statement will be executed for several times ... only
   the value of variables need to be changed.
   In this case, you don't need to create the statement several times; 
   using bind variables can make the statement be shared and just parsed once.
   This is also very useful in protecting against SQL injection.  
      See the sample code below for how this functions is used */

  global $db_conn, $success;
  $statement = OCIParse($db_conn, $cmdstr);

  if (!$statement) {
    echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
    $e = OCI_Error($db_conn);
    echo htmlentities($e['message']);
    $success = False;
  }

  foreach ($list as $tuple) {
    foreach ($tuple as $bind => $val) {
      //echo $val;
      //echo "<br>".$bind."<br>";
      OCIBindByName($statement, $bind, $val);
      unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype

    }
    $r = OCIExecute($statement, OCI_DEFAULT);
    if (!$r) {
      echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
      $e = OCI_Error($statement); // For OCIExecute errors pass the statement handle
      echo htmlentities($e['message']);
      echo "<br>";
      $success = False;
    }
  }

}

function printResult($result) { //prints results from a select statement
  echo "<br>Got data from table ClassType:<br>";
  echo "<table>";
  echo "<tr><th>ID</th><th>Description</th><th>Hour Rate</th><th>Features</th></tr>";

  while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
    echo "<tr><td>" . $row[0] . "</td><td>" . $row["DESCRIPTION"] . "</td><td>" . $row["HRRATE"] . "</td><td>" . $row["FEATURES"] . "</td></tr>"; //or just use "echo $row[0]" 
  }
  echo "</table>";

}

// Connect Oracle...
if ($db_conn) {

  if (array_key_exists('typesubmit', $_POST)) {
      //Getting the values from user and insert data into the table
      $tuple = array (
        ":bind1" => $_POST['ClassTypeID'],
        ":bind2" => $_POST['Description'],
        ":bind3" => $_POST['HrRate'],
        ":bind4" => $_POST['Features']
      );
      $alltuples = array (
        $tuple
      );
      executeBoundSQL("insert into ClassType values (:bind1, :bind2, :bind3, :bind4)", $alltuples);
      OCICommit($db_conn);

    } 

  if ($_POST && $success) {
    //POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
    header("location: classtype_add.php");
  } else {
    // Select data...
    $result = executePlainSQL("select * from ClassType");
    printResult($result);
  }

  //Commit to save changes...
  OCILogoff($db_conn);
} else {
  echo "cannot connect";
  $e = OCI_Error(); // For OCILogon errors pass no handle
  echo htmlentities($e['message']);
}

?>



</body>

</html>

