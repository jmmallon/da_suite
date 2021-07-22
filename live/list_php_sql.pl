#!/usr/bin/perl

use MP3::Tag;
use MP3::Info;

$| = 1;
$sep = "::";

$dirsfile = ($ARGV[0]) ? $ARGV[0] : "list.cfg";
$sqlfile = "songlist.sql";
$tablename = "songlist";
$searcher_page = "query.html";

open(FI,"$dirsfile") || die("No config file $dirsfile - $!");
chomp(@dirs = <FI>);
close(FI);

open(FO,">$sqlfile") || die("$sqlfile - $!");
print FO << "ENDSQL";
DROP TABLE $tablename;
CREATE TABLE $tablename (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    artist VARCHAR(50) NOT NULL,
    song VARCHAR(100) NOT NULL,
    album VARCHAR(100) NOT NULL,
    filename VARCHAR(200) NOT NULL,
    length INT(4) ZEROFILL,
    bitrate INT(3) ZEROFILL);
ENDSQL

foreach $start (sort(@dirs)) {
	unless (-d $start and -r _) {
		print STDERR "Can't open directory $start in $dirsfile!\n";
		next;
	}
	($ptr1, $ptr2, $a, $b, $maxlen) = ();
	($ptr1, $ptr2, $a, $b, $maxlen) = &dirprobe($start);
	push(@select1,@$ptr1);
	push(@select2,@$ptr2);
}

close(FO);
$select1 = join("\n",sort(@select1));
$select2 = join("\n",sort(@select2));
for ($i=0; $i <= int($maxlen/60) + 1; $i++) {
	$select3 .= "<option value=$i> $i\n";
}

for ($i=0; $i <= 55; $i+=5) {
	$select4 .= "<option value=$i> " . sprintf("%02d\n",$i);
}

open(FO,">$searcher_page") || die("$searcher_page - $!");
print FO << "ENDHTML";
<html><head>
<title>MP3 search</title>
</head>
<body bgcolor="#FFFFFF">
<form name=query1 method=post action=action.php target="search_results">
<input type=hidden name=action value="artist">
By <select name=artist>
$select1
</select> &nbsp;<input type=submit value="Search">
</form><p>
<form name=query2 method=post action=action.php target="search_results">
<input type=hidden name=action value="album">
From <select name=album>
$select2
</select> &nbsp;<input type=submit value="Search">
</form><p>
<form name=query3a method=post action=action.php target="search_results">
<input type=hidden name=action value="artlike">
Artist name 
<select name=type>
<option value="bg"> begins with
<option value="ct"> contains
<option value="ed"> ends with
</select> &nbsp;
<input type=text name="artist"> &nbsp;<input type=submit value="Search">
</form>
<form name=query3 method=post action=action.php target="search_results">
<input type=hidden name=action value="song">
Song title 
<select name=type>
<option value="bg"> begins with
<option value="ct"> contains
<option value="ed"> ends with
</select> &nbsp;
<input type=text name="song"> &nbsp;<input type=submit value="Search">
</form>
<form name=query4 method=post action=action.php target="search_results">
<input type=hidden name=action value="length">
Length &nbsp;
<select name=type>
<option value="lt"> shorter than
<option value="xt"> exactly
<option value="gt"> longer than
</select> &nbsp;
<select name=minutes>
$select3
</select> min. 
<select name=seconds>
$select4
</select> sec. &nbsp;<input type=submit value="Search">
</form>
<form name=query5 method=post action=action.php target="search_results">
<input type=hidden name=action value="between">
Between &nbsp;
<select name=minutes1>
$select3
</select> min. 
<select name=seconds1>
$select4
</select> sec. &nbsp;and &nbsp;
<select name=minutes2>
$select3
</select> min. 
<select name=seconds2>
$select4
</select> sec. &nbsp;
<input type=submit value="Search">
</form>
</body></html>
ENDHTML
close(FO);


sub dirprobe {
	my ($dir, $ptr1, $ptr2, $ptr3, $ptr4, $maxlen) = @_;
	my ($string) = "";
	my (@list) = ();
	my (@check1, @check2, @select1, @select2) = ();
	@select1 = @$ptr1 if (defined($ptr1));
	@select2 = @$ptr2 if (defined($ptr2));
	@check1 = @$ptr3 if (defined($ptr3));
	@check2 = @$ptr4 if (defined($ptr4));
	my ($select1, $select2);
	print "Doing $dir\n";
	opendir(DI, $dir) || die ("$dir - $!");
	@list = sort(readdir(DI));
	closedir(DI);
	foreach $file (@list) {
		$filename = "$dir/$file";
		next if ($file eq "." or $file eq ".." or -l $filename);
		if (-d $filename) {
			($ptra1, $ptra2, $ptra3, $ptra4, $maxlen) = &dirprobe($filename,\@select1,\@select2,\@check1,\@check2,$maxlen);
			@select1 = @$ptra1;
			@select2 = @$ptra2;
			@check1 = @$ptra3;
			@check2 = @$ptra4;
		} elsif ($file =~ /\.mp3$/) {
			$album = $artist = $song = $length = $gotit = "";
			@stuff = ();
			$info = get_mp3info($filename);
	        	$length = int($info->{SECS});
	        	$bitrate = int($info->{BITRATE});
	        	$bitrate =~ s/^0*//;
			undef $info;
			$mp3 = MP3::Tag->new($filename);
			@stuff = $mp3->autoinfo();
			if (@stuff) {
				map { s/^ *//; s/ *$//; s/'/`/g; s/\(/</g; s/\)/>/g; } @stuff;
				$filename =~ s/^ *//;
				$filename =~ s/ *$//;
				$filename =~ s/'/`/g;
				$filename =~ s/\(/</g;
				$filename =~ s/\)/>/g;
                		$filename =~ s#G:.FTP.Delicious Agony.Prog2k Artists#E:/DA Music/Prog2k Artists#;
                		$filename =~ s#G:.FTP.Delicious Agony.Elliot's Music#I:/Music#;
				$filename =~ s#[\\/]#%%#g;

				($song, $artist, $album) = @stuff[0,2,3];
				$album = "None" unless ($album);
				$song = "None" unless ($song);
				$artist = "None" unless ($artist);
				$artist = "None" unless ($artist);
				$length = 0 unless ($length);
				$bitrate = 0 unless ($bitrate);
				print FO qq#insert into $tablename (artist, song, album, filename, length, bitrate) values ('$artist','$song','$album','$filename',$length,$bitrate);\n#;

				$value = "<option value='$artist'> $artist";
				$grepvalue = $artist;
				$grepvalue =~ s/\W//g;
		   		unless (grep(/^$grepvalue$/i,@check1)) {
					push(@check1, $grepvalue);
					($tag,$label) = split(/'> /,$value);
					$label =~ s/`/'/g;
					$label =~ s/</(/g;
					$label =~ s/>/)/g;
					push(@select1, $tag . "'> " . $label);
				}
				$value = "<option value='$artist" . "###" . $album . "'> " . $artist . " - " . $album;
				$grepvalue = "$artist$album";
				$grepvalue =~ s/\W//g;
				unless (grep(/^$grepvalue$/i,@check2)) {
					push(@check2, "$grepvalue");
					($tag,$label) = split(/'> /,$value);
					$label =~ s/`/'/g;
					$label =~ s/</(/g;
					$label =~ s/>/)/g;
					push(@select2, $tag . "'> " . $label);
				}
 				$maxlen = $length if ($maxlen < $length);
			}
		}
	}
	return(\@select1,\@select2,\@check1,\@check2,$maxlen)
}
