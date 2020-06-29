<?php
$template = <<<THEVERYENDOFYOU

<table width=100% border=1><tr><td class=title>Character Profile</td></tr></table><p>

Here is the character profile for <b>{{charname}}</b>.<br /><br />
When you're finished, you may <a href="index.php">return</a> to what you were doing and continue your adventure.<br /><br />

<table width="100%">
<tr><td colspan="3" class="title"><img src="images/button_character.gif" alt="Character" title="Character" /></td>
  </tr>
<tr>
  <td width="37%"><span class="profile"><b><u>Profile of {{charname}}</u></b><br /><br />
  {{avatarlink}}
    <p><b><u>Location</u></b><br />
  Currently: {{location}}<br />
  Town: {{currenttown}}<br />
  Latitude: {{latitude}}<br />
  Longitude: {{longitude}}<br />
  Zone: {{zone}}<br />
  Movement: {{run}}<br />
    Constructed a Home: {{home}}<br />
    <p> <b><u>Stats of {{charname}}</u></b> <br>
      ID Number: {{id}} <br>
      Username: {{username}} <br>
      Title: {{title}} <br>
  Class: {{charclass}}<br />
    <p> Level: {{level}}<br />
  Gold: {{gold}} {{plusgold}}<br />
  Stored Gold: {{bank}}<br />
  Bones: {{bones}}<br />
  Guild: {{guildname}}<br />
  Guild Rank: {{guildrank}}<br />
  Experience: {{experience}} / {{nextlevel}} {{plusexp}}<br />
   Total Exp: {{totalexp}}<br />
    Skill Total: {{skilltotal}}<br />
    Attributes: {{attributes}}<br />
    Magic Find: {{magicfind}}   <br />
    <p> Hit Points: {{currenthp}} / {{maxhp}}<br />
  Magic Points: {{currentmp}} / {{maxmp}}<br />
  Travel Points: {{currenttp}} / {{maxtp}}<br />
  Ability Points: {{currentap}} / {{maxap}}<br />
  Fatigue: {{currentfat}} / {{maxfat}}<br />
  <br />
  Strength: {{strength}}<br />
  Dexterity: {{dexterity}}<br />
  Attack: {{attackpower}}<br />
  Defense: {{defensepower}}     
    <p><u><b>Duels</b></u>:<br>
Victories: {{numbattlewon}}<br />
Defeats: {{numbattlelost}}<p> <u><b>Other Information</b></u>:<br>
  Post Count: {{postcount}}<br>
  Poll: {{poll}}<br>
  Name: {{name}}<br>
  Gender: {{gender}}<br>
  Location: {{country}}<br>
  MSN: {{msn}}<br>
  AIM: {{aim}}<br>
  YIM: {{yim}}<br>
  ICQ: {{icq}}<br>
  Registered: {{regdate}}<br />
  Last Online: {{onlinetime}}
    <form action='gamemail.php?do=write' method='post'>
      <input type='hidden' name='recipient' value='{{charname}}'>
      <input type='submit' name='Email' value='Send Game Mail'>
    </form></td>
  <td width="33%"><span class="profile"><p><b><u>Quests:</u></b> <br>
  Quests Completed: {{questscomplete}}<p>
  Lost Fortune: {{quest1}} <br>
  Potion Assistant: {{quest2}}
  <br>Mad Scientist: {{quest3}} 
<br>The Parasite: {{quest4}}
  </p>
    <p><u><b>Skill Levels</b></u>:<br>
      Mining Level: {{mining}}<br>
      Mining Exp: {{miningxp}}<br>
    Smelting Level: {{smelting}}<br>
Smelting Exp: {{smeltingxp}}  <br>
Forging Level: {{forging}}<br>
Forging Exp: {{forgingxp}}  <br>
      Endurance Level: {{endurance}}<br>
      Endurance Exp: {{endurancexp}}<br>
    Crafting Level: {{crafting}}<br>
        Crafting Exp: {{craftingxp}}<br>
          </p>
    <p>Wisdom Level: {{skill1level}}<br />
    Stone Skin Level: {{skill2level}}<br />
    Monks Mind Level: {{skill3level}}<br />
    Fortune Level: {{skill4level}}
</p>
    <p><strong><u>Mining Ores</u>:<br></strong>Copper: {{ore1}}<br>
    Tin: {{ore2}}<br>
    Iron: {{ore3}}<br>
Magic: {{ore4}}<br>
    Dark: {{ore5}}<br>
Bright: {{ore6}}<br>
    Destiny: {{ore7}}<br>
Crystal: {{ore8}}<br>
    Diamond: {{ore9}}<br>
Heros: {{ore10}}<br>
    Holy: {{ore11}}<br>
Mythical: {{ore12}}<br>
    Black Dragons: {{ore13}}<p>
    
        <strong><u>Smelting Bars</u>:</strong><br>
        Bronze: {{bar1}}<br>
        Iron: {{bar2}}<br>
        Magic: {{bar3}}<br>
        Dark: {{bar4}}<br>
        Bright: {{bar5}}<br>
        Destiny: {{bar6}}<br>
        Crystal: {{bar7}}<br>
        Diamond: {{bar8}}<br>
        Heros: {{bar9}}<br>
        Holy: {{bar10}}<br>
        Mythical: {{bar11}}<br>
        Black Dragons: {{bar12}} </p>
    <p><strong><u>Crafting Items</u>:</strong><br>
String: {{string}}<br>
    Gold Nuggets: {{nuggets}}<br>
Sapphires: {{gem1}}<br>
Emeralds: {{gem2}}<br>
Rubys: {{gem3}}<br>
Diamonds: {{gem4}}<br>
Black Dragons: {{gem5}}    <p>
    </td>
  <td width="30%"><img src="images/class/ranger.gif" alt="Ranger" title="Ranger" />
