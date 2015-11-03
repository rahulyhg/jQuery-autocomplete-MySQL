<?php

require_once'dbvar.php'; /* Has these values:  $servername , $username , $password , $dbname */

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;port=3306;charset=utf8", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // below should be turned off to use PDO safely and is really only usable if you are using an old version of MySQL (from wiki.hashphp.org)
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
}

catch(PDOException $e) { 
//un-comment the PDOException to troubleshoot but leave it commented to hide info about code that would be available to hackers
        die("Failed to connect to the database: " /* . $e->getMessage() */); 
} 

$return_arr = array(); //instantiates an empty array to use for the response

if ($conn) //checks for a connection to the database
{ 
    $ac_term = $_GET['term'];//this comes from your database via getinfo.html so should not need to be scrubbed (please counter if you know something)

    //this query searches first and last names for a match to user typed info by assigning both :term1 and :term2 to the autocomplete term $ac_term   
    $query = "SELECT PERSON_ID, FIRST_NAME, LAST_NAME FROM PERSON where (FIRST_NAME REGEXP :term1 or LAST_NAME REGEXP :term2)"; 
    $result = $conn->prepare($query); 
    $result->bindValue(':term1',$ac_term);
    $result->bindValue(':term2',$ac_term);   
    $result->execute();  

    /* Retrieve and store in array the results of the query.
    concatenate first and last names for display to user*/ 
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) { 
        $row_array['id'] = $row['PERSON_ID']; 
        $row_array['value'] = $row['FIRST_NAME']." ".$row['LAST_NAME']; 
       
        array_push($return_arr,$row_array);   
    }//end while
 } //end if

/* Free connection resources. */ 
$conn = null;   

/* return results as json encoded array. */ 
echo json_encode($return_arr); 

exit();

?>