<?php
// Paul, (c)2011
// Code 128 Barcode Image Generator

// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.

// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.


// PHP5+, and GDlib required

// Use:
//		http://www.yourdomain.com/code128.php?data={yourdata}&w={preferredwidth}&h={preferredwidth}&text=true
//			Parameters
//				data - The data you want to encode in the barcode
//				w (optional) - The preferred barcode width in "scale units." "1" = 1x the minimum size, "2" = 2x the minimum size, etc. 
//				h (optional) - The preferred barcode height in pixels. The height will default to 20% the width if not specified, or a minimum of ~0.25"
//				text (optional) - True or false, whether or not to display the data as text below the barcode

// Settings:
	// Path to the font to use for the optional barcode text
	// Change this to match the path to your preffered font file if you'll be using the text feature
$fontPath = "./arialbd.ttf";

// Changelog:
//	v1.0.0 - First version

//*************************************************************************************
//* Do not edit anything below this line
//************************************************************************************* 



if(isset($_GET['data'])){												// Get data
	$stringData = $_GET['data'];
}else{
	die("Error: Must supply data to create barcode.");
}


if(isset($_GET['w'])){													// Get width (if any)
	$width = $_GET['w'];
}else{
	$width = 1;																// Choose default if not
}
$xScale = $width;

																			// Calculate actual width in pixels
// 			Quiet Zone		Start Char				Encoded Data				Check Digit		Stop Char		Quiet Zone
$width = ($width * 10) + ($width * 11) + ($width * 11 * strlen($stringData)) + ($width * 11) + ($width * 13) + ($width * 10);


if(isset($_GET['h'])){													// Get height (if any)
	$height = $_GET['h'];
}else{
	$height = $width * 0.20;												// Choose default if not
}

if($height < 25){														// Make sure the height is a minimum of 0.25" (1px ~ 0.010")
	$height = 25;
}


if(isset($_GET['text'])){												// Determine whether or not to display text under barcode
	$showText = $_GET['text'];
	if(strcasecmp("true" , $showText) == 0){
		$showText = true;
	}else if(strcasecmp("false" , $showText) == 0){
		$showText = false;
	}else{
		$showText = false;
	}
}else{
	$showText = false;
}



