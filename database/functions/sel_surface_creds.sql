--#########################################################################
--   Divelog PostgreSQL database functions.
--             $svn:author$
--       $divelog:copyright$
--            $svn:date$
/* $svn:log$
*/
--#########################################################################

--/////////////////////////////////////////////////////////////////////////
--   FUNCTION: sel_surface_creds
--   Input:
--   Output:
--   Returns:
--
--/////////////////////////////////////////////////////////////////////////

CREATE OR REPLACE FUNCTION sel_surface_creds(CHAR(1),INTERVAL HOUR to MINUTE) RETURNS CHAR(1) AS '
DECLARE
   start_pg ALIAS FOR $1;
   time ALIAS FOR $2;
BEGIN
   RETURN final_press_group 
          FROM rdp2 
          WHERE init_press_group = start_pg AND time BETWEEN min_time AND max_time;
          
END;
' LANGUAGE plpgsql;