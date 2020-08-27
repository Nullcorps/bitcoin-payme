<?php


$dbg = false;  // << Change this to true to enable debug mode and complete setup.
               // During setup, especially if some of your addresses are used,
               // refresh the page a few times till it's cleaned out all the
               // used addresses.


$nl = "<br>\n";
$filespath = "add";
$arfresh = "";
$arused = "";
$addresses_to_generate = 100;          
$api_preference = "blockchain.info"; // or blockchain.info
$vendor_name = "";
$vendor_signature = "";

use BitWasp\Bitcoin\Address\PayToPubKeyHashAddress;
use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Crypto\Random\Random;
use BitWasp\Bitcoin\Key\Factory\HierarchicalKeyFactory;
   
//require __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . '/vendor/autoload.php';


?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="main.css">
<style>

</style>
</head>
<body>
<?php

$showdbg = "none";

if ($dbg) { $showdbg = "block"; }


echo "<div id=dbg style=\"display: " . $showdbg . "\">";
if ($dbg) { echo "- get files folder" . $nl; }
$folder = get_files_folder();
if ($dbg) { echo "Folder: " . $folder . $nl; }

if ($dbg) { echo "- xpub in" . $nl; }

if (file_exists($folder . "/xp.txt"))
   {
   if ($dbg) { echo "- xpub exists, carry on" . $nl; }
   $xpub = trim(file_get_contents($folder . "/xp.txt"));
   if ($dbg) { echo "xpub retrieved, ending: " . substr($xpub,4,-4) . $nl; }
   }
else
   {
   echo "- xpub not found, form to enter" . $nl; 
   if ( isset($_POST['x']) )
      {
      if ($_POST['x'] <> "")
         {
         $xpub = $_POST['x'];
         echo "xpub entered as: " . $nl . $xpub . $nl; 
         file_put_contents($folder . "/xp.txt", $xpub);
         echo "Saved"; 
         }
      }
   else
      {
      echo "<div class=setupform>";
      echo "<form method=post>\n";
      echo "<h3>Enter your xpub to save</h3>\n";
      echo "You can find it in your Electrum wallet on the menu: wallet/information. It starts xpub and then has a bunchof numbers)" . $nl . $nl;
      echo "<input type=text name=x size=30 maxlength=200>\n";
      echo "<input type=submit value=Save>" . $nl;
      echo "</form></div>\n";
      }
   }

echo $nl;



if (file_exists($folder . "/name.txt"))
   {
   if ($dbg) { echo "- vendor name exists, carry on" . $nl; }
   $vendor_name = trim(file_get_contents($folder . "/name.txt"));
   if ($dbg) { echo "name retrieved: " . $vendor_name . $nl; }
   }
else
   {
   echo "- name not found, form to enter" . $nl;
   if ( isset($_POST['vendor_name']) )
      {
      if ($_POST['vendor_name'] <> "")
         {
         $vendor_name = $_POST['vendor_name'];
         echo "Vendor name entered as: " . $nl . $vendor_name . $nl;
         file_put_contents($folder . "/name.txt", $vendor_name);
         echo "Saved";
         }
      }
   else
      {
      echo "<div class=setupform>";
      echo "<form method=post>\n";
      echo "<h3>Enter your vendor name to save</h3>\n";
      echo "e.g. 'Mistress XYZ' or whatever. Short n sweet ftw." . $nl . $nl;
      echo "<input type=text name=vendor_name size=30 maxlength=200>\n";
      echo "<input type=submit value=Save>" . $nl;
      echo "</form></div>\n";
      }
   }

echo $nl;






if (file_exists($folder . "/sig.txt"))
   {
   if ($dbg) { echo "- vendor sig exists, carry on" . $nl; }
   $vendor_sig = trim(file_get_contents($folder . "/sig.txt"));
   if ($dbg) { echo "sig retrieved: " . $vendor_sig . $nl; }
   }
else
   {
   echo "- sig not found, form to enter" . $nl;
   if ( isset($_POST['vendor_sig']) )
      {
      if ($_POST['vendor_sig'] <> "")
         {
         $vendor_sig = $_POST['vendor_sig'];
         echo "Vendor sig entered as: " . $nl . $vendor_sig . $nl;
         file_put_contents($folder . "/sig.txt", $vendor_sig);
         echo "Saved";
         }
      }
   else
      {
      echo "<div class=setupform>";
      echo "<form method=post>\n";
      echo "<h3>Enter your vendor sig to save</h3>\n";
      echo "e.g. 'Mx' or whatever. Short n sweet ftw." . $nl . $nl;
      echo "<input type=text name=vendor_sig size=30 maxlength=200>\n";
      echo "<input type=submit value=Save>" . $nl;
      echo "</form></div>\n";
      }
   }

