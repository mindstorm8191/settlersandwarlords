<?php
  // Mapmanager.php
  // Holds functions that help manage the map for this game
  
  function getminimap($worldx, $worldy, $newplayerspot=0) {
    // Grabs the content of the minimap for the given world coordinates, or if it doesn't exist, generates some
    // worldx, worldy = coordinates on the world map that this player is at
    // newplayerspot = set to 1 if this is a new player location, which then requires an existing campfire
    
    // This function will need to be modified later to account for actual new players being added; we will probably
    
    // I realize that in most game setups, minimap refers to a pixel-based map in the corner for tracking things, but I didn't know what else to call the sub-map layer
    // when I started coding them. 
    
    $mapspotquery = danquery("SELECT * FROM map WHERE x = 0 AND y = 0;",
                             'include/mapmanager.php->getminimap()->get map location');
    $mapspot = mysqli_fetch_assoc($mapspotquery);
    if($mapspot) {
      $minimapquery = danquery("SELECT * FROM minimap WHERE mapid=". $mapspot['id'] ." ORDER BY y,x",
                               'include/mapmanager.php->getminimap()->load existing minimap');
      return $minimapquery;
    }else{
      $source = imagecreatefrompng("img/2layermap.png");
      if(!$source) die("fail - source image not found");
      $width = imagesx($source);   
      $height = imagesy($source);
      $startx = -floor($width/2);  // These values may end up smaller than the actual image, but we're not concerned with
      $endx   =  floor($width/2);  // including the edges, only that the zero line is included correctly.
      $starty = -floor($height/2);
      $endy   =  floor($height/2);
      $translatex = $startx;
      $translatey = $starty;
        
      $color = imagecolorat($source, $translatex, $translatey);
      $r = ($color >> 16) & 0xFF;
      $g = ($color >>  8) & 0xFF;
      $b =  $color        & 0xFF;
        
      // structures list
      // 0 - nothing built here
      // 1 - campfire.  Usually a plains block is selected for this
        
      $newid = 0;
      $b = array();
      if(aboutEqual($r,255,5)) {
       // this block is red.  Mark it as forest
        danquery("INSERT INTO map (x,y,owner,biome,ugresource,ugamount) VALUES (0,0,0,1, FLOOR(RAND()*4), (RAND()*1.5)+0.5);",
                 'index.php->no block found->add forest block');
        $newid = mysqli_insert_id($db);
                               
          // Now, we need to generate a 8x8 grid with a specific number of block types. Later, we'll add ranges to what is added
        $b = array(3,3,3,2,2,2,4,4,4);               // 3 stone blocks, 3 dirt blocks, 3 water blocks
        for($i=0;$i<10;$i++) { array_push($b, 0); }  // 10 plains
        for($i=0;$i<45;$i++) { array_push($b, 1); }  // The rest is forest blocks
      }else{
        // This block is not red.  Mark it as plains
        danquery("INSERT INTO map (x,y,owner,biome,ugresource,ugamount) VALUES (0,0,0,0, FLOOR(RAND()*4), (RAND()*1.5)+0.5);",
                 'index.php->no block found->add plains block');
        $newid = mysqli_insert_id($db);
        
        // Like before, generate an 8x8 grid with specific number of block types, scattered about
        $b = array(3,3,3,2,2,2,4,4,4);               // 3 stone blocks, 3 dirt blocks, 3 water blocks
        for($i=0;$i<10;$i++) { array_push($b, 1); }  // 10 forest blocks
        for($i=0;$i<45;$i++) { array_push($b, 0); }  // The rest is plains blocks
      }
      shuffle($b);  // Randomize the ordering of the array
      // Now, generate a series of MySQL insertion instances for this
      $a = '';
      for($i=0;$i<64;$i++) {
        $a .= '('. $newid .','. ($i%8) .','. floor($i/8.0) .','. $b[$i] .'),';
      }
      $a = substr($a, 0, strlen($a)-1);  // Trims off the last comma, to not break the sql statement
      
      danquery("INSERT INTO minimap (mapid, x, y, landtype) VALUES ". $a .";",
               'index.php->fill new minimap');
      // With this built now, we still need to select a spot for the campfire.  Let's update a record within this set at random.
      danquery("UPDATE minimap SET structure=1 WHERE mapid=". $newid ." AND landtype=0 ORDER BY RAND() LIMIT 1;",
               'index.php->add campfire to new minimap');
      
      // Now that we know the minimap portion has been built, let's load it in... in order.
      $minimapquery = danquery("SELECT * FROM minimap WHERE mapid=". $newid ." ORDER BY y,x;",
                              'index.php->read minimap when building');
      return $minimapquery;
    }
  }
?>