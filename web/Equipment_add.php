<!DOCTYPE html>

<html>

<head>

<link rel="stylesheet" type="text/css" href="style.css">

</head>
<body>

  <?php
    // define variables and set to empty values
    $websiteErr = "";
  ?>

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
            <a href="member_add.php">Add Membership</a>
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


  <div class="column content">
    <h3>Add New Equipment</h3>
    <label><span class="error">* required</span></label>
    <form method="post" action="Equipment_add.php">  
      <label for="PurchaseDate">Purchase Date:</label> <input type="text" name="PurchaseDate">
      <span class="error">* <?php echo $nameErr;?></span>
      <br><br>
      <label for="PurchasePrice">Purchase Price:</label> <input type="text" name="PurchasePrice">
      <span class="error">* <?php echo $websiteErr;?></span>
      <br><br>
      <label for="EquipID">EquipID:</label> <input type="text" name="EquipID">
      <span class="error">* <?php echo $websiteErr;?></span>
      <br><br>
     <label for="EquipType">Equipment Type:</label> <input type="text" name="EquipType">
      <span class="error">* <?php echo $websiteErr;?></span>
    <br><br>
    <label for="Location">Location:</label> <input type="text" name="Location" value="<?php echo $phoneNumber;?>">
      <span class="error">* <?php echo $websiteErr;?></span>
    <br><br>
   <label for="City">City: </label> <input type="text" name="City">
      <span class="error">* <?php echo $websiteErr;?></span>
    <br><br>
      <div class="button">
        <input type="submit" name="submit" value="Submit">  
      </div>
      
    </form>

  </div>
  <div class="column content">
  <p> Update the Equipment Location/City below: </p>
<p><font size="2"> EquipID&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Old City&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
New City&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Old Location&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;New Location</font></p>
<form method="POST" action="Equipment_add.php">
<!--refresh page when submit-->

   <p><input type="text" name="CurEquipID" size="6"><input type="text" name="oldCity" size="18"><input type="text" name="newCity" 
size="18"><input type="text" name="oldLocation" size="18"><input type="text" name="newLocation" size="18">
<!--define two variables to pass the value-->
      
<input type="submit" value="update" name="updatesubmit"></p>
</form>
</div>
<div class="column content">
    <h3>Remove Old Equipment</h3>
    <label><span class="error">* required</span></label>
    <form method="post" action="Equipment_add.php">  
   <label for="EQUIPID">EquipID: </label> <input type="text" name="DeleteEquip">
      <span class="error">* <?php echo $websiteErr;?></span>
    <br><br>
      <div class="button">
        <input type="submit" name="DeleteEquipment" value="DeleteEquipment">  
      </div>
      
    </form>

  </div>
<?php

//this tells the system that it's no longer just parsing 
//html; it's now parsing PHP

$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_c7s0b", "a38293149", "dbhost.ugrad.cs.ubc.ca:1522/ug");

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
  echo "<br>Got data from table Equpiment:<br>";
  echo "<table>";
  echo "<tr><th>EquipID</th><th>Price&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th><th>PurchaseDate&nbsp;&nbsp;&nbsp;&nbsp;</th><th>Type&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th><th>Branch&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th><th>City</th></tr>";

  while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
    echo "<tr><td>" .  $row["EQUIPID"] . "</td><td>" .  $row["PURCHASEPRICE"] . "</td><td>".  $row["PURCHASEDATE"] . "</td><td>" .  $row["EQUIPTYPE"] . "</td><td>" .  $row["LOCATION"] . "</td><td>" .  $row["CITY"] . "</td></tr>"; //or just use "echo $row[0]" 
  }
  echo "</table>";

}

// Connect Oracle...
if ($db_conn) {

  if (array_key_exists('submit', $_POST)) {
      //Getting the values from user and insert data into the table
      $tuple = array (
        ":bind1" => $_POST['EquipID'],
        ":bind2" => $_POST['PurchaseDate'],
        ":bind3" => $_POST['PurchasePrice'],
        ":bind4" => $_POST['EquipType'],
        ":bind5" => $_POST['Location'],
        ":bind6" => $_POST['City']
      );
      $alltuples = array (
        $tuple
      );
      executeBoundSQL("insert into Equipment values (:bind1, :bind2, :bind3, :bind4, :bind5, :bind6)", $alltuples);
      OCICommit($db_conn);

    } else
      if (array_key_exists('updatesubmit', $_POST)) {
        // Update tuple using data from user
        $tuple = array (
          ":bind1" => $_POST['CurEquipID'],
          ":bind2" => $_POST['oldCity'],
          ":bind3" => $_POST['newCity'],
          ":bind4" => $_POST['oldLocation'],
          ":bind5" => $_POST['newLocation']
        );
        $alltuples = array (
          $tuple
        );
        executeBoundSQL("update Equipment set CITY=:bind3, LOCATION=:bind5 where CITY=:bind2 AND EQUIPID=:bind1 AND LOCATION=:bind4", $alltuples);
        OCICommit($db_conn);

      }
      else
      if (array_key_exists('DeleteEquipment', $_POST)) {
        // Update tuple using data from user
        $tuple = array (
          ":bind1" => $_POST['DeleteEquip']
        );
        $alltuples = array (
          $tuple
        );
        executeBoundSQL("DELETE FROM Equipment WHERE EQUIPID=:bind1", $alltuples);
        OCICommit($db_conn);

      }

  if ($_POST && $success) {
    //POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
    header("location: Equipment_add.php");
  } else {
    // Select data...
    $result = executePlainSQL("select * from Equipment");
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

