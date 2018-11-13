
<!DOCTYPE html>
<html>
<body>

<?php include 'header.php';?>

<?php
// define variables and set to empty values
$confIdErr = "";
?>

<div class="column content">
  <h3>Enter your confirmation ID to find reservation:</h3>
  <label><span class="error">* required</span></label>
  <br><br>
  <form method="post" action="customer_reservation.php">
    <label for="confId">Confirmation ID:</label> <input type="text" name="confId" value="<?php echo $confId; ?>">
    <span class="error">* <?php echo $confIdErr; ?></span>
    <br><br>
    <div class="button">
      <input type="submit" name="submit" value="Submit">
    </div>

  </form>

</div>

<?php

$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_u2m0b", "a38920154", "dbhost.ugrad.cs.ubc.ca:1522/ug");

function printResult($result) { //prints results from a select statement
  echo "<br>Got data from table Reservation:<br>";
  echo "<table>";
  echo "<tr><th>Location</th><th>City</th><th>ClassId</th></tr>";

  while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
    echo "<tr><td>" . $row["location"] . "</td><td>" . $row["city"] . "</td><td>" . $row["classid"] . "</td></tr>"; //or just use "echo $row[0]"
  }
  echo "</table>";

}
function executePlainSQL($cmdstr)
{ //takes a plain (no bound variables) SQL command and executes it
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

function executeBoundSQL($cmdstr, $list)
{
  /* Sometimes the same statement will be executed for several times ... only
   the value of variables need to be changed.
   In this case, you don't need to create the statement several times;
   using bind variables can make the statement be shared and just parsed once.
   This is also very useful in protecting against SQL injection.
      See the sample code below for how this functions is used */
  echo "<br>executing " . $cmdstr . "<br>";
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
  return $statement;

}
// Connect Oracle...
if ($db_conn) {

  if (array_key_exists('submit', $_POST)) {
    echo "In post";
    //Getting the values from user and insert data into the table
    $tuple = array(
      ":bind1" => $_POST['confId'],
    );
    $alltuples = array(
      $tuple
    );
    $result = executeBoundSQL("select location, city, classid from reservation where customerid=:bind1", $alltuples);
    OCICommit($db_conn);
    printResult($result);

  }

  if ($_POST && $success) {
    //POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
    header("location: customer_reservation.php");
  } else {
    echo "Failed to retrieve data from database";
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
