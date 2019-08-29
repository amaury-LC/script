<?php
$dsn = "mysql:dbname=hoalitest;host=localhost";
$user = "root";
$password = "";
try {
    $dbh = new PDO($dsn, $user, $password,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
} catch (PDOException $e) {
    echo 'Connexion échouée : ' . $e->getMessage();
}

function num($adress){
    $num = " ";
   $numTab = str_split($adress);
   for ($i=0; $i < count($numTab) ; $i++) { 
       
        if($numTab[$i] == "R" | $numTab[$i] == "P" | $numTab[$i] == "L" ){

            return $num;
        }
        else{

            $num .= $numTab[$i];
            

        
        }

        
   }

}


set_time_limit(1000);


$reponse = $dbh->query('SELECT * FROM trash_collectors');

while ($donnees = $reponse->fetch())
{
    // echo $donnees["location_x"].'<br>';
    // echo $donnees["location_y"].'<br>';

    $homepage = file_get_contents('http://dev.virtualearth.net/REST/v1/Locations/'.$donnees["location_x"].','.$donnees["location_y"].'?o=json&key=AuH8sc-k6myWy7tJfcqxDHHqDFYEfjaVBSyR3K58ZI5Hx3ArAjQ2P6CoewiwBgsV ');

    $test =  json_decode($homepage, true);

    $address = $test["resourceSets"][0]["resources"][0]["address"]["addressLine"];


    $address = $test["resourceSets"][0]["resources"][0]["address"]["addressLine"];
    $pays = $test["resourceSets"][0]["resources"][0]["address"]["countryRegion"];
    $ville = $test["resourceSets"][0]["resources"][0]["address"]["locality"];
    $codePostal = $test["resourceSets"][0]["resources"][0]["address"]["postalCode"];
    $num = num($address);

    // echo $address.'<br>';
    // echo $pays.'<br>';
    // echo $ville.'<br>';
    // echo $codePostal.'<br>';
    // echo $num.'<br>';


    $req = $dbh->prepare('UPDATE trash_collectors SET adr_num = :adr_num,adr_street = :adr_street,adr_city = :adr_city,adr_zip_code = :adr_zip_code,adr_country = :adr_country WHERE id ='.$donnees["id"].'');
    $req->execute(array(
	'adr_num' => $num,
	'adr_street' => $address,
	'adr_city' => $ville,
	'adr_zip_code' => $codePostal,
	'adr_country' => $pays,

	));
    

}

$reponse->closeCursor(); 




