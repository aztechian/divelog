CREATE OR REPLACE FUNCTION sel_residual(CHAR(1), INT) RETURNS INTERVAL HOUR to MINUTE AS '
BEGIN
   IF $1 = '''' OR $1 IS NULL THEN
   	  RETURN ''0:00''::INTERVAL HOUR to MINUTE;
   END IF;
   RETURN MAX(residual) FROM rdp3 WHERE depth >= $2 AND pressure_group = $1;
END;
' LANGUAGE plpgsql;

