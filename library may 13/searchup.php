  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml">
  <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Results</title>
  <link rel="Stylesheet" type="text/css" href="css/smoothDivScroll.css" />
  <link href="templatemo_style.css" rel="stylesheet" type="text/css" />
  <script>
  function validateSearch() {
      var byTitle   = document.getElementById('searchForm').title.value.trim();
      var byAuthor  = document.getElementById('searchForm').author.value.trim();  
      var byPublisher = document.getElementById('searchForm').publisher.value.trim();
      if(byTitle == "" && byAuthor == "" && byPublisher == ""){
          alert("Enter at least one search parameter!");
          return false;
      }
  }
  </script>
  </head>
  <body>
  <h2 style="background-color:white;margin-bottom:0px;padding-left:10px;font-size:20px;">A Library Search System using Semantic Web</h2>
  <div id="templatemo_menu_wrapper">
    <div id="templatemo_menu">
      <ul>
        <li><a href="index.html" class="current">Search</a></li>
        <li><a href="http://www.jssaten.ac.in" rel="nofollow" target="_blank">JSSATE</a></li>
        <li><a href="http://210.212.85.155" target="_blank">JSS Info Centre</a></li>
        <li><a href="http://www.uptu.ac.in" target="_blank">UPTU</a></li>
        <li><a href="http://210.212.85.155/library" target="_blank">JSS Library Site</a></li>
        <li><a href="aboutus.html">About Us</a></li>
      </ul>
    </div>
    <!-- end of templatemo_menu --> 
  </div>
  <?php
  require_once( "sparqllib.php" );
  $title = trim($_GET["title"]);
  $author = trim($_GET["author"]);
  $publisher = trim($_GET["publisher"]);
  $db = sparql_connect( "http://localhost:8890/sparql/" );
  if( !$db ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
  $sparql = "SELECT ?o,?o2,?o1,?o3,?o4,?o5 from <http://localhost:8890/librarydata> WHERE { ?s rdf:author ?o.FILTER (regex(?o, '.*$author.*','i')) .?s rdf:publisher ?o1.FILTER (regex(?o1, '.*$publisher.*','i')).?s rdf:title ?o2.FILTER (regex(?o2, '.*$title.*','i')).?s rdf:edition ?o3.?s rdf:location ?o4.?s rdf:booksAvailable ?o5} ";
  $result = sparql_query( $sparql ); 
  if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit;
   }
   $fields = sparql_field_array( $result );
  ?>
  <div id="templatemo_content_wrapper">
  <div id="templatemo_sidebar">
    <div class="sidebar_box">
      <h2>JSS Library Search</h2>
     <form action="searchup.php" name="search_form"  id="searchForm" method="get" style="text-align:center" class="bootstrap-frm" onsubmit="return validateSearch()">
        <input type="text" placeholder="Title" name="title" size="35" value="<?php echo $title?>"/>
        <br/>
        <input type="text" placeholder="Author" name="author" size="35" value="<?php echo $author?>"/>
        <br/>
        <input type="text" placeholder="Publisher" name="publisher" size="35" value="<?php echo $publisher?>"/>
        <br/>
        <input type="submit" name="submit" value="Search" class="button"/>
      </form>
      <div class="cleaner"></div>
    </div>
    <div class="sidebar_box_bottom"></div>
  </div>
  <!-- end of sidebar -->
  <div id="templatemo_content" style="float:right;">
  <div class="content_box">
  <?php
          print "<h2>".sparql_num_rows( $result)." results found</h2>";
          ?>
  <table class='example_table' name='main_results'>
  <tr>
    <th>Author</th>
    <th>Title</th>
    <th>Publisher</th>
    <th>Edition</th>
    <th>location</th>
    <th>Books Available</th>
  </tr>
  <?php
  $c = 0;
  $author2 = "";
  $publisher2 = "";
  while( $row = sparql_fetch_array( $result ) )
  {
      print "<tr>";
      foreach( $fields as $field )
      {
          if($c==0)
          {
          $author2 = $row[$field];
          }
          if($c==2)
          {
          $publisher2 = $row[$field];
          }
          $c++;
          if($field=='o')
              print "<td><a href='searchup.php?title=&author=$row[$field]&publisher=' style='text-decoration:none ;color:black'>$row[$field]</a></td>";
          else if($field=='o1')
              print "<td><a href='searchup.php?title=&author=&publisher=$row[$field]' style='text-decoration:none ;color:black'>$row[$field]</a></td>";
          else if($field=='o2')
              print "<td><a href='searchup.php?title=$row[$field]&author=&publisher=' style='text-decoration:none ;color:black'>$row[$field]</a></td>";
          else
              print "<td>$row[$field]</td>";
      }
      print "</tr>";
  }
  print "</table>";
  ?>
  <div class="cleaner"></div>
  </div>
  <div class="content_box_bottom"></div>
  <?php
  if(sparql_num_rows( $result)==0)
      goto webresult;
  if($author == "")
  {
      print"<div class='content_box'>";
  		print"<h2>More books by $author2</h2>";
  $sparql1 = "SELECT ?o,?o2,?o1,?o3,?o4,?o5 from <http://localhost:8890/librarydata> WHERE { ?s rdf:author ?o.FILTER (regex(?o, '.*$author2.*','i')).?s rdf:publisher ?o1.?s rdf:title ?o2.?s rdf:edition ?o3.?s rdf:location ?o4.?s rdf:booksAvailable ?o5} ";
  $result1 = sparql_query( $sparql1 ); 
  if( !$result1 ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
  $fields = sparql_field_array( $result1 );
  ?>
  <table class='example_table' name='related_results'>
  <tr>
    <th>Author</th>
    <th>Title</th>
    <th>Publisher</th>
    <th>Edition</th>
    <th>location</th>
    <th>Books Available</th>
  </tr>
  <?php
  while( $row = sparql_fetch_array( $result1 ) )
  {
      print "<tr>";
      foreach( $fields as $field )
      {
              if($field=='o')
              print "<td><a href='searchup.php?title=&author=$row[$field]&publisher=' style='text-decoration:none ;color:black'>$row[$field]</a></td>";
          else if($field=='o1')
              print "<td><a href='searchup.php?title=&author=&publisher=$row[$field]' style='text-decoration:none ;color:black'>$row[$field]</a></td>";
          else if($field=='o2')
              print "<td><a href='searchup.php?title=$row[$field]&author=&publisher=' style='text-decoration:none ;color:black'>$row[$field]</a></td>";
          else
              print "<td>$row[$field]</td>";
      }
      print "</tr>";
  }
  print "</table>";
  ?>
  <div class="cleaner"></div>
  </div>
  <div class="content_box_bottom"></div>
  <?php         
  }
  if($publisher == "")
  {
      print"<div class='content_box'>";
  print"<h2>More books by $publisher2</h2>";
  $sparql1 = "SELECT ?o,?o2,?o1,?o3,?o4,?o5 from <http://localhost:8890/librarydata> WHERE { ?s rdf:author ?o.?s rdf:publisher ?o1.FILTER (regex(?o1, '.*$publisher2.*','i'))?s rdf:title ?o2.?s rdf:edition ?o3.?s rdf:location ?o4.?s rdf:booksAvailable ?o5} ";
  $result1 = sparql_query( $sparql1 ); 
  if( !$result1 ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
  $fields = sparql_field_array( $result1 );
  ?>
  <table class='example_table' name='related_results_publisher'>
  <tr>
    <th>Author</th>
    <th>Title</th>
    <th>Publisher</th>
    <th>Edition</th>
    <th>location</th>
    <th>Books Available</th>
  </tr>
  <?php
  while( $row = sparql_fetch_array( $result1 ) )
  {
      print "<tr>";
      foreach( $fields as $field )
      {
          if($field=='o')
              print "<td><a href='searchup.php?title=&author=$row[$field]&publisher=' style='text-decoration:none ;color:black'>$row[$field]</a></td>";
          else if($field=='o1')
              print "<td><a href='searchup.php?title=&author=&publisher=$row[$field]' style='text-decoration:none ;color:black'>$row[$field]</a></td>";
          else if($field=='o2')
              print "<td><a href='searchup.php?title=$row[$field]&author=&publisher=' style='text-decoration:none ;color:black'>$row[$field]</a></td>";
          else
              print "<td>$row[$field]</td>";
      }
      print "</tr>";
  }
  print "</table>";
  ?>
  <div class="cleaner"></div>
  </div>
  <div class="content_box_bottom"></div>
  <?php
  }
   ?>
   <?php webresult: ?>
  </div>
  <!-- end of content -->
  <div class="cleaner"></div>
  <div id="makeMeScrollable"></div>
  <script type="text/javascript">
        function handleResponse(response) {
        for (var i = 0; i < response.items.length; i++) {
          var item = response.items[i];
          if(item.volumeInfo.industryIdentifiers[0].type=="ISBN_13") 
              isbn = item.volumeInfo.industryIdentifiers[0].identifier;
          else
              isbn = item.volumeInfo.industryIdentifiers[1].identifier;
          document.getElementById("makeMeScrollable").innerHTML += "<div class='bookDetail'>" + item.volumeInfo.title
          +"<br/>"+item.volumeInfo.authors[0]
          +"<br/>"+"<img src='"+item.volumeInfo.imageLinks.thumbnail+"'/>"
          +"<br/>"+item.volumeInfo.publisher
          +"<br/>Rating -"+item.volumeInfo.averageRating+"/5"
          +"<br/>View on"
          +"<br/><a href='"+item.accessInfo.webReaderLink+"' target='_blank'>Google Books |</a>"
          +"<a href='http://www.flipkart.com/search?q="+isbn+"' target='_blank'> Flipkart |</a>"
          +"<a href='http://www.infibeam.com/Books/search?q="+isbn+"' target='_blank'> Infibeam</a>"
          +"</div>";
        }
      }
      </script>
  <?php 
      $googleapi = "https://www.googleapis.com/books/v1/volumes?q='".$title."'+inauthor:'".$author."'+inpublisher:'".$publisher."'&callback=handleResponse";
          ?>
  <script src="<?php echo $googleapi ?>"></script> 
  <!-- jQuery library - Please load it from Google API's --> 
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" type="text/javascript"></script> 
  <!-- jQuery UI (Custom Download containing only Widget and Effects Core)
           You can make your own at: http://jqueryui.com/download --> 
  <script src="js/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script> 
  <!-- Latest version (3.1.4) of jQuery Mouse Wheel by Brandon Aaron
           You will find it here: https://github.com/brandonaaron/jquery-mousewheel --> 
  <script src="js/jquery.mousewheel.min.js" type="text/javascript"></script> 
  <!-- jQuery Kinectic (1.8.2) used for touch scrolling --> 
  <!-- https://github.com/davetayls/jquery.kinetic/ --> 
  <script src="js/jquery.kinetic.min.js" type="text/javascript"></script> 
  <!-- Smooth Div Scroll 1.3 minified--> 
  <script src="js/jquery.smoothdivscroll-1.3-min.js" type="text/javascript"></script> 
  <script type="text/javascript">
      $(document).ready(function () {
          $("div#makeMeScrollable").smoothDivScroll({
              mousewheelScrolling: "allDirections",
              manualContinuousScrolling: true,
              autoScrollingMode: "onStart"
          });
          $("div#makeMeScrollable").bind("mouseover", function() {
      $(this).smoothDivScroll("stopAutoScrolling");
      }).bind("mouseout", function() {
      $(this).smoothDivScroll("startAutoScrolling");
    });
  });
  </script>
  </div>
  <div id="templatemo_footer_wrapper">
    <div id="templatemo_footer">
      <ul class="footer_menu">
        <li><a href="http://www.jssaten.ac.in" rel="nofollow" target="_blank">JSSATE</a></li>
        <li><a href="http://210.212.85.155" target="_blank">JSS Info Centre</a></li>
        <li><a href="http://www.uptu.ac.in" target="_blank">UPTU</a></li>
        <li><a href="http://210.212.85.155/library" target="_blank">JSS Library Site</a></li>
        <li class="last_menu"><a href="aboutus.html">About Us</a></li>
      </ul>
      Copyright © 2014 <a href="#"></a> |
      Designed for JSSATE Noida</a> </div>
  </div>
  </body>
  </html>