if ($dbg)
   { 
   echo $nl;
   echo "- derivation point start (m/1/n)" . $nl;
   echo $nl;
   }
   
$generate_addresses = false;

if ($dbg) { echo "- get the next address" . $nl; }

if (file_exists($folder . "/fresh.txt"))
   {
   //$tmp = file_get_contents($folder . "/fresh.txt");
   //$arfresh = explode("\n",$tmp);
   }
else
   {
   $generate_addresses = true;
   }   

   
   
if (file_exists($folder . "/used.txt"))
   {
   //$tmp = file_get_contents($folder . "/used.txt");
   //$arused = explode("\n",$tmp);
   }
else
   {
   file_put_contents($folder . "/used.txt", "");
   //$tmp = "";
   //$arused = explode($tmp,"\n");
   }
   
if ($dbg) { echo $nl; }

   
if ( $generate_addresses && $xpub <> "" )
   {
   echo "<div class=setupform>";
   echo "GENERATE ADDRESSES!" . $nl;
   generate_addresses($xpub);
   echo "</div>\n";      
   }
echo $nl;


if ($dbg) { echo "- - check the fresh addresses stack" . $nl; }
if ($dbg) { echo "- - check the address's conf and unconf balance is 0 before issuing" . $nl; }
$btcaddress = get_fresh_address();
if ($dbg) { echo "ADDRESS TO SEND TO: " . $btcaddress . $nl; }
if ($dbg) { echo $nl; }

if ($dbg) { echo "- throw up QR & rest of page" . $nl . $nl; }

if ($dbg) { echo "<div id=setupdone>Ok If you got to this point and you see the QR code, your name and signoff on the page you can turn debug mode off again now and you're all set :)<br><br></div>"; }

echo "</div>";


if (substr($btcaddress,0,5) <> "ERROR" && $vendor_name <> "" && $vendor_sig <> "")
   {

echo "<script src=\"kjua-0.1.1.min.js\"></script>\n";
echo "<center><div id=container style=\"\">";

echo "<div id=header>Pay " . $vendor_name . " with Bitcoin</div>";
echo "<center><div style=\"width: 420px; text-align: center;\"><div id=oink style=\"border: 1px solid black; background-color: #ffffff; width; 420px; height: 420px; padding-top: 0px; text-align: center;\"></div></div></center>\n";
echo "<script language-javascript>
var url = '" . $btcaddress . "';
//var opts = \"\";
//opts = opts + \"render: 'image', crisp: true, minVersion: 1, ecLevel: 'L', size: 400, ratio: null, fill: '#333', back: '#fff', text: 'Pay with Bitcoin', rounded: 0, \";
//opts = opts + \"quiet: 0, mode: 'plain', mSize: 30, mPosX: 50, mPosY: 50, label: 'label test', fontname: 'sans', fontcolor: '#333', image: null\";
var el = kjua({text: url, label: 'Pay with Bitcoin', size: 400, crisp: true, back: '#fff' });
document.getElementById('oink').appendChild(el);

function clearfield(f)
   {
   //alert('clear field with name: ' + f);
   var t = document.getElementById(f); 
   setTimeout(\"document.getElementById('\"+f+\"').innerHTML = '&nbsp;'\",1000);
   }


</script>
<br>
<strong>BTC ONLY - NO SHICOINS</strong><br>
<input type=text id=btcaddress onclick=\"this.setSelectionRange(0, 99999); document.execCommand('copy'); document.getElementById('tmpinfo').innerHTML = 'Address copied'; clearfield('tmpinfo');\" value=\"" . $btcaddress . "\">
<div id=tmpinfo>Click to copy the address</div>
</div>";

echo $nl;
echo "<div id=halp>
  Bitcoin is " . $vendor_name . "'s favourite currency.<br><br>This page lets you send me Bitcoin.<br><br>If you don't have Bitcoin already then 
  go and make an account on <a href=\"https://coinbase.com\" target=\"_blank\" title=\"Coinbase=Feds but they are the easiest onramp for a slut like yourself and you're sending it to me for off-exchange self-custody anyway\">coinbase.com</a>
  or get the <a href=\"https://play.google.com/store/apps/details?id=com.coinbase.android&hl=en_GB&pageId=none\" title=\"coinbase are evil, try bitbargain or literally any other way next time\">coinbase app</a> and you'll be able to buy some right away.
  <br><br>
  Coinbase = Paypal for Bitcoin.<br>
  Register your credit card then buy some Bitcoin and send it to the address above.<br><br>
  This is actually pretty straightforward. You just copy the address or scan the QR code
  if you are using the app, then hit \"MAX\" and send. Double check the address you're
  sending to - I normally check the first few digits and the last few and if they're right
  and you copied/pasted you're good. <br><br>
  You can do this, and just think how you'll feel as you hit send. The excitement! Savour
  that feeling. This is how you pay me now and I get an orgasm-like full body tingle that
  lasts about 30 sec when someone pays me in BTC - it's one of my favourite feelings.
  You can get back to this page any time by going to my site and hitting the \"send BTC\" link.
  Learn to LOVE this process. If you're doing this process then good things have either just
  happened or are about to happen (lucky you!). This is your life now, slut ;)
<br><br>
PLEASE NOTE: ONLY BUY BITCOIN WHICH ALWAYS HAS THE TICKER \"BTC\". NOT \"BITCOIN CASH\" OR
ANY OF THE OTHER SHITTY CLONES. THEY ARE *NOT* THE SAME. YOU'LL LOSE YOUR MONEY AND I'LL BE
SUPER PISSED OFF TOO SO JUST DON'T. ONLY BITCOIN/BTC<br><br>" . $vendor_sig . "<br>
</div>
<div style=\"padding: 20px\"><input id=halpbutton type=button value=\"CLICK ME IF YOU DONT KNOW WHAT TO DO WITH THIS PAGE\" style=\"font-family: verdana, arial; font-size: 16px; padding: 12px;\" onclick=\"document.getElementById('halp').style.display = 'block'; document.getElementById('halpbutton').style.display = 'none';\"></div>
</center>";
   }
