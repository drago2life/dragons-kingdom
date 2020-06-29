<?php
$template = <<<THEVERYENDOFYOU
<table width="100%">
<tr><td class="title"><img src="images/button_location.gif" alt="Location" title="Location" /></td></tr>
<tr><td>
Currently: {{location}}<br />
Latitude: {{latitude}}<br />
Longitude: {{longitude}}<br />
Zone: {{zone}}<br />
{{homes}}
Movement: {{run}}<br />
<a href="javascript:openmappopup()">View Battle Field Map</a><br /><br />
<center>
<div id="compassdiv" style="visibility: hidden">
<img usemap="#direction" src="images/compass.gif" border="0"/>
<map name="direction">
  <area shape="poly" alt="Move North West" onclick="move()" onmouseover="setdir('North West')" title="Move North West" coords="63,61,36,5,5,5,5,39" />
  <area shape="poly" alt="Move South West" onclick="move()" onmouseover="setdir('South West')" title="Move South West" coords="64,65,41,124,5,124,5,91" />
  <area shape="poly" alt="Move South East" onclick="move()" onmouseover="setdir('South East')" title="Move South East" coords="68,66,125,90,125,124,94,124" />
  <area shape="poly" alt="Move North East" onclick="move()" onmouseover="setdir('North East')" title="Move North East" coords="67,60,91,5,125,5,125,38" />
  <area shape="poly" alt="Move West" onclick="move()" onmouseover="setdir('West')" title="Move West" coords="63,63,5,42,5,88" />
  <area shape="poly" alt="Move South" onclick="move()" onmouseover="setdir('South')" title="Move South" coords="66,66,91,124,43,124" />
  <area shape="poly" alt="Move East" onclick="move()" onmouseover="setdir('East')" title="Move East" coords="67,63,125,41,125,87" />
  <area shape="poly" alt="Move North" onclick="move()" onmouseover="setdir('North')" title="Move North" coords="65,60,39,5,89,5" />
</map>
<br />Move:
<br />
<form name="compass" action="index.php?do=move" method ="POST" onsubmit="carrot()">
    <input type="submit" name="direction" value="Direction?" disabled />
</form>
</div>
<div style="color: red; text-align: center" id="warningdiv">Compass load delayed to prevent Power Clicking and reduce Server Load.</div>
<table width="92" height="49" border="0">
  <tr>
    <td><form action="index.php?do=runoff" method="post">
<input name="runon" type="submit" value="Walk" /> 
</form></td>
    <td><form action="index.php?do=runon" method="post">
<input name="runon" type="submit" value="Run" /> 
</form></td>
  </tr>
</table>
</center>
</td></tr>
</table>
<table width="100%">
<tr><td class="title"><img src="images/button_fastspells.gif" alt="Fast Spells" title="Fast Spells" /></td></tr>
<tr><td>
{{magiclist}}
</td></tr>
</table><br />
<table width="100%">
<tr><td class="title"><img src="images/button_towns.gif" alt="Towns" title="Towns" /></td></tr>
<tr><td>
{{currenttown}}
Your purchased Maps.<br />
<p>Travel To:<br />
{{townslist}}
</td></tr>
</table><br />

THEVERYENDOFYOU;
?>
