<?php

class Blacklist
{
	public static function save($param, $advID)
	{
		$result = self::check($param, $advID);
		var_dump($result);
		$sql = "INSERT INTO `blacklists` (`id`, `block`) VALUES ";
		foreach ($result as $elem)
		{
			$sql.="('".$advID."','".$elem."'),";
		}
		$sql = mb_substr($sql, 0, -1);
		$db = new DB();
		$db->save($sql);
		return "successful"."<br>";
	}

	public static function get($advID)
	{
		$result = '';
		$sql = "SELECT DISTINCT block FROM blacklists WHERE id=:id";
		$db = new DB();
		$result = $db->query($sql,$advID,'get');
		return $result;
	}

	public static function check($param, $advID)
	{
		$output = '';
		$list = null;
    	if (!ctype_digit($advID))
		{
        	exit("Неккоректный идентификатор рекламодателя: ($advID)");
        }
           			
		try
		{
        	
			$list = explode(",", $param);
			/*$stmtPubl = $dbh->prepare("SELECT * FROM publishers WHERE id= ?");
			$stmtSite = $dbh->prepare("SELECT * FROM sites WHERE id= ?");*/
			foreach ($list as &$elem)
			{
				$elem = trim($elem);
				$thisPref = substr($elem, 0, 1);
				$thisId = substr($elem, 1);
				if (!in_array($thisPref, array('s','p')))
				{
					$ouptut.= "<br>Не соответствие префиксов: ".$elem;
					unset($list[array_search($elem, $list)]);
					continue;
				}

				if (!ctype_digit($thisId))
				{
					$ouptut.= "<br>Не соответствие идентификатора: ".$elem;
					unset($list[array_search($elem, $list)]);
					continue;
				}

				$sql = 'SELECT * FROM advertisers WHERE id=:id';
		        $db = new DB;
		        $result = $db->query($sql,$advID, '');
		       	if(!$result)
		       	{
		       		$ouptut.= "<br>Не найден рекламодатель с id  ".$advID;
		       		unset($list[array_search($elem, $list)]);
					continue;
		       	}
		       	
		       	if($thisPref=='s')
		       	{
			       	$sql = 'SELECT * FROM sites WHERE id=:id';
			        $db = new DB;
			        $result = $db->query($sql,$thisId, '');
			       	if(!$result)
			       	{
			       		$ouptut.= "<br>Не найден идентификатор сайта s".$thisId;
			       		unset($list[array_search($elem, $list)]);
						continue;
			       	}
			    }

			    if($thisPref=='p')
		       	{
			       	$sql = 'SELECT * FROM publishers WHERE id=:id';
			        $db = new DB;
			        $result = $db->query($sql,$thisId, '');
			       	if(!$result)
			       	{
			       		$ouptut.= "<br>Не найден идентификатор паблишера p".$thisId;
			       		unset($list[array_search($elem, $list)]);
						continue;
			       	}
			    }
			}
 
		}
		catch (Exception $e)
		{
			echo "Не удалось разделить строку. Ошибка: ".$e;
			die();
		}

		/*echo $ouptut;
		echo "<br>";*/
		//var_dump($list);
		return $list;
	}

	

}

class DB
{
	private $db;

	// Соединение с БД
	public function __construct()
	{
		$host = "localhost";
		$user = "newuser";
		$password = "000000";
		$db = "test";
		$this->db = new PDO('mysql:host='.$host.';dbname='.$db.';charset=utf8', $user, $password);
	}

	// Операции над БД
	public function query($sql, $params, $res)
	{
		$stmt = $this->db->prepare($sql);
			
		// Обход массива с параметрами 
		// и подставление значений

		$stmt->bindValue(":id", $params);

			
		// Выполняем запрос
		$stmt->execute();
		// Возвращаем ответ
		if($res == 'get')
		{
			return $stmt->fetchAll();
		}
		else return $stmt->rowCount();
		
	}

	public function save($sql)
	{
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
	}
}
?>