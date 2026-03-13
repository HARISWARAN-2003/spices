<?php
if (file_exists('fpdf.php')) {
    echo "Success! fpdf.php is found in this folder.";
} else {
    echo "Error: fpdf.php is STILL MISSING from " . getcwd();
}
?>