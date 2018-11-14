<!DOCTYPE html>
<?php include 'header.php';?>

<html>

<head>

<link rel="stylesheet" type="text/css" href="style.css">

</head>
<body>

  <?php
    // define variables and set to empty values
    $nameErr = $emailErr = $genderErr = $websiteErr = "";
    $name = $email = $gender = $phoneNumber = "";
  ?>


  <div class="column content">
    <h3>Add Class</h3>
    <label><span class="error">* required</span></label>
    <form method="post" action="class_add.php">  
      <label for="name">Class ID:</label> 
        <input type="text" name="ClassID">
      <br><br>
      <label for="name">Duration:</label> 
        <input type="text" name="Duration">
      <br><br>
      <label for="name">Total Fee:</label>
        <input type="text" name="TotalFee">
    <br><br>
      <label for="name">Room:</label>
        <input type="text" name="Room">
      <br><br>
      <label for="name">Instructor's ID:</label>
        <input type="text" name="InstructorID"> 
      <br><br>
        <label for="name">Start Date:</label>
      <input type="text" name="StartDate"> 
      <br><br>
        <label for="name">End Date:</label>
        <input type="text" name="EndDate"> 
      <br><br>
        <label for="name">Start Time:</label>
      <input type="text" name="StartTime"> 
      <br><br>
        <label for="name">End Time:</label>
      <input type="text" name="EndTime"> 
      <br><br>
        <label for="name">ClassTypeID:</label>
      <input type="text" name="ClassTypeID"> 
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
$db_conn = OCILogon("ora_e8m2b", "a75788745", "dbhost.ugrad.cs.ubc.ca:1522/ug");

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
  echo "<br>Got data from table Class:<br>";
  echo "<table>";
  echo "<tr>
      <th>Class ID</th>
      <th>Duration</th>
      <th>Total Fee</th>
      <th>Room</th>
      <th>Instructor's ID</th>
      <th>Start Date</th>
      <th>End Date</th>
      <th>Start Time</th>
      <th>End Time</th>
      <th>ClassTypeID</th>
  </tr>";

  while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
    echo "<tr><td>" .  $row[0] . "</td><td>" .  $row[1] . "</td><td>".  $row[2] . "</td><td>" .  $row[3] . "</td><td>" .  $row[4] . "</td><td>" .  $row[5] . "</td><td>" .  $row[6] . "</td><td>" .  $row[7] . "</td><td>" .  $row[8] . "</td><td>" .  $row[9] . "</td></tr>"; //or just use "echo $row[0]" 
  }
  echo "</table>";

}

// Connect Oracle...
if ($db_conn) {

  if (array_key_exists('typesubmit', $_POST)) {
      //Getting the values from user and insert data into the table
      $tuple = array (
        ":bind1" => $_POST['ClassID'],
        ":bind2" => $_POST['Duration'],
        ":bind3" => $_POST['TotalFee'],
        ":bind4" => $_POST['Room'],
        ":bind5" => $_POST['InstructorID'],
        ":bind6" => $_POST['StartDate'],
        ":bind7" => $_POST['EndDate'],
        ":bind8" => $_POST['StartTime'],
        ":bind9" => $_POST['EndTime'],
        ":bind10" => $_POST['ClassTypeID']
      );
      $alltuples = array (
        $tuple
      );
      executeBoundSQL("insert into Class values (:bind1, :bind2, :bind3, :bind4, :bind5, :bind6, :bind7, :bind8, :bind9, :bind10)", $alltuples);
      OCICommit($db_conn);

    } 

  if ($_POST && $success) {
    //POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
    header("location: class_add.php");
  } else {
    // Select data...
    $result = executePlainSQL("select * from Class");
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

