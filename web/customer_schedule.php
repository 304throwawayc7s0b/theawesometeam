<!DOCTYPE html>
<?php include 'header.php';?>
<html>
<body>
  
<?php
// define variables and set to empty values
$custId = "";
?>


<div class="column content intro">
  <h3>Find your schedule</h3>
  <form method="POST" action="customer_schedule.php">
      <p><label for="custId">Customer ID:</label> <input type="text" name="custId" value="<?php echo $custId;?>"></p>
    <p><input type="submit" value="Search" name="search"></p>
  </form>

    <h3>Explore classes</h3>
    <form method="POST" action="customer_schedule.php">
        <p>View number & type of classes offered:</p>
        <p><input type="submit" value="Explore" name="explore"></p>
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

function printClassResult($result) { //prints class results from view
  echo "<br><h4>Classes Offered: </h4><br>";
  echo "<table>";
  echo "<tr><th>Class Description</th><th>Hourly rate</th><th># of classes offered</th></tr>";
  while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
    echo "<tr><td>" . $row["DESCRIPTION"] . "</td><td>$" . $row["HRRATE"] . "</td><td>" . $row["COUNT"] . "</td></tr>";
  }
  echo "</table>";
}

// Connect Oracle...
if ($db_conn) {

  if (array_key_exists('search', $_POST)) {
    $tuple = array (
      ":bind1" => $_POST['custId']
    );
    $alltuples = array (
      $tuple
    );

    // select c.Duration, c.room, c.StartTime, c.EndTime, ct.description from reservation r, class c, customer cu, classtype ct where cu.name = :bind1 AND cu.phone=:bind2 AND cu.CustomerID=r.CustomerID AND r.classid=c.classid AND c.ClassTypeID=ct.ClassTypeID
    $result = executeBoundSQL("select c.Duration, c.room, c.StartTime, c.EndTime, ct.description from reservation r, class c, classtype ct where r.CustomerID=:bind1 AND r.classid=c.classid AND c.ClassTypeID=ct.ClassTypeID", $alltuples);
    OCICommit($db_conn);
    printResult($result);
  } else if(array_key_exists('explore', $_POST)) {
    $result = executePlainSQL("select * from class_count_view");
    printClassResult($result);
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
