#!/bin/sh

work_dir=`pwd`

file_list="common.php config.php createuser.php images index.php session.php style.css test.php diveedit.php"

for i in $file_list
do
   if [ ! -e ~/public_html/$i ]
   then
      echo "making link: ln -s $work_dir/$i ~/public_html/$i"
      ln -s $work_dir/$i ~/public_html/$i
   fi
done

echo "Done."
exit 0;

