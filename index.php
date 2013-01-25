<html>
	<head>
		<link rel="StyleSheet" HREF="css/style.css" TYPE="text/css">
		<script type="text/javascript" src="js/jquery.js"></script>
	</head>
<body>
	<?php
		include('config.php');

		$query = '';
		if (isset($_GET['database']) && !isset($_GET['table'])){
			$database = $_GET['database'];
			printTables($database);
		}
		
		if (isset($_GET['database']) && isset($_GET['table'])){
			$database = $_GET['database'];
			$table = $_GET['table'];
			printTable($database, $table);
		}
		
		if (!isset($_GET['database']) && !isset($_GET['table'])){
			printDatabases();
		}

		function printTable($database, $table){
			mysql_select_db('$database'); 
			$columnNames = array();
			$columQueryString = "SELECT column_name FROM information_schema.columns WHERE table_name = '$table' order by ordinal_position";
			$columnQuery = mysql_query($columQueryString);
			while ($row = mysql_fetch_array($columnQuery)) {
				$columnNames[] = $row['column_name'];
			}
			
			$sql = '';
			if (isset($_GET['sort'])){
				$sortCriteria = $columnNames[$_GET['sort']];
				if (isset($_GET['desc'])) {
					$sql="select * from $table order by $sortCriteria desc";
				}
				else $sql="select * from $table order by $sortCriteria";			
			}
			else{
				$sql="select * from $table";	
			}
			
			mysql_select_db($database); 
			$query = mysql_query($sql);
			echo "<div id=\"toolbar_top_left\" onclick=\"javascript:(document.location='".$_SERVER["SCRIPT_NAME"]."?database=".$database."');\"><a href=\"".$_SERVER["SCRIPT_NAME"]."?database=".$database."\">Atr&aacute;s</a></div>";
			echo "<div id=\"toolbar_top_right\" onclick=\"javascript:(document.location='#top');\"><a href=\"#top\">Arriba</a></div>";
	
			echo ("<table id=\"container-table\">");
			echo ("<caption><a name=\"#top\">Tabla \"".$table."\"<br/>Base de datos \"".$database."\"</a></caption>");
			echo ("<tr id=\"header\">");	
				for ($i=0; $i<count($columnNames); $i++){
					echo "<td class=\"header\"><a href=\"".$_SERVER["SCRIPT_NAME"]."?database=".$database."&table=".$table."&sort=".$i.((isset($_GET['sort']) && $_GET['sort']==$i && !isset($_GET['desc']))?"&desc":"")."\">".$columnNames[$i].((isset($_GET['sort']) && $_GET['sort']==$i && !isset($_GET['desc']))?"&#x25B2;":"&#x25BC;")."</a></td>";
				}
			echo ("</tr>");
			if (mysql_num_rows($query)>0){
				$j=0;
				while ($row = mysql_fetch_array($query)){
					echo ("<tr class=\"row\">");
					for ($i=0; $i<count($columnNames); $i++){
						echo "<td class=\"".(($i>0)?"leftbar ":"").((($j%2)==0)?"pair ":"unpair ").((isset($_GET['sort']) && $_GET['sort']==$i)?" highlighted":"")."\">".@$row[$columnNames[$i]]."</a></td>";
					}
					echo ("</tr>");
					$j++;
				}	
			}else{
				echo "<tr><td colspan=\"".count($columnNames)."\" class=\"empty\">Tabla vac&iacute;a</td></tr>";
			}
			echo "</table>";
			
			echo ("<table id=\"header-fixed\">");
			echo ("<tr>");	
				for ($i=0; $i<count($columnNames); $i++){
					echo "<td class=\"header\"><a href=\"".$_SERVER["SCRIPT_NAME"]."?database=".$database."&table=".$table."&sort=".$i.((isset($_GET['sort']) && $_GET['sort']==$i && !isset($_GET['desc']))?"&desc":"")."\">".$columnNames[$i].((isset($_GET['sort']) && $_GET['sort']==$i && !isset($_GET['desc']))?"&#x25B2;":"&#x25BC;")."</a></td>";
				}
			echo ("</tr>");
			echo ("</table>");
			
			//mysql_free_result($columnQuery);
			mysql_free_result($query);
			mysql_close();
		}
		
		function printTables($database){
			//$database = $_GET['database'];
			$tables = array();
			//mysql_select_db('$database'); 
			$sql = "SHOW TABLES FROM $database";
			$tablesQuery= mysql_query($sql);
			if (!$tablesQuery) {
			    echo "Error de BD, no se pudieron listar las tablas\n";
			    echo 'Error MySQL: ' . mysql_error();
			    exit;
			}
			while ($row = mysql_fetch_row($tablesQuery)) {
				$tables[] = $row[0];
			}
			echo "<div id=\"toolbar_top_left\" onclick=\"javascript:(document.location='".$_SERVER["SCRIPT_NAME"]."');\"><a href=\"".$_SERVER["SCRIPT_NAME"]."\">Atr&aacute;s</a></div>";
			echo "<div id=\"toolbar_top_right\" onclick=\"javascript:(document.location='#top');\"><a href=\"#top\">Arriba</a></div>";
			echo ("<table>");
			echo ("<caption><a name=\"#top\">Base de datos (".$database.")</a></caption>");
			if (count($tables)>0){
				for ($i=0; $i<count($tables); $i++){
					echo ("<tr class=\"row\">");
					echo "<td onclick=\"javascript:(document.location='".$_SERVER["SCRIPT_NAME"]."?database=".$database."&table=".$tables[$i]."');\" class=\"".(($i>0)?"leftbar ":"").((($i%2)==0)?"pair ":"unpair ")."\"><a href=\"".$_SERVER["SCRIPT_NAME"]."?database=".$database."&table=".$tables[$i]."\">".$tables[$i]."</a></td>";
					echo ("</tr>");
				}
			}else{
				echo "<tr><td colspan=\"".count($tables)."\" class=\"empty\">Base de datos vac&iacute;a</td></tr>";
			}
			echo "</table>";
			mysql_free_result($tablesQuery);
			mysql_close();
		}
		
		function printDatabases(){
			$databases = array();
			$databasesQuery = mysql_query("SHOW DATABASES");
			while ($row = mysql_fetch_assoc($databasesQuery)) {
			    $databases[] = $row['Database'];
			}
			echo "<div id=\"toolbar_top_right\" onclick=\"javascript:(document.location='#top');\"><a href=\"#top\">Arriba</a></div>";
			echo ("<table>");
			echo ("<caption><a name=\"#top\">Bases de datos</a></caption>");
			for ($i=0; $i<count($databases); $i++){
				echo ("<tr class=\"row\">");
				echo "<td onclick=\"javascript:(document.location='".$_SERVER["SCRIPT_NAME"]."?database=".$databases[$i]."');\" class=\"".(($i>0)?"leftbar ":"").((($i%2)==0)?"pair ":"unpair ")."\"><a href=\"".$_SERVER["SCRIPT_NAME"]."?database=".$databases[$i]."\">".$databases[$i]."</a></td>";
				echo ("</tr>");
			}
			echo "</table>";
			mysql_free_result($databasesQuery);
			mysql_close();
		}
	?>
	
	<script type="text/javascript">
		var tableOffset = $("#header").offset().top;
		var $header = $("#container-table > thead").clone();
		var $fixedHeader = $("#header-fixed").append($header);
		var rowWidth = $('#header').width();
		$('#header-fixed').css({ width: rowWidth+"px"});
		var leftOffset = ($("#container-table").offset().left); //Grab the left position left first

		$(function(){
			var counter = 0;
	        $('#header-fixed td').each(function(){
	        	var columnWidth = $('#header td:eq('+counter+')').width();
	            var $this = $(this);
	            $this.css({ width: columnWidth+"px"});
	            counter++;
	        })
	    });
		
		$(window).bind("scroll", function() {
		    var offset = $(this).scrollTop();
		
		    if (offset >= tableOffset && $fixedHeader.is(":hidden")) {
		        $fixedHeader.show();
		    }
		    else if (offset < tableOffset) {
		        $fixedHeader.hide();
		    }
		    $('#header-fixed').css({
        		'left': -($(this).scrollLeft()-leftOffset)
    		});
		});
	</script>
	</body>
</html>