--#########################################################################
--   Divelog PostgreSQL database functions.
--             $svn:author$
--       $divelog:copyright$
--            $svn:date$
/* $svn:log$
*/
--#########################################################################

--/////////////////////////////////////////////////////////////////////////
--   FUNCTION: sel_dive_data
--   Input:    The integer representing a users' ID.
--   Output:   None.
--   Returns:  A set of records holding all of that users dive meta-data.
--
--/////////////////////////////////////////////////////////////////////////
CREATE OR REPLACE FUNCTION sel_dive_data(INT) RETURNS SETOF dives AS'
DECLARE
   uid ALIAS FOR $1;
   data dives%ROWTYPE;
BEGIN
   FOR data in SELECT * FROM dives WHERE userid=uid ORDER BY time_in LOOP
      return next data;
   END LOOP;
   RETURN;
END;
' LANGUAGE plpgsql;

