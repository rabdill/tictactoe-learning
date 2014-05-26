<?php

	
include 'db_connect.php';
	
	
	
	function process_move()	{
		$query = "UPDATE tblSquares SET " . $_POST['move'] . " = 1 WHERE gameID = '" . $_POST['gameID'] . "'";
		$data = mysql_query($query);
			if($data == false) { echo "<strong><font color=red>Failed to insert move";
										die(mysql_error());
										echo "</strong></font><br><br>";		}
	}
	
	
	
	function start_new_game()	{
		$query = "SELECT MAX(gameID) FROM tblSquares";
		$data = mysql_query($query);
			//	if it can't find the MAX(gameID):
			if($data == false) { echo "<strong><font color=red>Failed to get MAX(gameID)";
										die(mysql_error());
										echo "</strong></font><br><br>";		}
										
			//	if it CAN find the MAX(gameID):
			else	{
				$info = mysql_fetch_array($data);
				$gameID = $info['MAX(gameID)'] + 1;
				//	start a new game
				$query = "INSERT INTO tblSquares (gameID) VALUES('" . $gameID . "')";
				$data = mysql_query($query);
					if($data == false) { echo "<strong><font color=red>Failed to insert new game";
												die(mysql_error());
												echo "</strong></font><br><br>";		}
					return $gameID;
			}
							
	}
	
	
	
	function check_for_gameover($info)	{
		//	0: game on!
		//	1: X wins
		//	2: O wins
		//	3: tie
		
		//	check the horizontals:
		if($info['square0'] !== '0' and $info['square0'] == $info['square1'] and $info['square0'] == $info['square2']) return $info['square0'];
		elseif($info['square3'] !== '0' and $info['square3'] == $info['square4'] and $info['square3'] == $info['square5']) return $info['square3'];  
		elseif($info['square6'] !== '0' and $info['square6'] == $info['square7'] and $info['square6'] == $info['square8']) return $info['square6'];
		
		//	check the verticals:
		elseif($info['square0'] !== '0' and $info['square0'] == $info['square3'] and $info['square0'] == $info['square6']) return $info['square0'];
		elseif($info['square1'] !== '0' and $info['square1'] == $info['square4'] and $info['square1'] == $info['square7']) return $info['square1'];
		elseif($info['square2'] !== '0' and $info['square2'] == $info['square5'] and $info['square2'] == $info['square8']) return $info['square2'];
		
		//	check the diagonals:
		elseif($info['square0'] !== '0' and $info['square0'] == $info['square4'] and $info['square0'] == $info['square8']) return $info['square0'];
		elseif($info['square2'] !== '0' and $info['square2'] == $info['square4'] and $info['square2'] == $info['square6']) return $info['square2'];
		
		//	check for tie
		elseif($info['square0'] !== '0' and $info['square1'] !== '0' and $info['square2'] !== '0' and $info['square3'] !== '0' and $info['square4'] !== '0' and 
				$info['square5'] !== '0' and $info['square6'] !== '0' and $info['square7'] !== '0' and $info['square8'] !== '0') return 3;
				
		else return 0;
	
	}
	

	function print_grid($info)	{
	echo "<table border='1'>";
		for($row = 0; $row < 3; $row++)	{
			echo "<tr>";
			for($column = 0; $column < 3; $column++)	{
				echo "<td>";
				$squareint = (int) ($row * 3) + $column;
				$squareNum = "square" . $squareint;
				
				//fill the square:
					switch($info[$squareNum])	{
						case 1: echo "X"; break;
						case 2: echo "O"; break;
					}
				
				
				echo "</td>";
			}	//	printing columns
			echo "</tr>";
		}	//	printing rows
	echo "</table>";
	}
	
	

?>