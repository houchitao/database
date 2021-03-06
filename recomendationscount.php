<?php
// This script php implements

// find all artists listened by a user’s friends but not the user, order them by the
// sum of listening count, recommend the top 5

/*
MATCH ( u:user {id: 3})-[:ADD_FRIEND_TO]->(:user)-[listen: WEIGHT] -(a:artist)
WHERE NOT (u)-[:WEIGHT]-(a)
WITH a, sum(listen.weight) as count
RETURN a
ORDER BY count desc LIMIT 5;
*/

// Include library of functions
include("functions.php");

// connection to neo4j
require('vendor/autoload.php');
$client = new Everyman\Neo4j\Client('localhost', 7474);

printHeader();
printSearchForm($uid); 
echo "<B>User: $uid - All Artist Listened by my friends order by the sum of listening count</b><br><br>";

$queryString = "MATCH (u:user {id: $uid})-[:ADD_FRIEND_TO]->(:user)-[listen: WEIGHT] -(a:artist)
WHERE NOT (u)-[:WEIGHT]-(a)
WITH a, sum(listen.weight) as count
RETURN a
ORDER BY count desc LIMIT 5";

$query = new Everyman\Neo4j\Cypher\Query($client, $queryString);
$result = $query->getResultSet();
			
foreach ($result as $row) {
	$artist = $client->getNode($row['x']->getId());
	$artistId = $artist->getProperty('id');
	$artistName = $artist->getProperty('name');
	echo "Artist Name: ".$artistName."</br>"; 
	printArtistInfo($artistId);
	?>
	<form action="listen.php" method="post">
	<input type="hidden" name="action" value="addlisten">
	<input type="hidden" name="userId" value="<? echo $uid ?>">
	<input type="hidden" name="artistId" value="<? echo $artistId ?>">
	<input type="submit" value="Listen">
	</form>
	<?

}
printFooter();
?>