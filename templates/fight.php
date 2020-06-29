<?php
$template = <<<THEVERYENDOFYOU
<table width="451">
<tr><td colspan="2" class="title"><img src="images/title_fighting.gif" alt="Fighting" /></td>
  </tr>
<tr>
  <td width="257">You are fighting a.. <b><u>{{monstername}}</u>!</b><br />
    <br />
    <b><u>Available Stats</u></b> <br>
    Weapon: {{cweap}} <br>
    Armor: {{carm}} <br>
    Shield: {{cshield}} <br>
    Monster's Level: {{monsterlevel}} <br>
    {{monsterhp}} {{yourturn}} </td>
  <td width="182"><img src="images/monsters/{{monsterid}}.gif" alt="{{monstername}}" border="0" align="absmiddle"></td>
</tr>
<tr><td colspan="2">{{monsterturn}} {{command}} 
</td>
  </tr>
</table>
THEVERYENDOFYOU;
?>