else
   {
   echo "<div id=halp2>Setup is not complete, please enable debug mode, complete setup and then you'll be good to go :)</div>" . $nl;
   }

echo "
</body>
</html>";



function get_fresh_address()
   {
   global $nl;
   global $api_preference;
   global $dbg;
   
   $folder = get_files_folder();
   
   $addpath = $folder . "/fresh.txt";
   $usedpath = $folder . "/used.txt";
   if ($dbg) { echo "Get fresh address" . $nl; }
   if ($dbg) { echo "Files path for addresses: " . $folder . $nl; }
   
   $adds = "";
   $aradds = [];
   $nextadd = "";
   
   if(file_exists($addpath))
      {
      $adds = file_get_contents($addpath);
      $aradds = explode("\n",$adds);
      $cnt = 0;
      
      
      do {
         $addused = "";
         $nextadd = $aradds[$cnt];
         if ($dbg) { echo "Next address: " . $nextadd . $nl; }
         
         $isfresh = "";
         
         
         $tmpused = file_get_contents($folder . "/used.txt");
         $artmpused = explode("\n",$tmpused);
         

         if ( in_array($nextadd, $artmpused) )
            {
            if ($dbg) { echo "It's in the used list - remove this one" . $nl; }
            $addused = "USED";
            }
         else
            {
            if ($dbg) { echo "Looks like it's not been used by us" . $nl; }
            if ($dbg) { echo "check address history.." . $nl; }
            if ($api_preference == "blockstream.info")
               {
               if ($dbg) { echo "Doing Blockstream.info" . $nl; }
               }
            else
               {
               if ($dbg) { echo "Doing Blockchain.info" . $nl; }
               $tmp = is_address_fresh_bc($nextadd, false);
               if ($dbg) { echo "tmp: " . $tmp . $nl; }
               $addused = $tmp;
               }
            }
         
         if ($addused == "USED")
            {
            if ($dbg) { echo "WRITE TO USED STACK" . $nl; }
            if ($dbg) { echo "Add the current address to the used addresses list" . $nl; }
            file_put_contents($usedpath, $nextadd . "\n", FILE_APPEND | LOCK_EX);
      
            $addsleft = str_replace($nextadd . "\n","", $adds);
            //echo "Addresses left: " . $nl . $addsleft . $nl;
            if ($dbg) { echo "Writing remaining fresh addresses back to file" . $nl; }
            file_put_contents($addpath, $addsleft);
            
            }
         
         if ($dbg) { echo $nl; }
         $cnt++;
         } while ($addused <> "FRESH" && $cnt < 50);
      
      if ($addused == "FRESH")
         {
         if ($dbg) { echo "Next fresh address: " . $nextadd . $nl; }
         return $nextadd;
         }
      if ($dbg) { echo "done" . $nl; }
      
      return "SHOW ME WHAT YOU GOT"; // This shouldn't happen      
      }
   else
      {
      return "ERROR: missing addresses file";
      }
   return;
   }







