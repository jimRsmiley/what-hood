package Whathood::Utils;

use strict;
use warnings;

sub prompt_user {
    my $class = shift;
    my $msg = shift;
    print "$msg. Press CTRL-C to cancel, any key to continue\n";
    <STDIN>;
}

sub check_run_as_root {
    my $user=`whoami`;
    chomp $user;
    unless ( $user eq "root" ) {
       print "must be run as root. Not '$user'\n";
       exit;
    }
}
1;
