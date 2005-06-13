--#########################################################################
--   Divelog PostgreSQL database functions.
--             $svn:author$
--       $divelog:copyright$
--            $svn:date$
/* $svn:log$
*/
--#########################################################################

--/////////////////////////////////////////////////////////////////////////
--   FUNCTION: sel_has_previous_dives
--   Input:
--   Output:
--   Returns:
--
--/////////////////////////////////////////////////////////////////////////

CREATE OR REPLACE FUNCTION sel_has_previous_dives(INT,TIMESTAMP) RETURNS boolean AS '
DECLARE
   uid ALIAS FOR $1;
   searchtime ALIAS FOR $2;
BEGIN
   PERFORM diveid FROM dives WHERE userid=uid AND time_out BETWEEN (searchtime - INTERVAL''6 Hour'')
   		AND searchtime;
   IF FOUND THEN
      RETURN true;
   ELSE
      RETURN false;
   END IF;
END;
' LANGUAGE plpgsql;

