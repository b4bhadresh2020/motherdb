<?php
echo "test";


$servername = "localhost";
$username = "root";
$password = "";
$database = "motherdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");

for($i = 0; $i <= 10000; $i++) {

	$sql = "SELECT * FROM user";
	$result = $conn->query($sql)->fetch_assoc();
	$txt =  $i." >> ".$result["userId"]."\n";
	echo $txt;
	fwrite($myfile, $txt);

}

fclose($myfile);
echo "Connected successfully";

?>