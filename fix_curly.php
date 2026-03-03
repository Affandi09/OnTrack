<?php
$files = [
    "/var/www/html/vendor/classes/extras/htmlfilter.php",
    "/var/www/html/vendor/ezyang/htmlpurifier/library/HTMLPurifier/ChildDef/Custom.php",
    "/var/www/html/vendor/ezyang/htmlpurifier/library/HTMLPurifier/Encoder.php",
    "/var/www/html/vendor/ezyang/htmlpurifier/library/HTMLPurifier/TagTransform/Font.php",
    "/var/www/html/vendor/mpdf/mpdf/src/Barcode/Code128.php",
    "/var/www/html/vendor/mpdf/mpdf/src/Barcode/Code93.php",
    "/var/www/html/vendor/mpdf/mpdf/src/Barcode/EanExt.php",
    "/var/www/html/vendor/mpdf/mpdf/src/Barcode/EanUpc.php",
    "/var/www/html/vendor/mpdf/mpdf/src/Barcode/Imb.php",
    "/var/www/html/vendor/mpdf/mpdf/src/Color/ColorConverter.php",
    "/var/www/html/vendor/mpdf/mpdf/src/Gif/ImageHeader.php",
    "/var/www/html/vendor/mpdf/mpdf/src/Gradient.php",
    "/var/www/html/vendor/mpdf/mpdf/src/Image/ImageProcessor.php",
    "/var/www/html/vendor/mpdf/mpdf/src/Image/Svg.php",
    "/var/www/html/vendor/mpdf/mpdf/src/Mpdf.php",
    "/var/www/html/vendor/mpdf/mpdf/src/Otl.php",
    "/var/www/html/vendor/mpdf/mpdf/src/Pdf/Protection.php"
];

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "Not found: $file\n";
        continue;
    }
    $code = file_get_contents($file);

    // Replace $var{something} with $var[something]
    // Regex looks for $ followed by variable name, then { ... }
    $pattern = "/(\\$[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*(?:->[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*)?)\\{([^}]*)\\}/";
    $new_code = preg_replace($pattern, "$1[$2]", $code);

    if ($new_code !== $code) {
        file_put_contents($file, $new_code);
        echo "Fixed array curly brace syntax in $file\n";
    }
}
echo "Done.\n";
