<?php	
		$debugger = true;
		$debugString = "" ;
include 'functions.php';
connect_db();	

			//	if there's a move submitted:
			if(isset($_POST['move']))	{
				if($_POST['move'] == 'new') $gameID = start_new_game();
				
				elseif($_POST['move'] !== 'computer')	{
					process_move();
					$gameID = $_POST['gameID'];
				
					//		Get the computer move:
					$query = "SELECT * FROM tblSquares WHERE gameID =" . $gameID;
					$data = mysql_query($query);
						if($data == false) { echo "<strong><font color=red>Failed to get game squares";
													die(mysql_error());
													echo "</strong></font><br><br>";		}

							$info = mysql_fetch_array($data);
							$gameOver = check_for_gameover($info);
							//	If the game isn't over, make a computer move:
							if($gameOver == 0) {		computer_move($_POST['gameID'], $debugger, $debugString);
										$gameID = $_POST['gameID'];
							}		
				}
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
			<form action="nine.php" method="post">
			<input type="hidden" name='gameID' value="<?php echo $gameID; ?>">
			<button name='move' type='submit' value='new'>New game</button>
			<button name='move' type='submit' value='computer'>Get computer move</button><br><br><br>
			
			
			<table border = "1">
			<?php
				//	get the current grid
				$query = "SELECT * FROM tblSquares WHERE gameID =" . $gameID;
				$data = mysql_query($query);
					if($data == false) { echo "<strong><font color=red>Failed to get game squares";
												die(mysql_error());
												echo "</strong></font><br><br>";		}
					
					//	draw the grid:					
					else	{
						$info = mysql_fetch_array($data);
						$gameOver = check_for_gameover($info);
						if($gameOver == 0) {		//	if the game isn't over:
							for($row = 0; $row < 3; $row++)	{
								echo "<tr>";
								for($column = 0; $column < 3; $column++)	{
									echo "<td>";
									$squareint = (int) ($row * 3) + $column;
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
						$query = "UPDATE tblSquares SET winner='{$gameOver}' WHERE gameID =" . $gameID;
						$data = mysql_query($query);

						
					}	//	end of grid-drawing block
				
					?>
			</form>
			
		</table>
			<div id='computer-output'><?php echo $debugString; ?></div>
		
	</body>
</html>