/*
CREATE FUNCTION auto_increment_dives_pk() RETURNS TRIGGER AS '
  begin
  new.diveid := nextval("dives_pk_sequence");
  return new;
  end;
'
LANGUAGE plpgsql;
*/

CREATE OR REPLACE FUNCTION sel_user_creds(TEXT) RETURNS TEXT AS'
DECLARE 
   usern ALIAS FOR $1;
   data text;
BEGIN
   SELECT INTO data password FROM users WHERE username=usern;
   IF NOT FOUND THEN
      RAISE EXCEPTION ''user % not found'', usern;
   END IF;
   RETURN data;
END;
' LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION sel_dive_data(INT) RETURNS SETOF RECORD AS'
DECLARE
   uid ALIAS FOR $1;
   data record;
BEGIN
   FOR data in SELECT * FROM dives LOOP
      return next data;
   END LOOP;
   RETURN;
END;
' LANGUAGE plpgsql;

