CREATE FUNCTION auto_increment_dives_pk() RETURNS TRIGGER AS '
  begin
  new.diveid := nextval("dives_pk_sequence");
  return new;
  end;
'
LANGUAGE plpgsql;

