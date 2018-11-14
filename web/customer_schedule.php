<!DOCTYPE html>
<?php include 'header.php';?>
<html>
<body>

<?php
// define variables and set to empty values
$name = $phone = "";
?>


<div class="column content intro">
  <h3>Find your schedule</h3>
  <form method="POST" action="customer_schedule.php">
    <label for="name">Name:</label> <input type="text" name="name" value="<?php echo $name;?>">
    <label for="phone">Phone:</label> <input type="text" name="phone" value="<?php echo $phone;?>">
    <p><input type="submit" value="Search" name="search"></p>
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
  echo "<br><h4>Your Schedule: </h4><br>";
  echo "<table>";
  echo "<tr><th>Room</th><th>Start Time</th><th>End Time</th><th>Duration</th><th>Description</th></tr>";
  while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
    $i++;
    echo "<tr><td>" . $row["ROOM"] . "</td><td>" . $row["STARTTIME"] . "</td><td>" . $row["ENDTIME"] . "</td><td>" . $row["DURATION"] . "</td><td>" . $row["DESCRIPTION"] . "</td></tr>"; //or just use "echo $row[0]"
  }
  echo "</table>";
  if ($i == 0) {
    echo "<br><h4>No Schedule Found.</h4><br>";
  }
}

// Connect Oracle...
if ($db_conn) {

  if (array_key_exists('search', $_POST)) {
    $tuple = array (
      ":bind1" => $_POST['name'],
      ":bind2" => $_POST['phone']
    );
    $alltuples = array (
      $tuple
    );

    // select c.Duration, c.room, c.StartTime, c.EndTime, ct.description from reservation r, class c, customer cu, classtype ct where cu.name = :bind1 AND cu.phone=:bind2 AND cu.CustomerID=r.CustomerID AND r.classid=c.classid AND c.ClassTypeID=ct.ClassTypeID
    $result = executeBoundSQL("select c.Duration, c.room, c.StartTime, c.EndTime, ct.description from reservation r, class c, customer cu, classtype ct where cu.name = :bind1 AND cu.phone=:bind2 AND cu.CustomerID=r.CustomerID AND r.classid=c.classid AND c.ClassTypeID=ct.ClassTypeID", $alltuples);
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

