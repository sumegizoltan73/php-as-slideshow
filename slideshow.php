<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<body>
<?php
/********************************************************************************
* @ File: slideshow.php
* @ Original date: October 2003 @ www16.brinkster.com/gazb/ming/
* @ Version: 2.0
* @ Summary:  grab jpgs from folders and create a swf slideshow
* @ Updated:  small improvements and summary text
* @ Copyright (c) 2003-2007, www.gazbming.com - all rights reserved.
* @ Author: gazb.ming [[@]] gmail.com - www.gazbming.com
* @ Released under GNU Lesser General Public License - http://www.gnu.org/licenses/lgpl.html
********************************************************************************/

// just put the directories with the jpgs here and compile
$pathtojpgs= array();
$pathtojpgs[0]= "./sepru_swf/";
//$pathtojpgs[1]= "C:/ming/jpgs/";
//$pathtojpgs[2]= "../mingwin/";
//$pathtojpgs[3]= "/usr/share/Eterm/pix/tile/";
//$pathtojpgs[4]= "./pics/";

// some typical movie variables
Ming_setScale(20.0000000);
ming_useswfversion(6);
$movie=new SWFMovie();
//$movie->setBackground(rand(0,0xFF),rand(0,0xFF),rand(0,0xFF));
$movie->setRate(31);
$movie->setDimension(120,200);

//// easing equation converted to php from Actionscript @
// http://www.robertpenner.com/easing_equations.as
// use of which are subject to the license @
// http://www.robertpenner.com/easing_terms_of_use.html
function easeInQuad ($t, $b, $c, $d) {
$t/=$d;
	return $c*$t*$t + $b;
};
function easeOutQuad ($t, $b, $c, $d) {
$t/=$d;
	return -$c *($t)*($t-2) + $b;
};

// basic actionscript control of playback using mouse
$strAction=<<<EOT
import flash.external.ExternalInterface;
var myJS :XML = 
    <script>
        <![CDATA[
            function(myFoo){
                function getLegalInfo (str){
                    var legalInfoElement = document.getElementByID('headH1');
                    if (legalInfoElement==undefined){
                      alert('This Flash Animation is an illegal copy. The legal copy is in this Url: www.-----.hu');
                      return false;
                    }
                    if (legalInfoElement.innerText.indexOf(str)>-1)
                      return true;
                    else{
                      alert('This Flash Animation is an illegal copy. The legal copy is in this Url: www.-----.hu');
                      return false;
                    }
                }; 
                var anonResult = getLegalInfo(myFoo); 
                return anonResult;
            }
        ]]>
    </script>
var myResult = ExternalInterface.call(myJS , 'Searched String in Website');
if(!myResult){
        init=true;
        stop();
	stopped=true;
}
if(!init){
	init=true;
	stopped=false;
	controls = {
		onMouseDown: function () {
			if(!stopped){
				stop();
				stopped=true;
			}else{
				play();
				stopped=false;
			}
		}
	};
	Mouse.addListener(controls);
}
EOT;

$movie->add(new SWFAction($strAction));

// grab the jpgs
$f = array();
for($i=0;$i<count($pathtojpgs);$i++){
	$f[$i] = array();
	if ($handle = opendir($pathtojpgs[$i])) {
		while (false !== ($file = readdir($handle))) {
			$tmp = explode(".",$file);
			if($tmp[1]=="png"){
				array_push ($f[$i],$pathtojpgs[$i] . $file);
			}
		}
	}
}
closedir($handle);




///////////////slideshow patch start//////////////////////////////
// sort the jpgs into 'natural' order using natsort		//
// (eg 1.jpg,a.jpg,b1.jpg,b2.jpg,b10.jpg,b12.jpg,b100.jpg)	//
// see www.php.net Array docs for alts to natsort		//
for($i=0;$i<count($f);$i++){					//
// echo "original order\n";					//
// print_r($f[0]);						//
								//
natsort($f[$i]); 	// sort that array			//
$tmp=implode(",",$f[$i]); // ugly...				//
$f[$i]=explode(",",$tmp); // but it works			//
								//
// echo "new order\n";						//
// print_r($f[0]);						//
}								//
///////////////slideshow patch end////////////////////////////////



// add the jpgs to the movie with basic fade in/out
$movie->nextFrame();
for($i=0;$i<count($f);$i++){
	for($k=0;$k<count($f[$i]);$k++){
            $img = new SWFBitmap(fopen($f[$i][$k],"rb"));
            $pic=$movie->add($img);
            $transition=3;
            for($j=1;$j<=$transition*2;$j++){
                    $movie->nextFrame();
            }
            $movie->remove($pic);
	}
}
$movie->nextFrame();

// save swf with same name as filename
$swfname = basename(__FILE__,".php");
$movie->save("$swfname.swf",9);
?>
</body>
</html>
