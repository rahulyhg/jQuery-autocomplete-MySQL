
<?php
   /*this require statement refers to db log info page which might look like this:

   <?php
    $servername = "12.34.56.78";
    $username = "yourusername";
    $password = "yourpassword";
    $dbname = "yourdbname";
    ?>
    keeping your password hard to find is a good idea */
   require_once'dbvar.php'; 
  
  $personid = $_GET['personid'];//get a primary key to reference rows in the database 
  //use a catch block to open a connection
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;port=3306;charset=utf8", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // below should be turned off to use PDO safely and is really only usable if you are using an old version of MySQL (from wiki.hashphp.org)
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    //get persons info by reference and bind the value to variable recieved from getinfo.php
    $query = "SELECT PERSON_ID, FIRST_NAME, LAST_NAME FROM PERSON WHERE PERSON_ID = :person_id"; 
    $result = $conn->prepare($query); 
    $result->bindValue(':person_id',$personid);  
    $result->execute();  

    //create a table with column headers - add any table formatting inside <table ...>
    echo '<table align="left" cellspacing="2" cellpadding="2">
          <tr><td align="left"><b>Person ID</b></td>
              <td align="left"><b>Name</b></td></tr>';

    //fetch persons identifying info from database and fill the table
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
       echo '<tr><td align="left">' . $row['PERSON_ID'] . '</td>
                 <td align="left">' . $row['FIRST_NAME'] . ' ' . $row['LAST_NAME'] . '</td></tr>';
         }

        echo '</table>';//end person description table

    //get persons task/activities from three MySQL tables using variable recieved from getinfo.php
    $query =   "SELECT PROJECT_NAME,TASK_NAME,TASK_NOTES,TIMER_RUNNING FROM TASK, ASSIGNED_TO_TASK, PROJECT
      WHERE TASK.TASK_ID = ASSIGNED_TO_TASK.TASK_ID and ASSIGNED_TO_TASK.PROJECT_ID = PROJECT.PROJECT_ID and PERSON_ID = :person_id";
    $result = $conn->prepare($query); 
    $result->bindValue(':person_id',$personid);  
    $result->execute();  
   
  //create a table with column headers
    echo '<table align="left" cellspacing="2" cellpadding="2">
          <tr><td align="left"><b>Project Name</b></td>
          <td align="left"><b>Task</b></td>
          <td align="left"><b>Notes</b></td>
          <td align="left"><b>Active</b></td></tr>';

   //fetch info and fill table
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
       echo '<tr><td align="left">' . $row['PROJECT_NAME'] . '</td>
                 <td align="left">' . $row['TASK_NAME'] . '</td>
                 <td align="left">' . $row['TASK_NOTES'] . '</td>
                 <td align="left">' . $row['TIMER_RUNNING'] .'</td></tr>';
         }

        echo '</table>'; //end list of persons task/activities table

         }  //end try

catch(PDOException $e)
    {
    echo $query . "<br>" . $e->getMessage();
    }
$conn = null;

exit();

?>



