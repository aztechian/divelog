CREATE OR REPLACE FUNCTION sel_has_previous_dives(INT,TIMESTAMP) RETURNS boolean AS '
DECLARE
   uid ALIAS FOR $1;
   searchtime ALIAS FOR $2;
BEGIN
   SELECT diveid FROM dives WHERE userid=uid AND time_out > searchtime - INTERVAL'6 Hour';
   IF FOUND THEN
      RETURN true;
   ELSE
      RETURN false;
   END IF;
END;
' LANGUAGE plpgsql;

