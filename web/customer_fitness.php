<!DOCTYPE html>
<?php include 'header.php';?>
<html>
<body>
<div class="welcome">
  <span class="welcome">CPSC304 Team 5</span>
  <span class="welcome" style="font-size: 90%; padding:0px 10px 15px 100px;">
   Friendship Gym
  </span>
</div>

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
$custId = "";
?>


<div class="column content intro">
  <h3>View fitness profile:</h3>
  <form method="POST" action="customer_fitness.php">
    <p><label for="custId">Customer ID:</label> <input type="text" name="custId" value="<?php echo $custId;?>"></p>
    <p><input type="submit" value="View Profile" name="view"></p>
  </form>
</div>


<?php
$success = True;
$db_conn = OCILogon("ora_u2m0b", "a38920154", "dbhost.ugrad.cs.ubc.ca:1522/ug");

function executePlainSQL($cmdstr) {
  global $db_conn, $success;
  $statement = OCIParse($db_conn, $cmdstr);

  if (!$statement) {
    echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
    $e = OCI_Error($db_conn);
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
    return $statement;
  }

}

function printResult($result) { //prints results from a select statement
  $i = 0;
  echo "<br><h4>Fitness profile: </h4><br>";
  echo "<table>";
  echo "<tr><th>Date</th><th>Height</th><th>Weight</th><th>Body Fat(%)</th><th>Water %</th><th>Muscle Mass(%)</th></tr>";
  while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
    $i++;
    echo "<tr><td>" . $row["STARTDATE"] . "</td><td>" . $row["HEIGHT"] . "</td><td>" . $row["WEIGHT"] . "</td><td>" . $row["BODYFAT"] . "</td><td>" . $row["WATER"] . "</td><td>" . $row["MUSCLEMASS"] . "</td></tr>"; //or just use "echo $row[0]"
  }
  echo "</table>";
  if ($i == 0) {
    echo "<br><h4>No Profile Found.</h4><br>";
  }
}

// Connect Oracle...
if ($db_conn) {

  if(array_key_exists('view', $_POST)){
    $tuple = array (
      ":bind1" => $_POST['custId']
    );
    $alltuples = array (
      $tuple
    );

    $result = executeBoundSQL("select fm.height, fm.startDate, fm.weight, fm.bodyfat, fm.water, fm.musclemass FROM FitnessMeasurement fm WHERE fm.CustomerID=:bind1", $alltuples);
    OCICommit($db_conn);
    printResult($result);

  } else {
    //Commit to save changes...
    OCILogoff($db_conn);
  }
} else {
  echo "cannot connect";
  $e = OCI_Error(); // For OCILogon errors pass no handle
  echo htmlentities($e['message']);
}

?>



</body>

</html>