function is_address_fresh_bc($address)
   { 
   global $nl;
   global $dbg;
   $totalbalance_url_uc = "https://blockchain.info/q/getreceivedbyaddress/" . $address . "?confirmations=1";
   $totalbalance_url_cf = "https://blockchain.info/q/getreceivedbyaddress/" . $address . "?confirmations=0";
      

   if ($dbg) { echo "Total balance url (uc): " . $totalbalance_url_uc . $nl; }
   if ($dbg) { echo "Total balance url (cf): " . $totalbalance_url_cf . $nl; }
   
   $totalbalance_uc = file_get_contents($totalbalance_url_uc);
   $totalbalance_cf = file_get_contents($totalbalance_url_cf);
   
   if ($dbg) { echo "totalbalance_uc: " . $totalbalance_uc . $nl; }
   if ($dbg) { echo "totalbalance_cf: " . $totalbalance_cf . $nl; }
   
   $tmp = $totalbalance_uc + $totalbalance_cf;
   if ($tmp)
      {
      return "USED";
      }
   else
      {
      return "FRESH";
      }
   }


function is_address_fresh_bs($address)
   {
   
   }




function get_address_balance_bc($address, $confirmed)
   {
   // confirmed true/false, false for unconfirmed balance
   if ($confirmed)
      {
      $confirmed_url_bc = "https://blockchain.info/q/addressbalance/" . $address . "?confirmations=1";
      $balance_bc = file_get_contents($confirmed_url_bc);
      if ($balance_bc > 0)
         { $balance_bc = $balance_bc / 100000000; }
      return $balance_bc;
      }
   else
      {
      $unconfirmed_url_bc = "https://blockchain.info/q/addressbalance/" . $address . "?confirmations=0";
      $unconfirmed_bc = file_get_contents($unconfirmed_url_bc);
      if ($unconfirmed_bc > 0)
         { $unconfirmed_bc = $unconfirmed_bc / 100000000; }
      return $unconfirmed_bc;
      }
   }


function get_address_balance_bs($address, $confirmed)
   {
   global $nl;
   // confirmed true/false, false for unconfirmed balance
   $address_info_url_bs = "https://blockstream.info/api/address/" . $address;
   //echo "URL: " . $address_info_url_bs . $nl;
   $address_infoj = file_get_contents($address_info_url_bs);
   $address_info = json_decode($address_infoj, true);
         
   //echo "<pre>" . print_r($address_info, true) . "</pre>" . $nl;
   if ($confirmed)
      {
      $confirmed_balance_bs = ($address_info['chain_stats']['funded_txo_sum'] - $address_info['chain_stats']['spent_txo_sum'])/100000000;
      //echo "Confirmed balance: " . number_format($confirmed_balance_bs,8) . $nl;
      return number_format($confirmed_balance_bs,8);
      }
   else
      {
      $unconfirmed_balance_bs = ($address_info['mempool_stats']['funded_txo_sum'] - $address_info['mempool_stats']['spent_txo_sum'])/100000000;
      //echo "Unconfirmed balance: " . number_format($unconfirmed_balance_bs,8) . $nl;
      return number_format($unconfirmed_balance_bs,8);
      }
   }








