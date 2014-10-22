#!/usr/bin/php
<?php

$opt=getopt("n:ht:");
if (isset($opt['n']))
	$num = intval($opt['n']);
else
	$num = 0;

if ($num == 0 )
$num = 10;

$cmd=sprintf("git log --pretty='format:&&&&%%cd=%%s %%b&&&&(%%cn)[%%h]@@@'  --no-merges -n %d",$num);

exec($cmd,$output,$ret);

if ($ret != 0 ) {
	echo implode("\n",$output);
	exit($ret);
}
$out = implode("",$output);
$output = array();
$output = explode("@@@",$out);

$i=1;
foreach($output as $line) {
	$newline = preg_replace_callback('#(&&&&)(.*)(&&&&)#', 'rep_space', $line);
	if ($i++ < $num)
		$newline .= "\n";
	if (isset($opt['t'])) {
		if (preg_match("/=#(\d+)/",$newline, $mat)) {
			if ($opt['t'] != $mat[1])
				continue;
		}
	}
	if (isset($opt['h']))  {
		if (preg_match("/\[(\S+)\]/",$newline,$mat))
			$newline = $mat[1] . "\n";

	}
echo  $newline;

}

function rep_space($mat) {
	list($a,$b) = explode("=",$mat[2]);
	return $a . "=" . trim($b);
	//return "[".trim($mat[2])."]";
}
