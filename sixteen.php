<?php	
		$debugger = true;
		$debugString = "" ;
include 'functions16.php';
connect_db();	

			//	if there's a move submitted:
			if(isset($_POST['move']))	{
				if($_POST['move'] == 'new') $gameID = start_new_game();
				
				elseif($_POST['move'] !== 'computer')	{
					process_move();
					$gameID = $_POST['gameID'];
				}
				
					else	{		//COMPUTER MOVE TIME
						$gameID = $_POST['gameID'];
						//	get the current grid
						$query = "SELECT * FROM tblSquares16 WHERE gameID =" . $gameID;
						$data = mysql_query($query);
						
						if($data !== false) {
							$info = mysql_fetch_array($data);

							$squareNumber = 0;	
							
							while($squareNumber < 16)	{
								$squareTest = "square" . $squareNumber;
								if($info[$squareTest] == '0')	{			// if it's a blank square:
										//	BUILD THE TEST QUERY:
									if($debugger==true) 	$debugString .= "<br><br>TESTING " . $squareTest;
									$query1 = "SELECT COUNT(gameID) FROM tblSquares16 WHERE ";
									$query1 .= $squareTest . "='2' AND ";		// 	start the query with the proposed computer move
									
									for($i = 0; $i < 16; $i++)	{
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
									
									$score = (3* $owins) + $ties - (2 * $xwins);
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
						$query1 = "UPDATE tblSquares16 SET " . $computerMove . " = 2 WHERE gameID = '" . $_POST['gameID'] . "'";
						if($debugger==true) $debugString .= "<br>" . $query1;
						$data1 = mysql_query($query1);
							if($data1 == false) { echo "<strong><font color=red>Failed to insert move";
														die(mysql_error());
														echo "</strong></font><br><br>";		}		
					
						//		Now delete all the options we piled up:
						$query = "DELETE FROM tblTempOptions WHERE 1";
						$data = mysql_query($query);
						if($data == false) echo "DIDN'T DELETE THE OPTIONS.";	
						
						
					}
				
				}		// end computer move block
				
			}	//	end block that evaluates $_POST['move']
			
			else $gameID = start_new_game();		//	if it's not set
		?>




<html>
	<head>
		<title>EVIL TIC TAC TOE</title>
		<style>
			td	{
				width: 50px;
				height: 50px;
				text-align: center;
				}
			button {
				height: 48px;
				
				}
			</style>
	</head>
	
	<body>
	<h1>Game Number <?php echo $gameID; ?></h1>
			<form action="sixteen.php" method="post">
			<input type="hidden" name='gameID' value="<?php echo $gameID; ?>">
			<button name='move' type='submit' value='new'>New game</button>
			<button name='move' type='submit' value='computer'>Get computer move</button><br><br><br>
			
			
			<table border = "1">
			<?php
				//	get the current grid
				$query = "SELECT * FROM tblSquares16 WHERE gameID =" . $gameID;
				$data = mysql_query($query);
					if($data == false) { echo "<strong><font color=red>Failed to get game squares";
												die(mysql_error());
												echo "</strong></font><br><br>";		}
					
					//	draw the grid:					
					else	{
						$info = mysql_fetch_array($data);
						$gameOver = check_for_gameover($info);
						if($gameOver == 0) {		//	if the game isn't over:
							for($row = 0; $row < 4; $row++)	{
								echo "<tr>";
								for($column = 0; $column < 4; $column++)	{
									echo "<td>";
									$squareint = (int) ($row * 4) + $column;
									$squareNum = "square" . $squareint;
									
									//fill the square:
									if($info[$squareNum] == 0) {
										echo "<button name='move' value='" . $squareNum . "'>move</button>";
									}
									else {
										switch($info[$squareNum])	{
											case 1: echo "X"; break;
											case 2: echo "O"; break;
										}
									}
									
									
									echo "</td>";
								}	//	printing columns
								echo "</tr>";
							}	//	printing rows
							
														
						}	//	if gameOver is 0
						
						
							
						
						elseif($gameOver == 1) { print_grid($info); echo "<h2>X wins!</h2>"; }
						elseif($gameOver == 2) { print_grid($info); echo "<h2>O wins!</h2>"; }
						else { echo print_grid($info); "TIE!"; }
						
						//	 set the winner
						$query = "UPDATE tblSquares16 SET winner='{$gameOver}' WHERE gameID =" . $gameID;
						$data = mysql_query($query);

						
					}	//	end of grid-drawing block
				
					?>
			</form>
			
		</table>
			<div id='computer-output'><?php echo $debugString; ?></div>
		
	</body>
</html>