<?php
error_reporting(0);

// argument 1: mapping between CMU's arpabet subset and desired output
// argument 2: input dictionary file
// argument 3: optional, include inflection marker column, defaults to false
// argument 4: optional, limit number of rows

// example: php main.php ipa.tsv cmudict.tsv > output.tsv

ini_set("memory_limit", "256M");

include "str_pad_unicode.php";

if (!$argv[1] or !$argv[2]) exit();

$mapping = file($argv[1]);

$dict = file($argv[2]);

$inflections = ($argv[3] || false);

$limit = $argv[4];

$map[1] = "ˈ";
$map[2] = "ˌ";

foreach($mapping as &$line):
	$line = explode("\t", $line);
	$line = array_map("trim", $line);
	$map[$line[0]] = $line[1];
endforeach;
$dict = array_filter($dict, function($a) {
	return  !($a[0] === ";" && $a[1] === ";" && $a[2] === ";");
});
if ($limit) {
	$dict = array_slice($dict, 0, $limit);
}

foreach($dict as &$entry):
	$entry = str_replace("  ", " ", $entry);
	$entry = trim($entry);
	$entry = explode("\t", $entry, 2);
	$entry[1] = preg_replace("/(\w\w)(\d)/", "$1 $2", $entry[1]);
	$entry = implode(" ", $entry);
	$entry = explode(" ", $entry);
	$out = [];
	$out[0] = array_shift($entry);
	foreach($entry as $glyph):
		if (!$map[$glyph] || $map[$glyph] === 0) {continue;}
		$map[$glyph] = ($map[$glyph] ? $map[$glyph] : $glyph);
		$out[1] .= $map[$glyph];
		if (strcmp($map[$glyph], "ˈ") === 0 || strcmp($map[$glyph], "ˌ") === 0) {continue;}
		$out[2] .= $map[$glyph];
	endforeach;
	echo str_pad_unicode($out[0], 25, " ");
	if ($inflections) {
		echo str_pad_unicode($out[1], 25, " ");
	}
	echo str_pad_unicode($out[2], 25, " ");
	echo "\n";
endforeach;

ini_set("memory_limit", "128M");

