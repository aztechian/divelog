--#########################################################################
--   Divelog PostgreSQL database functions.
--             $svn:author$
--       $divelog:copyright$
--            $svn:date$
/* $svn:log$
*/
--#########################################################################


--/////////////////////////////////////////////////////////////////////////
--   FUNCTION: del_dive
--   Input:
--   Output:
--   Returns:
--
--/////////////////////////////////////////////////////////////////////////

CREATE OR REPLACE FUNCTION del_dive(int) RETURNS BOOLEAN AS '
DECLARE
   diveid ALIAS FOR $1;
   retVal BOOL := ''false'';
   
BEGIN

   DELETE FROM dive