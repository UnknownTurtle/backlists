<?php
include 'Blacklists.php';
/*$obj = new Blacklist();
$obj->check('p1, p111, s2, s222, f333 ,fd3, s11f, s-12', '14');*/
$result = Blacklist::save('p1, p214114, p111, s2, s222, f333 ,fd3, s11f, s-12', '14');
echo $result;
//var_dump($result);
$result = Blacklist::get(13);
foreach ($result as $row) 
{
	$id = $row['block'];
    echo $id."<br>";
}
?>