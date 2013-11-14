<html>
<head>
    <title>iOS App Icon Compare - How does your icon stack up against the rest?</title>
</head>
<body>
<?php

$genreNumbers = array( "Games" => "6014",
"Books" => "6018",
"Business" => "6000",
"Catalogs" => "6022",
"Education" => "6017",
"Entertainment" => "6016",
"Finance" => "6015",
"Food & Drink" => "6023",
"Health & Fitness" => "6013",
"Lifestyle" => "6012",
"Medical" => "6020",
"Music" => "6011",
"Navigation" => "6010",
"News" => "6009",
"Newsstand" => "6021",
"Photo & Video" => "6008",
"Productivity" => "6007",
"Reference" => "6006",
"Social Networking" => "6005",
"Sports" => "6004",
"Travel" => "6003",
"Utilities" => "6002",
"Weather" => "6001",
"* All Genres *" => "nothing"
 );


$listIDs = array( "Top Paid Apps" => "toppaidapplications",
"Top Free Apps" => "topfreeapplications",
"Top Grossing Apps" => "topgrossingapplications",
"Top Paid iPad Apps" => "toppaidipadapplications",
"Top Free iPad Apps" => "topfreeipadapplications",
"Top Grossing iPad Apps" => "topgrossingipadapplications"
 );

function get_url_contents($url){
        $crl = curl_init();
        $timeout = 5;
        curl_setopt ($crl, CURLOPT_URL,$url);
        curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
        $ret = curl_exec($crl);
        curl_close($crl);
        return $ret;
}

function iconImageURLsInRSS( $rssString )
{
    $theMatches = array();
    $theOffset = 0;
    $howMany = preg_match_all("/image height[=][\"]75[\"][>](.*)[<]\/im[:]image[>]/", $rssString, $theMatches, PREG_PATTERN_ORDER);
    echo( "<!--$howMany app icons-->");
    
    return $theMatches;
}

function getAppNames( $rssString )
{
    $theMatches = array();
    $theOffset = 0;
    $howMany = preg_match_all("/title[>](.*)[<]\/title[>][^<]/", $rssString, $theMatches, PREG_PATTERN_ORDER);
    
    echo( "<!--$howMany app names-->");
    return $theMatches;
}



function displayIconGridForIconURL( $localIconURL, $genreID, $listID )
{
    $gridWidth = 6; 
    $gridHeight = 5;

    $iconSpacing = 15;
    
    if( $listID == "" )
    {
        $listID = "toppaidapplications";
    }
    
    $rssURL = "https://itunes.apple.com/us/rss/" . $listID . "/limit=100";
    if( $genreID != "nothing" )
    {
        $rssURL .= "/genre=" . $genreID ;
    }
    $rssURL .=  "/xml" ;
    
    echo( "<!-- RSS feed URL: [$rssURL] -->");
    $top100GamesRSS = get_url_contents( $rssURL );

    
    $imageMatches = iconImageURLsInRSS( $top100GamesRSS );
    $appMatches = getAppNames( $top100GamesRSS );
    $imageURLs = $imageMatches[1];
    $appNames = $appMatches[1];
    
    $iconURLsToDisplay = array();
    $iconAppNamesToDisplay = array();
    
    $howManyIconsToDisplay = $gridHeight * $gridWidth;
    $iconsDisplayed = 0;
    
    $localIconIndex = rand( 0, ($howManyIconsToDisplay - 1) );
    
    $iconIndex = rand(0, count($imageURLs) );
    
    for( $whichIcon = 0; $whichIcon < $howManyIconsToDisplay; $whichIcon++ )
    {
        if( $whichIcon == $localIconIndex )
        {
            $iconURLsToDisplay["local"] = $localIconURL;
            continue;
        }
            
        while($iconURLsToDisplay["$iconIndex"] != "" )
        {
            $iconIndex = rand(0, (count($imageURLs) - 1) );
        }
        
        $iconURLsToDisplay["$iconIndex"] = $imageURLs[$iconIndex];
        $iconAppNamesToDisplay["$iconIndex"] = $appNames[$iconIndex];
        
        //echo( "<!-- icon $iconIndex is " . $imageURLs[$iconIndex] . ", named " . $appNames[$iconIndex] . "-->\n" );
    }
    
    $whichIcon = 0;
    
    ?>
    <table width="<?php echo ($gridWidth * (75 + $iconSpacing * 2) ); ?>" cellspacing="<?php echo $iconSpacing; ?>" border="0">
    <tr>
    <?php
    
    foreach( $iconURLsToDisplay as $key => $iconURL )
    {
        if( $whichIcon % $gridWidth == 0 )
        {
            echo "</tr>\n<tr>";
        }
        echo( "<td> <img src=\"$iconURL\" width=75 height=75 border=0 alt=\"" . $iconAppNamesToDisplay[$key] . "\"> </td>\n");
        //echo "<!--show icon $whichIcon<br>-->";
        $whichIcon++;
    }
    
    ?>
    </tr>
    </table>
    <?php
}

$iconURL = "";
if( isset($_GET["iconURL"])  )
{
    $iconURL = $_GET["iconURL"];
}

$genreID = "";
if( isset($_GET["genre"]))
{
    $genreID = $_GET["genre"];
}

$listID = "";
if( isset($_GET["list"]))
{
    $listID = $_GET["list"];
}

?>

<h1>Icon Tester</h1>
<h3>Does your app icon stick out amongst some of the most popular apps in your category?</h3>
<p><a href="http://www.idevgames.com/forums/thread-10291.html">Inspired by Seth Willits's comment in this iDevGames thread</a></p>
<form method="get" action="#">
Your icon URL (75x75): <input type="text" size="80" name="iconURL" id="iconURL" value="<?php echo $iconURL;
?>"><br />
Category: 
<select name="genre">
<?php
   foreach( $genreNumbers as $key => $value )
    {
        if( $value == $genreID)
        {
            echo( "<option value=\"$value\" selected=\"yes\">$key</option>/n");
        }
        else
        {
            echo( "<option value=\"$value\">$key</option>/n");
        }
        
    }
?>
</select><br />

List: 
<select name="list">
<?php
   foreach( $listIDs as $key => $value )
    {
        if( $value == $listID)
        {
            echo( "<option value=\"$value\" selected=\"yes\">$key</option>/n");
        }
        else
        {
            echo( "<option value=\"$value\">$key</option>/n");
        }
        
    }
?>
</select><br />

<p><i>Data taken from the U.S. charts.</i></p>
<input type="submit" name="Show My Grid" id="Show My Grid" title="Show My Grid">

</form>

<?php

if( isset($_GET["iconURL"])  )
{
    
    if( strlen( $iconURL ) <= 8 )
    {
        echo "Put in the complete URL of your icon.";
    }
    else
    {
        $genreID = $_GET["genre"];
        displayIconGridForIconURL($iconURL, $genreID, $listID);
    }
}

?>
</body></html>