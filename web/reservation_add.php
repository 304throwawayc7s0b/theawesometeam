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
    <h3>Add Reservation</h3>
    <label><span class="error">* required</span></label>
    <form method="post" action="reservation_add.php">  
      <label for="name">Location:</label> 
        <input type="text" name="Location">
      <br><br>
      <label for="name">City:</label> 
        <input type="text" name="City">
      <br><br>
      <label for="name">Class ID:</label>
        <input type="text" name="ClassID">
    <br><br>
      <label for="name">Customer ID:</label>
        <input type="text" name="CustomerID"> 
      <br><br>
        <label for="name">Confirmation #:</label>
      <input type="text" name="Confirmation"> 
      <br><br>
        <label for="name">Credit Card:</label>
        <input type="text" name="CreditCard"> 
      <br><br>
        <label for="name">CancelationFee:</label>
      <input type="text" name="CancelationFee"> 
      <br><br>
        <label for="name">CreatedTime:</label>
      <input type="text" name="CreatedTime"> 
      <br><br>
        <label for="name">CreatedDate:</label>
      <input type="text" name="CreatedDate"> 
      <br><br>
        <label for="name">Discount Code:</label>
      <input type="text" name="DiscountCode"> 
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
  echo "<br>Got data from table Reservation:<br>";
  echo "<table>";
  echo "<tr>
      <th>Location</th>
      <th>City</th>
      <th>Class ID</th>
      <th>Customer ID</th>
      <th>Confirmation #</th>
      <th>Credit Card</th>
      <th>Cancelation Fee</th>
      <th>Created Time</th>
      <th>Created Date</th>
      <th>DiscountCode</th>
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
        ":bind1" => $_POST['Location'],
        ":bind2" => $_POST['City'],
        ":bind3" => $_POST['ClassID'],
        ":bind4" => $_POST['CustomerID'],
        ":bind5" => $_POST['Confirmation'],
        ":bind6" => $_POST['CreditCard'],
        ":bind7" => $_POST['CancelationFee'],
        ":bind8" => $_POST['CreatedTime'],
        ":bind9" => $_POST['CreatedDate'],
        ":bind10" => $_POST['DiscountCode']
      );
      $alltuples = array (
        $tuple
      );
      executeBoundSQL("insert into Reservation values (:bind1, :bind2, :bind3, :bind4, :bind5, :bind6, :bind7, :bind8, :bind9, :bind10)", $alltuples);
      OCICommit($db_conn);

    } 

  if ($_POST && $success) {
    //POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
    header("location: reservation_add.php");
  } else {
    // Select data...
    $result = executePlainSQL("select * from Reservation");
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

