#!/bin/sh
#
#  Installation script for divelog CMS program.
#           Ian Martin, Apr 26, 2005
#
#  This script will create the appropriate databases and tables
#  in postgreSQL. Next, it installs any functions and procedures
#  and finally, it populates any constant tables with values.
# 
#  Ideally, this will be run from the web to make it easy on the
#  person doing the install, but it can also be run on its own.
#
#  This script assumes that there is alread a user set up for this
#  purpose and that it has the CREATE priviledge in the database.
##################################################################
##################################################################

which=/usr/bin/which
#
# Set these variables if `which` does not exist on your machine
psql=/usr/bin/psql
dbcreate=/usr/bin/dbcreate

#
if [ -e $which ]
then
   psql=`$which psql`
   dbcreate=`$which dbcreate`
 
   dbname=$1
   dbuser=$2
  
   tempfile='tempDDL'
else
   echo "Cannot find program \"which\". Exiting"
   exit 1
fi

createdb -U postgres $dbname
psql -U postgres -d template1 <<EOF
  GRANT INSERT, UPDATE, SELECT, DELETE ON SCHEMA $dbname TO $dbuser;
EOF

ddl_files=(users.ddl dives.ddl dive_data.ddl rdp1.ddl rdp2.ddl rdp3.ddl)
touch $tempfile

for $i in ${ddl_files[@]}
do
   cat ddl/$i >> $tempfile
done
  
psql -U $dbuser -d $dbname -f $tempfile
psql -U $dbuser -d $dbname <<EOF
\copy rdp1.tab to rdp1
\copy rdp2.tab to rdp2
\copy rdp3.tab to rdp3
EOF

exit 0;
