package Whathood::Utils;

use strict;
use warnings;

require Exporter;

my @EXPORT_OK = qw(exec_sql_stmt);

sub prompt_user {
    my $class = shift;
    my $msg = shift;
    print "$msg. Press CTRL-C to cancel, any key to continue";
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

sub exec_sql_stmt {
    my $db_name = shift;
    my $sql_stmt = shift;
    `sudo su - postgres -c 'psql -c "$sql_stmt" $db_name'`;
}
1;
