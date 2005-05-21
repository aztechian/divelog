CREATE OR REPLACE FUNCTION sel_curr_pgroup(INT,TIMESTAMP) RETURNS CHAR(1) AS '
DECLARE
   uid ALIAS FOR $1;
   searchtime ALIAS FOR $2;

   has_dives boolean;
   dive_recs dives%ROWTYPE;
   dive_list record;
BEGIN
   SELECT INTO has_dives sel_has_previous_dives(uid,searchtime);
   IF NOT has_dives THEN
      RETURN 'A';
   END IF;


   FOR dive_recs IN SELECT * FROM dives WHERE userid=uid AND time_out > searchtime - INTERVAL'6 Hour' ORDER BY time_out ASC LOOP


