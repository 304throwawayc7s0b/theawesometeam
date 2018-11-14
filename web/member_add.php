<!DOCTYPE html>
<?php include 'header.php';?>
<html>
<body>

<?php
// define variables and set to empty values
$nameErr = $emailErr = $genderErr = $websiteErr = "";
$name = $email = $gender = $phoneNumber = "";
?>

<div class="column content">
    <h3>Add Membership</h3>
    <label><span class="error">* required</span></label>
    <form method="post" action="member_add.php">
        <label for="name">Name:</label> <input type="text" name="name" value="<?php echo $name; ?>">
        <span class="error">* <?php echo $nameErr; ?></span>
        <br><br>
        <label for="name">E-mail:</label> <input type="text" name="email" value="<?php echo $email; ?>">
        <span class="error"><?php echo $emailErr; ?></span>
        <br><br>
        <label for="name">Phone Number:</label> <input type="text" name="phoneNumber"
                                                       value="<?php echo $phoneNumber; ?>">
        <span class="error">* <?php echo $websiteErr; ?></span>
        <br><br>
        <label for="name">Gender:</label>
        <input type="radio" name="gender" <?php if (isset($gender) && $gender == "female") echo "checked"; ?>
               value="female">Female
        <input type="radio" name="gender" <?php if (isset($gender) && $gender == "male") echo "checked"; ?>
               value="male">Male
        <input type="radio" name="gender" <?php if (isset($gender) && $gender == "other") echo "checked"; ?>
               value="other">Other
        <span class="error">* <?php echo $genderErr; ?></span>
        <br><br>
        <div class="button">
            <input type="submit" name="submit" value="Submit">
        </div>

    </form>

</div>

<?php

//this tells the system that it's no longer just parsing 
//html; it's now parsing PHP

$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_u2m0b", "a38920154", "dbhost.ugrad.cs.ubc.ca:1522/ug");

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

function printResult($result)
{ //prints results from a select statement
  echo "<br>Got data from table Members:<br>";
  echo "<table>";
  echo "<tr><th>Name</th><th>EMail</th><th>PhoneNumber</th><th>Gender</th></tr>";

  while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
    echo "<tr><td>" . $row["NAME"] . "</td><td>" . $row["EMAIL"] . "</td><td>" . $row["PHONENUMBER"] . "</td><td>" . $row["GENDER"] . "</td></tr>"; //or just use "echo $row[0]" 
  }
  echo "</table>";

}

// Connect Oracle...
if ($db_conn) {

  if (array_key_exists('submit', $_POST)) {
    //Getting the values from user and insert data into the table
    $tuple = array(
      ":bind1" => $_POST['name'],
      ":bind2" => $_POST['email'],
      ":bind3" => $_POST['phoneNumber'],
      ":bind4" => $_POST['gender']
    );
    $alltuples = array(
      $tuple
    );
    executeBoundSQL("insert into Members values (:bind1, :bind2, :bind3, :bind4)", $alltuples);
    OCICommit($db_conn);

  }

  if ($_POST && $success) {
    //POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
    header("location: member_add.php");
  } else {
    // Select data...
    $result = executePlainSQL("select * from Members");
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