function generate_addresses($xpub)
   {
   global $nl;
   global $addresses_to_generate;
   
   $folder = get_files_folder();
   echo "xpub: " . $xpub . $nl;
   echo "files folder: " . $folder . $nl;
   
   $math = Bitcoin::getMath();
   $network = Bitcoin::getNetwork();
   $random = new Random();
   
   // By default, this example produces random keys.
   $hdFactory = new HierarchicalKeyFactory();
   $master = $hdFactory->generateMasterKey($random);
   
   // To restore from an existing xprv/xpub:
   //$master = $hdFactory->fromExtended("yourxpuborxprivhere");
   //$xpub = "";
   
   echo "xpub passed in: " . $xpub . $nl;
   echo  "Restoring from xpub ending " . substr($xpub, -4, 4) . $nl . $nl;
   $master = $hdFactory->fromExtended($xpub);
   $childKey = $master->derivePath('0/0');
   $pubKey = $childKey->getPublicKey();
   //echo "Pubkey: <pre>" .
  
    print_r($pubKey,true) . "</pre>" . $nl;
   
   $masterAddr = new PayToPubKeyHashAddress($master->getPublicKey()->getPubKeyHash());
 
   echo "Directly derive path m/0/n stylee:" . $nl;
   // maybe make the address derivation path user configurable? would perhaps improve
   // compatibility with other wallets which might use different derivation paths?
   
   //$sameKey2 = $master->derivePath("0/1");
   //echo " - m/0/1 " . $sameKey2->toExtendedPublicKey() . $nl;
   //$child3 = new PayToPubKeyHashAddress($sameKey2->getPublicKey()->getPubKeyHash());
   //echo "   Address: " . $child3->getAddress() . $nl . $nl;
   //
   //
   //$sameKey2 = $master->derivePath("0/2");
   //echo " - m/0/1 " . $sameKey2->toExtendedPublicKey() . $nl;
   //$child3 = new PayToPubKeyHashAddress($sameKey2->getPublicKey()->getPubKeyHash());
   //echo "   Address: " . $child3->getAddress() . $nl . $nl;
   //
   //
   //$sameKey2 = $master->derivePath("0/3");
   //echo " - m/0/1 " . $sameKey2->toExtendedPublicKey() . $nl;
   //$child3 = new PayToPubKeyHashAddress($sameKey2->getPublicKey()->getPubKeyHash());
   //echo "   Address: " . $child3->getAddress() . $nl . $nl;
   //
   
   $addresses = "";
   ob_flush();
   
   for ($n=0;$n<$addresses_to_generate;$n++)
      {
      $sameKey2 = $master->derivePath("0/".$n);
      //echo " - m/0/" . $n . ": " . $sameKey2->toExtendedPublicKey() . $nl;
      $child3 = new PayToPubKeyHashAddress($sameKey2->getPublicKey()->getPubKeyHash());
      echo "   Address m/0/" . $n . ": " . $child3->getAddress() . $nl;
      $addresses .= $child3->getAddress() . "\n";
      ob_flush();
      }
   
   
   echo "Saving addresses to text file.." . $nl;
   
   file_put_contents( $folder . "/fresh.txt", $addresses );
   echo "DONE!" . $nl;
   
   //echo "HARDENED PATH (disabled bc no privkeys)\n";
   //$hardened2 = $master->derivePath("0/999999'");
   
   //$child4 = new PayToPubKeyHashAddress($hardened2->getPublicKey()->getPubKeyHash());
   //echo " - m/0/999999' " . $hardened2->toExtendedPublicKey() . $nl;
   //echo "   Address: " . $child4->getAddress() . $nl . $nl;
   

   
   return;
   }


















function get_files_folder()
   {
   global $filespath;
   global $nl;
   
   
   //die();
   
   $upload_base = realpath(__DIR__);
   $out = "";
   if ( file_exists($upload_base . "/" . $filespath) )
      {
      $out .= "Folder exists" . $nl;
      // CHECK FOR .HTACCESS
      }
   else
      {
      mkdir ($upload_base . "/" . $filespath);
      $out .= "Folder " . $upload_base . "/" . $filespath . " created" . $nl;
      }

   if ( file_exists($upload_base . "/" . $filespath . "/.htaccess") ) // SWITCHED OFF THIS LOOP FOR NOW - TESTING DOWNLOADABLE PDFS (now that we've got longer random keys)
      {
      // all is fine, htaccess exists
      $out .= "All is fine, .htaccess exists" . $nl;
      }
   else
      {
      $out .= "Need to make .htaccess" . $nl;
      $httmp = "Order Allow,Deny
Deny from All
";
      file_put_contents($upload_base . "/" . $filespath . "/.htaccess", $httmp);
      $out .= ".htaccess created" . $nl;
      }    
   
   $out .= "Return: " . $upload_base . "/" . $filespath . $nl;
   //return $out;
   return $upload_base . "/" . $filespath;    
   }
