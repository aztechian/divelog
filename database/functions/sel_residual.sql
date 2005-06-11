CREATE OR REPLACE FUNCTION sel_residual(CHAR(1), INT) RETURNS INTERVAL HOUR to MINUTE AS '
BEGIN
   RETURN MAX(residual) FROM rdp3 WHERE depth >= $2 AND pressure_group = $1;
END;
' LANGUAGE plpgsql;

