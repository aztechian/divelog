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
#  purpose and that it has the CREATE privilege in the database.
##################################################################
##################################################################

which=/usr/bin/which
#
# Set these variables if `which` does not exist on your machine
psql=/usr/bin/psql
dbcreate=/usr/bin/createdb

#
if [ -e $which ]
then
   psql=`$which psql`
   dbcreate=`$which createdb`
 
   dbname=$1
   dbuser=$2
  
   tempfile='tempDDL'
else
   echo "Cannot find program \"which\". Exiting"
   exit 1
fi

ddl_files=(users.ddl dives.ddl dive_data.ddl auth_access.ddl posts.ddl posts_text.ddl sessions.ddl rdp1.ddl rdp2.ddl rdp3.ddl functions.ddl)
touch $tempfile

for i in ${ddl_files[@]}
do
   cat ddl/$i >> $tempfile
done
  
echo -n "PostgreSQL superuser "
createdb -U postgres $dbname
echo -n "PostgreSQL superuser "
createuser -D -A -U postgres $dbuser
createlang -d template1 -U postgres plpgsql
psql -U postgres -d template1 <<EOF
  REVOKE ALL ON SCHEMA public FROM PUBLIC CASCADE;
  GRANT ALL ON SCHEMA public TO PUBLIC;
  GRANT USAGE ON SCHEMA public TO $dbuser;
  \c $dbname
  \i $tempfile;
  \i privs.sql;
EOF

echo -n "PostgreSQL standard user "
psql -U $dbuser -d $dbname <<EOF
\copy rdp1 from rdp1.tab
\copy rdp2 from rdp2.tab
\copy rdp3 from rdp3.tab
EOF

rm -f $tempfile
exit 0;
