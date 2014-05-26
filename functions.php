<?php

	
include 'db_connect.php';
	
	function computer_move($gameID, $debugger, $debugString)	{
	//COMPUTER MOVE TIME
	//	get the current grid
	$query = "SELECT * FROM tblSquares WHERE gameID =" . $gameID;
	$data = mysql_query($query);
	
	if($data !== false) {
		$info = mysql_fetch_array($data);

		$squareNumber = 0;	
		
		while($squareNumber < 9)	{
			$squareTest = "square" . $squareNumber;
			if($info[$squareTest] == '0')	{			// if it's a blank square:
					//	BUILD THE TEST QUERY:
				if($debugger==true) 	$debugString .= "<br><br>TESTING " . $squareTest;
				$query1 = "SELECT COUNT(gameID) FROM tblSquares WHERE ";
				$query1 .= $squareTest . "='2' AND ";		// 	start the query with the proposed computer move
				
				for($i = 0; $i < 9; $i++)	{
					$curSquare = "square" . $i;
					if($info[$curSquare] !== '0')	{	//	Query it up with only the filled-in squares
						if($curSquare !== $squareTest) $query1 .= $curSquare . "='" . $info[$curSquare] . "' AND ";	//	Add the board to the query EXCEPT for the one we're testing
					}
				}
				//	this FOR loop will end with an extra "AND"
				
				
				$query1x = $query1 . "winner='1'";	//		where x won			
				$query1o = $query1 . "winner='2'";	//		where o won
				$query1t = $query1 . "winner='3'";	//		where tie
				
				$data1 = mysql_query($query1x);
				$info1 = mysql_fetch_array($data1);
				$xwins = (int) $info1['COUNT(gameID)'];
				if($debugger==true) 	 $debugString .= "<br>xwins = " . $xwins;
				
				$data1 = mysql_query($query1o);
				$info1 = mysql_fetch_array($data1);
				$owins = (int) $info1['COUNT(gameID)'];
				if($debugger==true) $debugString .= "<br>owins = " . $owins;
				
				$data1 = mysql_query($query1t);
				$info1 = mysql_fetch_array($data1);
				$ties = (int) $info1['COUNT(gameID)'];
				if($debugger==true) $debugString .= "<br>ties = " . $ties;
				
				$score = (2* $owins) + $ties - (4 * $xwins);
				if($debugger==true) $debugString .= "<br>score = " . $score;
				
				$query2 = "INSERT INTO tblTempOptions (squareNum, score) VALUES ('" . $squareTest . "', " . $score . ")";
				if($debugger==true) $debugString .= "<br>" . $query2;
				$data2 = mysql_query($query2);
				if(!$data2) echo "<h1>Failed to insert option into tblTempOptions</h1>";

			}
			$squareNumber++;
		}
		
		
				//	SET THE COMPUTER'S MOVE
			$query1 = "SELECT squareNum, score FROM tblTempOptions ORDER BY score DESC";
			$data1 = mysql_query($query1);
			$info1 = mysql_fetch_array($data1);
			
			$computerMove = $info1['squareNum'];
			$moveScore = (int) $info1['score'];		//	for calculating the certainty later
			$query1 = "UPDATE tblSquares SET " . $computerMove . " = 2 WHERE gameID = '" . $_POST['gameID'] . "'";
			if($debugger==true) $debugString .= "<br>" . $query1;
			$data1 = mysql_query($query1);
				if($data1 == false) { echo "<strong><font color=red>Failed to insert computer move";
											die(mysql_error());
											echo "</strong></font><br><br>";		}		

						

			if($debugger == true)	{	
				$totalScores = 0;
				//	find the minimum score:
				$query1 = "SELECT MIN(score) FROM tblTempOptions";	//	sort results by square number
				$data1 = mysql_query($query1);
				$info1 = mysql_fetch_array($data1);
				$minScore = $info1['MIN(score)'];
				
				//	print all the scores into a grid:
				$query1 = "SELECT squareNum, score FROM tblTempOptions ORDER BY squareNum ASC";	//	sort results by square number
				$data1 = mysql_query($query1);
				$info1 = mysql_fetch_array($data1);	//	load the first score
				echo "<table border='1' style='float:right;'>";
				for($squareNumber = 0; $squareNumber < 9; $squareNumber++)	{
					if($squareNumber % 3 == 0) echo "<tr>";	//if it's a new row
					echo "<td>";
					$squareTest = "square" . $squareNumber;
					if($info1['squareNum'] == $squareTest) {
						echo $info1['score'];
						
						$totalScores += $info1['score'] - $minScore;		
							//	we add the minimum score to each one so we can add the scores together
							//	and have each number affect the certainty: if one square's score is 10
							//	and another's is -12, the certainty would otherwise pop out as -500%
							
						$info1 = mysql_fetch_array($data1);		// print the score and get the next one
						}
					echo "</td>";
					if(($squareNumber + 1) % 3 == 0) echo "</tr>";	//if it's about to be a new row
				}
				echo "</table>";
				echo "<h1>" . (($moveScore + $minScore) * 100) . " / " . $totalScores . "</h1>";
				$certainty = abs((($moveScore + $minScore)) * 100 / $totalScores, 1));	
					//	we have to use the absolute value for cases where the computer selects a 
					//	square that has a negative score.
				
			}
			
			
			//		Now delete all the options we piled up:
			$query = "DELETE FROM tblTempOptions WHERE 1";
			$data = mysql_query($query);
			if($data == false) echo "DIDN'T DELETE THE OPTIONS.";	
			return $certainty;
		}
	}
	
	
	
	
	
	
	function process_move($gameID,$move)	{
		$query = "UPDATE tblSquares SET " . $move . " = 1 WHERE gameID = '" . $gameID . "'";
		$data = mysql_query($query);
			if($data == false) { echo "<strong><font color=red>Failed to insert move<br>";
										echo $query . " ";
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