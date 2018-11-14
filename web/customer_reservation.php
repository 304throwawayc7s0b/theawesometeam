<!DOCTYPE html>
<?php include 'header.php';?>
<html>
<body>

<?php
// define variables and set to empty values
$confirmation = "";
?>

<div class="column content intro">
    <h3>Delete Reservation</h3>
    <form method="POST" action="customer_reservation.php">
        <label for="confirmation">Confirmation #:</label> <input type="text" name="confirmation" value="<?php echo $confirmation;?>">
        <p><input type="submit" value="Delete" name="delete"></p>
    </form>
  <h3>Search Reservation</h3>
  <form method="POST" action="customer_reservation.php">
    <label for="confirmation">Confirmation #:</label> <input type="text" name="confirmation" value="<?php echo $confirmation;?>">
    <p><input type="submit" value="Search" name="search"></p>
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

function printResult($result, $conf) { //prints results from a select statement
    $i = 0;
    echo "<br><h4>Reservation for confirmation #" . $conf .": </h4><br>";
    echo "<table>";
    echo "<tr><th>Location</th><th>City</th><th>Room</th><th>Start Time</th><th>End Time</th><th>Instructor</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
      $i++;
      echo "<tr><td>" . $row["LOCATION"] . "</td><td>" . $row["CITY"] . "</td><td>" . $row["ROOM"] . "</td><td>" . $row["STARTTIME"] . "</td><td>" . $row["ENDTIME"] . "</td><td>" . $row["FIRSTNAME"] ." ". $row["LASTNAME"] . "</td></tr>"; //or just use "echo $row[0]"
    }
    echo "</table>";
    if ($i == 0) {
        echo "<br><h4>No Reservation Found.</h4><br>";
    }
}

// Connect Oracle...
if ($db_conn) {

  if (array_key_exists('search', $_POST)) {
    $tuple = array (
      ":bind1" => $_POST['confirmation']
    );
    $alltuples = array (
      $tuple
    );

    $result = executeBoundSQL("select r.location, r.city, c.room, c.StartTime, c.EndTime, i.FirstName, i.LastName from reservation r, class c, instructor i where confirmation=:bind1 AND r.classid=c.classid AND c.InstructorID=i.InstructorID", $alltuples);
    OCICommit($db_conn);

    printResult($result, $_POST['confirmation']);
  } else if(array_key_exists('delete', $_POST)) {
    $tuple = array (
      ":bind1" => $_POST['confirmation']
    );
    $alltuples = array (
      $tuple
    );
    executeBoundSQL("delete from reservation where confirmation=:bind1", $alltuples);
    OCICommit($db_conn);
    echo "Reservation successfully deleted.";

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

