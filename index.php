<?php
  require("include/common.php");
  require("include/mapmanager.php");
?>
<html>
  <head>
    <title>Settlers & Warlords</title>
    <script src="include/jquery.js" type="text/javascript"></script>
    <script type="text/javascript">
      
    </script>
  </head>
  <body>
    Welcome!<br />
    <br />
    ...umm, nothing works yet.  Be patient!<br />
    <br />
    <table><tr><td>
    <?php
    
      $worldx = 0;
      $worldy = 0;
      $minimapquery = getminimap(0,0,1);
      $minimap = mysqli_fetch_assoc($minimapquery);
      
      echo('<table>');
      for($y=0; $y<8; $y++) {
        echo('<tr>');
        for($x=0; $x<8; $x++) {
          $img = '';
          switch($minimap['structure']) {
            case 0:  // Nothing here, show basic lands
              switch($minimap['landtype']) {
                case 0: $img = "emptygrass"; break;  // plains
                case 1: $img = "pinetreeone"; break;  // forest
                case 2: $img = "basicdirt"; break;    // dirt
                case 3: $img = "basicrock"; break; // stone
                case 4: $img = "smallpond"; break; // water
              }
            break;
            case 1: $img = "firepit"; break;
          }
          echo('<td><img src="img/'. $img .'.jpg" /></td>');
          $minimap = mysqli_fetch_assoc($minimapquery);
        }
        echo('</tr>');
      }
      echo('</table>');
    ?>
    </td><td>
      <?php /* now that we have our map, lets put some tasks to do on the right */ ?>
      Idle workers: <b>4</b><br />
      <br />
      <b>Getting started tasks</b><br />
      1) Gather more firewood<br />
      2) Gather sticks<br />
      3) Gather food<br />
      4) Collect stones<br />
      5) Setup workshop<br />
      6) Setup storage space
    </table>
  </body>
</html>