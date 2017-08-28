#!/usr/local/bin/perl -w 
# Before `make install' is performed this script should be runnable with
# `make test'. After `make install' it should work as `perl access.t'  
##################### We start with some black magic to print on failure.    
use strict;
use warnings;

use Test::More tests => 13;

use UMLS::Interface;
use File::Spec;
use File::Path;

if(!(-d "t")) {
    
    print STDERR "Error - program must be run from UMLS::Similarity\n";
    print STDERR "directory as : perl t/ic.t \n";
    exit;  
}

#  initialize option hash
my %option_hash = ();

#  set the option hash
$option_hash{"t"} = 1;

#  connect to the UMLS-Interface
my $umls = UMLS::Interface->new(\%option_hash);
ok($umls);

#  get the version of umls that is being used
my $version = $umls->version();

#  set the key directory (create it if it doesn't exist)
my $keydir = File::Spec->catfile('t','key', $version);
if(! (-e $keydir) ) {
    File::Path->make_path($keydir);
}

#  get the tests
my $testdir = File::Spec->catdir('t','tests', 'measures');
opendir(DIR, $testdir) || die "Could not open directory $testdir\n";
my @testfiles = grep { $_ ne '.' and $_ ne '..' and $_ ne "CVS"} readdir DIR;

my $perl     = $^X;
my $util_prg = File::Spec->catfile('utils', 'umls-similarity.pl');

my @measures = qw/res lin jcn/;

foreach my $measure (@measures) {

    foreach my $file (@testfiles) {
	
	my $configfile = "config.$file";		      
	my $keyfile    = "$measure.$file";
	my $pfile      = "$file.icpropagation";

	my $infile  = File::Spec->catfile('t','tests', 'measures', $file);
	my $config  = File::Spec->catfile('t','config', $configfile);
	my $key     = File::Spec->catfile('t', 'key', $version, $keyfile);
	
	my $icpropagation = File::Spec->catfile('t','options', $pfile);
	
	my $output = `$perl $util_prg --config $config --icpropagation $icpropagation --measure $measure --infile $infile --realtime 2>&1`;
	
	if(-e $key) {
	    ok (open KEY, $key) or diag "Could not open $key: $!";
	    my $key = "";
	    while(<KEY>) { $key .= $_; } close KEY;
	    cmp_ok($output, 'eq', $key);
	}
	else {
	    ok(open KEY, ">$key") || diag "Could not open $key: $!";
	    print KEY $output;
	    close KEY; 
	  SKIP: {
	      skip ("Generating key, no need to run test", 1);
	    }
	}
    }
}
    
