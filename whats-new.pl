#!/usr/bin/perl
use strict;
use warnings;
use Mojo::UserAgent;
use Data::Dumper;

###############################################################################
# SETUP
###############################################################################
# URL, publications are expected as <a class=Publication href=url>text</a>
my $url = 'http://www.bamf.de/DE/Infothek/Statistiken/Asylzahlen/asylzahlen-node.html';

# where to save previous state, should be readable and writable by script
my $compare_to = 'asylzahlen-node.dump';   

# if enabled print some debugging to STDERR
my $DEBUG = 0;

###############################################################################
# actual code
###############################################################################

# access remote URL
my $ua = Mojo::UserAgent->new;
my $tx = $ua->get($url);
my $res = $tx && $tx->res;
$res && $res->code == 200 or die "failed to get $url";

# extract all publications: <a class=Publication href=url>text</a>
#  -> ( [ url1,text1], [url2,text2], ...)
my @pub;
$res->dom('a.Publication')->each(sub {
    push @pub, [ 
	''.Mojo::URL->new($_->{href})->to_abs($tx->req->url),
	$_->text 
    ];
});
warn "got publications ".Dumper(\@pub) if $DEBUG;

# compare to previous state and output new findings as UTF8
if (my $old = do $compare_to) {
    my %have = map { $_->[0] => 1 } @$old;
    my @new = grep { !$have{$_->[0]} } @pub;
    if (!@new) {
	warn "nothing new\n" if $DEBUG;
    } else {
	binmode(STDOUT,':utf8');
	print "Es sind neue Publikationen verf\x{fc}gbar:\n".
	    join("", map { "$_->[1] | $_->[0]\n" } @new);
	_save(\@pub);
    }
} else {
    warn "no previous state\n" if $DEBUG;
    _save(\@pub);
}


sub _load { do $compare_to }
sub _save {
    my $data = shift;
    open(my $fd,'>',$compare_to) or die "cannot write to $compare_to: $!";
    print $fd Dumper($data);
}