$code = array(															// Code 128 value table
	0 => "212222",	// " "
	1 => "222122",	// "!"
	2 => "222221",	// "{QUOTE}"
	3 => "121223",	// "#"
	4 => "121322",	// "$"
	5 => "131222",	// "%"
	6 => "122213",	// "&"
	7 => "122312",	// "'"
	8 => "132212",	// "("
	9 => "221213",	// ")"
	10 => "221312",	// "*"
	11 => "231212",	// "+"
	12 => "112232",	// ","
	13 => "122132",	// "-"
	14 => "122231",	// "."
	15 => "113222",	// "/"
	16 => "123122",	// "0"
	17 => "123221",	// "1"
	18 => "223211",	// "2"
	19 => "221132",	// "3"
	20 => "221231",	// "4"
	21 => "213212",	// "5"
	22 => "223112",	// "6"
	23 => "312131",	// "7"
	24 => "311222",	// "8"
	25 => "321122",	// "9"
	26 => "321221",	// ":"
	27 => "312212",	// ";"
	28 => "322112",	// "<"
	29 => "322211",	// "="
	30 => "212123",	// ">"
	31 => "212321",	// "?"
	32 => "232121",	// "@"
	33 => "111323",	// "A"
	34 => "131123",	// "B"
	35 => "131321",	// "C"
	36 => "112313",	// "D"
	37 => "132113",	// "E"
	38 => "132311",	// "F"
	39 => "211313",	// "G"
	40 => "231113",	// "H"
	41 => "231311",	// "I"
	42 => "112133",	// "J"
	43 => "112331",	// "K"
	44 => "132131",	// "L"
	45 => "113123",	// "M"
	46 => "113321",	// "N"
	47 => "133121",	// "O"
	48 => "313121",	// "P"
	49 => "211331",	// "Q"
	50 => "231131",	// "R"
	51 => "213113",	// "S"
	52 => "213311",	// "T"
	53 => "213131",	// "U"
	54 => "311123",	// "V"
	55 => "311321",	// "W"
	56 => "331121",	// "X"
	57 => "312113",	// "Y"
	58 => "312311",	// "Z"
	59 => "332111",	// "["
	60 => "314111",	// "\"
	61 => "221411",	// "]"
	62 => "431111",	// "^"
	63 => "111224",	// "_"
	64 => "111422",	// "`"
	65 => "121124",	// "a"
	66 => "121421",	// "b"
	67 => "141122",	// "c"
	68 => "141221",	// "d"
	69 => "112214",	// "e"
	70 => "112412",	// "f"
	71 => "122114",	// "g"
	72 => "122411",	// "h"
	73 => "142112",	// "i"
	74 => "142211",	// "j"
	75 => "241211",	// "k"
	76 => "221114",	// "l"
	77 => "413111",	// "m"
	78 => "241112",	// "n"
	79 => "134111",	// "o"
	80 => "111242",	// "p"
	81 => "121142",	// "q"
	82 => "121241",	// "r"
	83 => "114212",	// "s"
	84 => "124112",	// "t"
	85 => "124211",	// "u"
	86 => "411212",	// "v"
	87 => "421112",	// "w"
	88 => "421211",	// "x"
	89 => "212141",	// "y"
	90 => "214121",	// "z"
	91 => "412121",	// "{"
	92 => "111143",	// "|"
	93 => "111341",	// "}"
	94 => "131141"); // "~"


	$stringArr = str_split($stringData);								// Split the data into an array of characters
	$barcodeData = "";													// Create a new string for bar and space widths
	$checksum = 104;													// Start keeping track of the checksum values

	$barcodeData .= "211214";											// Add the start code to the barcode

	foreach($stringArr as $i => $c){									// Parse each char and update the checksum
		$barcodeData .= $code[ord($c) - 32];
		$checksum += ($i + 1) * (ord($c) - 32);
	}

	$barcodeData .= $code[$checksum % 103];								// Calculate the checksum and add it to the barcode
	$barcodeData .= "2331112";											// Add stop code to the barcode

	$barcodeData = str_split($barcodeData);								// Split barcode into array of alternating bar and space widths: 0 = bar, 1 = space, 2= bar, etc.


	$stringWidth = 0;													// Determine margins if text will be displayed
	$stringHeight = 0;
	$lineHeight = 0;
	if($showText && file_exists($fontPath)){
		$stringLoc = imagettfbbox(10 * $xScale, 0, $fontPath, $stringData);
		$stringWidth = abs($stringLoc[4] - $stringLoc[0]);				// Find total text width and height
		$stringHeight = abs($stringLoc[5] - $stringLoc[1]);
		
		$stringLoc = imagettfbbox(10 * $xScale, 0, $fontPath, "A");		// Find basepoint for font
		$lineHeight = abs($stringLoc[5] - $stringLoc[1]);
	}

	$img = imagecreate($width, $height + 20 + $stringHeight)			// Create new image (with 10px padding on top and bottom) 
		or die("Error: Cannot create barcode image.");

	$black = imagecolorallocate($img, 0, 0, 0);							// Allocate colors for image
	$white = imagecolorallocate($img, 255, 255, 255);

	imagefill($img, 0, 0, $white);										// Make background white (for now)

	$x = 10 * $xScale;													// Keep track of the X position
	$bar = true;														// Keep track of whether it's a bar or space

	foreach($barcodeData as $barWidth){									// Iterate through bars
		$barWidth = (int)$barWidth * $xScale;
		if($bar){														// If it's a bar...
			while($barWidth > 0){											// Draw a line for each width point
				imageline($img, $x, 10, $x, $height, $black);
				$x++;														// Push X pointer forward
				$barWidth--;
			}
			$bar = !$bar;
		}else{															// If it's a space... just push X pointer forward
			$x += $barWidth;
			$bar = !$bar;
		}
	}

	imagecolortransparent($img, $white);								// Make white transparent

	if($showText && file_exists($fontPath)){							// Generate text if necessary (with appropriate gap between barcode and text)
		imagefttext($img, (10 * $xScale), 0, (($width - $stringWidth)/2), ($height + ((0.5 * $xScale) + 5) + $lineHeight), $black, $fontPath, $stringData);
	}

	header("Content-type: image/png");									// Let the browser know we're outputting a PNG image (change this line and the next for GIF, JPG, etc.)
	imagepng($img);														// Output the image
	imagedestroy($img);

	?>