<table width="100%">
  <tr>
    <td class="title"><img src="images/button_spells.gif" alt="Spells" title="Spells" /></td>
  </tr>
  <tr>
    <td>
      <table width="100%">
        <tr>
          <td><span class="profile">{{magiclist}}</td>
        </tr>
      </table>
      <p> </td>
  </tr>
</table>
<table width="100%">
  <tr>
    <td class="title"><img src="images/button_towns.gif" alt="Towns" title="Towns" /></td>
  </tr>
  <tr>
    <td>
      <table width="100%">
        <tr>
          <td>              <p><span class="profile">{{townslist}}</td>
        </tr>
      </table>
      <p> </td>
  </tr>
</table>
<p>&nbsp;</p></td>
</tr>
</table>
<br />

<table width="100%">
  <tr>
    <td width="53%"><table width="100%">
      <tr>
        <td class="title"><img src="images/button_inventory.gif" alt="Inventory" title="Inventory" /></td>
      </tr>
      <tr>
        <td>
          <table width="100%" span class="profile">
                                  <tr>
              <td><img src="images/icon_helm.gif" alt="Helm" title="Helm" /></td>
              <td>Helm: {{helmname}}</td>
            </tr>
            <tr>
              <td><img src="images/icon_weapon.gif" alt="Weapon" title="Weapon" /></td>
              <td width="100%">Weapon: {{weaponname}}</td>
            </tr>
            <tr>
              <td><img src="images/icon_armor.gif" alt="Armor" title="Armor" /></td>
              <td>Armor: {{armorname}}</td>
            </tr>
                        <tr>
              <td><img src="images/icon_legarmor.gif" alt="Leg Armor" title="Leg Armor" /></td>
              <td>Leg Armor: {{legsname}}</td>
            </tr>
            <tr>
              <td><img src="images/icon_shield.gif" alt="Shield" title="Shield" /></td>
              <td>Shield: {{shieldname}}</td>
            </tr>
                        <tr>
              <td><img src="images/icon_gauntlet.gif" alt="Gauntlets" title="Gauntlets" /></td>
              <td>Gauntlets: {{gauntletsname}}</td>
            </tr>
            <tr>
              <td><img src="images/icon_amulet.gif" alt="Amulet" title="Amulet" /></td>
              <td>Amulet: {{amuletname}} </td>
            </tr>
            <tr>
              <td><img src="images/icon_ring.gif" alt="Ring" title="Ring" /></td>
              <td>Ring: {{ringname}} </td>
            </tr>
                        <tr>
              <td><img src="images/icon_paxe.gif" alt="Pickaxe" title="Pickaxe" /></td>
              <td>Pickaxe: {{pickaxe}} </td>
            </tr>
            <tr>
              <td><img src="images/icon_drink.gif" alt="Tavern Drink" title="Tavern Drink" /></td>
              <td>Tavern Drink: {{drink}}</td>
            </tr>
            <tr>
              <td><img src="images/icon_potion.gif" alt="Current Potion" title="Current Potion" /></td>
              <td>Current Potion: {{potion}}</td>
            </tr>
            <tr>
              <td><img src="images/icon_dscale.gif" alt="Dragon Scales" title="Dragon Scales" /></td>
              <td>Dragon Scales: {{dscales}} </td>
            </tr>
                          <tr>
              <td colspan="2">Christmas 2004: {{xmas2004}} (<i>+7 TP & +50 Defense</i>)<p>Halloween 2004: {{hween2004}} (<i>+15 Dexterity & +50 Attack</i>)<p>Easter 2005: {{easter2005}} (<i>+50 HP & +10 Magic Find</i>)</td>
              </tr>
          </table>
          <p>
          <table width="100%" span class="profile">
            <tr>
              <td width="2%"><img src="images/icon_arrow.gif" alt="Slot 1" title="Slot 1" /></td>
              <td width="98%">Slot 1: {{slot1name}}</td>
            </tr>
            <tr>
              <td><img src="images/icon_arrow.gif" alt="Slot 2" title="Slot 2" /></td>
              <td width="98%">Slot 2: {{slot2name}}</td>
            </tr>
            <tr>
              <td><img src="images/icon_arrow.gif" alt="Slot 3" title="Slot 3" /></td>
              <td width="98%">Slot 3: {{slot3name}}</td>
            </tr>
            <tr>
              <td><img src="images/icon_arrow.gif" alt="Slot 4" title="Slot 4" /></td>
              <td>Slot 4: {{slot4name}}</td>
            </tr>
            <tr>
              <td><img src="images/icon_arrow.gif" alt="Slot 5" title="Slot 5" /></td>
              <td>Slot 5: {{slot5name}}</td>
            </tr>
                        <tr>
              <td><img src="images/icon_arrow.gif" alt="Slot 6" title="Slot 6" /></td>
              <td>Slot 6: {{slot6name}}</td>
            </tr>
                       <tr>
              <td><img src="images/icon_arrow.gif" alt="Slot 7" title="Slot 7" /></td>
              <td>Slot 7: {{slot7name}}</td>
            </tr>
                        <tr>
              <td><img src="images/icon_arrow.gif" alt="Slot 8" title="Slot 8" /></td>
              <td>Slot 8: {{slot8name}}</td>
            </tr>
        </table></td>
      </tr>
    </table>      <br />    </td>
    <td width="47%">
      <table width="100%">
        <tr>
          <td class="title"><img src="images/button_items.gif" alt="Held Items" title="Held Items" /></td>
        </tr>
        <tr>
          <td>
            <table width="100%" span class="profile">

                <td><p>{{inventitemslist}}</span>
              </tr>
            </table>
        </tr>
      </table>


</table>
THEVERYENDOFYOU;
?>
