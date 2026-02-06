<?php
/*
PHP Captcha by Codepeople.net
http://www.codepeople.net
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!ini_get("zlib.output_compression")) ob_clean();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");    

if (!isset($_GET["ps"])) $_GET["ps"] = '';
if (!isset($_GET["bcolor"]) || $_GET["bcolor"] == '') $_GET["bcolor"] = "FFFFFF";
if (!isset($_GET["border"]) || $_GET["border"] == '') $_GET["border"] = "000000";

//configuration
$imgX = min( ( isset($_GET["width"]) && is_numeric( $_GET["width"] ) )? intval($_GET["width"]) : "180" , 800); 
$imgY = min( ( isset($_GET["height"]) && is_numeric( $_GET["height"] ) )? intval($_GET["height"]) : "60" , 600);

$letter_count = min( ( isset($_GET["letter_count"]) && is_numeric( $_GET["letter_count"] ) )? intval($_GET["letter_count"]) : "5", 20);
$min_size = min( ( isset($_GET["min_size"]) && is_numeric( $_GET["min_size"] ) )? intval($_GET["min_size"]) : "35", 200); 
$max_size = min( ( isset($_GET["max_size"]) && is_numeric( $_GET["max_size"] ) )? intval($_GET["max_size"]) : "45", 200); 
$noise = min( ( isset($_GET["noise"]) && is_numeric( $_GET["noise"] ) )? intval($_GET["noise"]) : "200", 5000); 
$noiselength = min( ( isset($_GET["noiselength"]) && is_numeric( $_GET["noiselength"] ) )? intval($_GET["noiselength"]) : "5", 50); 
$bcolor = cpcff_decodeColor($_GET["bcolor"]);  
$border = cpcff_decodeColor($_GET["border"]);  

$noisecolor = 0xcdcdcd;         
$random_noise_color= true;      
$tcolor = cpcff_decodeColor("666666"); 
$random_text_color= true;                                
                                               
function cpcff_decodeColor($hexcolor)
{
   $color = hexdec($hexcolor);
   $c["b"] = $color % 256;
   $color = $color / 256;
   $c["g"] = $color % 256;
   $color = $color / 256;
   $c["r"] = $color % 256;
   return $c;
}

function cpcff_similarColors($c1, $c2)
{
   return sqrt( pow($c1["r"]-$c2["r"],2) + pow($c1["g"]-$c2["g"],2) + pow($c1["b"]-$c2["b"],2)) < 125;
}

if (function_exists('session_start')) @session_start();

function cpcff_make_seed() {
    list($usec, $sec) = explode(' ', microtime());
    return (float) $sec + ((float) $usec * 100000);
}
mt_srand(cpcff_make_seed());
$randval = mt_rand();

$str = "";
$length = 0;
for ($i = 0; $i < $letter_count; $i++) {
	 $str .= chr(mt_rand(97, 122))." ";
}
$_SESSION['rand_code'.sanitize_key($_GET["ps"])] = str_replace(" ", "", $str);

$uidt = uniqid();
set_transient( "cpeople-captcha-".$uidt , str_replace(" ", "", $str) , 1800 );
setCookie('rand_code'.sanitize_key($_GET["ps"]), $uidt, time()+36000,"/");

if (!function_exists('imagecreatetruecolor'))
{
    header("Content-type: image/png");
    readfile( dirname( __FILE__ ) . "/no-gd-library.png");
    exit;
}

$image = imagecreatetruecolor($imgX, $imgY);
$backgr_col = imagecolorallocate($image, $bcolor["r"],$bcolor["g"],$bcolor["b"]);
$border_col = imagecolorallocate($image, $border["r"],$border["g"],$border["b"]);

if ($random_text_color)
{
  do 
  {
     $selcolor = mt_rand(0,256*256*256);
  } while ( cpcff_similarColors(cpcff_decodeColor($selcolor), $bcolor) );
  $tcolor = cpcff_decodeColor($selcolor);
}    

$text_col = imagecolorallocate($image, $tcolor["r"],$tcolor["g"],$tcolor["b"]);   

switch (@$_GET["font"]) {
    case "font-2.ttf":
    case "font2":
        $selected_font = "font-2.ttf";
        break;
    case "font-3.ttf":
    case "font3":
        $selected_font = "font-3.ttf";
        break;
    case "font-4.ttf":
    case "font4":
        $selected_font = "font-4.ttf";
        break;               
    default:
        $selected_font = "font-1.ttf";    
}

$font = dirname( __FILE__ ) . "/". $selected_font;

// 1. Create Image and Colors
$image = imagecreatetruecolor($imgX, $imgY);
imagealphablending($image, true);
imagesavealpha($image, true);

$bgColor = imagecolorallocate($image, $bcolor['r'], $bcolor['g'], $bcolor['b']);
$borderColor = imagecolorallocate($image, $border['r'], $border['g'], $border['b']);
imagefill($image, 0, 0, $bgColor);

// 1. Setup Margins
$leftPadding = 15;  // Explicit safe zone for the first character
$rightPadding = 15; // Safe zone for the last character
$availableWidth = $imgX - ($leftPadding + $rightPadding);

$charArray = str_split($str);
$totalChars = count($charArray);
$cellWidth = $availableWidth / $totalChars;

// 2. Decorative Background Noise
for ($i = 0; $i < 6; $i++) {
    $shapeAlpha = imagecolorallocatealpha($image, rand(220, 245), rand(220, 245), rand(220, 245), 90);
    imagefilledellipse($image, rand(0, $imgX), rand(0, $imgY), rand(20, $imgX), rand(20, $imgY), $shapeAlpha);
}

// 3. Render Characters with Safe Positioning
foreach ($charArray as $i => $char) {
    $fontSize = rand($min_size, $max_size);
    $angle = rand(-10, 10);
    
    $bbox = imagettfbbox($fontSize, $angle, $font, $char);
    
    // Calculate dimensions from bounding box
    $charWidth = abs($bbox[4] - $bbox[0]);
    $charHeight = abs($bbox[5] - $bbox[1]);

    // X = Base padding + (index * cell width) + centering within that cell
    $x = $leftPadding + ($i * $cellWidth) + ($cellWidth - $charWidth) / 2;
    
    // Y = Centered vertically with a slight random jitter for modern feel
    $y = ($imgY / 2) + ($charHeight / 2) - rand(-2, 2);

    $textColor = imagecolorallocatealpha($image, rand(40, 70), rand(40, 70), rand(80, 110), 0);
    
    imagettftext($image, $fontSize, $angle, $x, $y, $textColor, $font, $char);
}

// 4. Fine Grain Noise
for ($i = 0; $i < $noise; $i++) {
    $noiseColor = imagecolorallocatealpha($image, 80, 80, 80, rand(90, 110));
    $x1 = rand(0, $imgX);
    $y1 = rand(0, $imgY);
    // Using noiselength to define the sprawl of the noise dots/lines
    imageline($image, $x1, $y1, $x1 + rand(1, $noiselength), $y1 + rand(1, $noiselength), $noiseColor);
}

// 5. Border and Output
imagerectangle($image, 0, 0, $imgX - 1, $imgY - 1, $borderColor);

header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
exit;
?>