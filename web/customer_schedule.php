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
    //Getting the values from user and insert data into the table
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
    // select c.Duration, c.room, c.StartTime, c.EndTime, ct.description from customer cu inner join reservation r on cu.CustomerID=r.CustomerID inner join class c on r.classid=c.classid inner join classtype ct on c.ClassTypeID=ct.ClassTypeID where cu.name ='Sarina' AND cu.phone='778-319-3333';
    // $result = executePlainSQL("select c.Duration, c.room, c.StartTime, c.EndTime, ct.description from customer cu inner join reservation r on cu.CustomerID=r.CustomerID inner join class c on r.classid=c.classid inner join classtype ct on c.ClassTypeID=ct.ClassTypeID where cu.name ='Sarina' AND cu.phone='778-319-3333'");
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

