Hello! Setting up the database for this project will be fairly easy, especially if you
already have some experience with MySQL. As you can see at the top of the
functions.php file, you need a file called "db_connect.php" to facilitate writing info
to the database. This file is included in .gitignore because it almost always contains
sensitive credentials. To make your own, just paste this into a file and fill in your
info:
//	Code starts here:
<?php
	function connect_db()	{
		date_default_timezone_set ('America/New_York');
					$data=mysql_connect("DB SERVER ADDRESS","DB USERNAME","DB PASSWORD!");
					if(!$data) echo "<h2>ERROR: Could not connect to database.</h2>";
					$data=mysql_select_db("DB NAME");
					if(!$data) echo "<h2>ERROR: Could not choose schema.</h2>";    
	}
	
	?>
//	Code ends here

One note: To get things started quickly, I used the deprecated mysql_
commands rather than the new mysqli_ ones. I only did this because I'm far more
comfortable using them and I wanted to get something up quickly, but everything
will at some point be switched over. If you want to do it yourself, that's even better.

You'll also need to create three tables for the app to use. Just run these queries
(or, if you see something like CHARSET that you want to change, at least something
LIKE these queries) in your new schema and you'll be all set.

This one creates the table in which the computer stores the results of all the games
played on the standard 3x3 tic-tac-toe grid:

CREATE TABLE `tblSquares` (
  `gameID` int(10) unsigned NOT NULL,
  `square0` int(1) unsigned zerofill NOT NULL,
  `square1` int(1) unsigned zerofill NOT NULL,
  `square2` int(1) unsigned zerofill NOT NULL,
  `square3` int(1) unsigned zerofill NOT NULL,
  `square4` int(1) unsigned zerofill NOT NULL,
  `square5` int(1) unsigned zerofill NOT NULL,
  `square6` int(1) unsigned zerofill NOT NULL,
  `square7` int(1) unsigned zerofill NOT NULL,
  `square8` int(1) unsigned zerofill NOT NULL,
  `winner` int(1) unsigned zerofill NOT NULL,
  PRIMARY KEY (`gameID`),
  UNIQUE KEY `gameID_UNIQUE` (`gameID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



This one does the same thing, except for the 4x4 grid:

CREATE TABLE `tblSquares16` (
  `gameID` int(10) unsigned NOT NULL,
  `square0` int(1) unsigned zerofill NOT NULL,
  `square1` int(1) unsigned zerofill NOT NULL,
  `square2` int(1) unsigned zerofill NOT NULL,
  `square3` int(1) unsigned zerofill NOT NULL,
  `square4` int(1) unsigned zerofill NOT NULL,
  `square5` int(1) unsigned zerofill NOT NULL,
  `square6` int(1) unsigned zerofill NOT NULL,
  `square7` int(1) unsigned zerofill NOT NULL,
  `square8` int(1) unsigned zerofill NOT NULL,
  `square9` int(1) unsigned zerofill NOT NULL,
  `square10` int(1) unsigned zerofill NOT NULL,
  `square11` int(1) unsigned zerofill NOT NULL,
  `square12` int(1) unsigned zerofill NOT NULL,
  `square13` int(1) unsigned zerofill NOT NULL,
  `square14` int(1) unsigned zerofill NOT NULL,
  `square15` int(1) unsigned zerofill NOT NULL,
  `winner` int(1) unsigned zerofill NOT NULL,
  PRIMARY KEY (`gameID`),
  UNIQUE KEY `gameID_UNIQUE` (`gameID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


Aaand this one is the table in which the computer temporarily stores the options it's
evaluating for any given move. In between moves, it's empty, and right now it only
works if one computer player is using it at a time. This whole structure will likely be
replaced eventually, but for now we're stuck with it.

CREATE TABLE `tblTempOptions` (
  `optionID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `squareNum` varchar(45) NOT NULL,
  `score` int(10) NOT NULL,
  PRIMARY KEY (`optionID`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;
