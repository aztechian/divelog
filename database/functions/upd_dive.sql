--#########################################################################
--   Divelog PostgreSQL database functions.
--             $svn:author$
--       $divelog:copyright$
--            $svn:date$
/* $svn:log$
*/
--#########################################################################

--/////////////////////////////////////////////////////////////////////////
--   FUNCTION: upd_dive
--   Input:    The dive id to modify, an array holding the names of fields
--             to update, an array with values to update corresponding to
--             the fields.
--   Output:   None.
--   Returns:  Boolean value indicating success or failure of the update.
--
--/////////////////////////////////////////////////////////////////////////

CREATE OR REPLACE FUNCTION upd_dive(INT,ANYARRAY,ANYARRAY) RETURNS boolean AS'
DECLARE
   i_diveid ALIAS FOR $1;
   field_array ALIAS FOR $2;
   value_array ALIAS FOR $3;
   
   new dives%ROWTYPE;
   update_pg BOOLEAN := false;
   status_val BOOLEAN := false;
   query_str text := ''UPDATE dives SET '';

BEGIN
   IF array_upper(field_array,1) <> array_upper(value_array,1) THEN
      RAISE EXCEPTION ''The given arrays do not have the same number of items. Cannot update.'';
      RETURN false;
   END IF;

   FOR i in array_lower(field_array,1)..array_upper(field_array,1) LOOP
      IF field_array[i] = ''depth'' OR field_array[i] = ''time_in'' OR field_array[i] = ''time_out'' THEN
         update_pg := true;
      END IF;
      query_str := query_str 
      			|| quote_ident(field_array[i])
      			|| ''=''
      			|| quote_literal(value_array[i]);
      IF i <> array_upper(field_array,1) THEN
         query_str := query_str || '','';
      END IF;
   END LOOP;

   query_str := query_str || '' WHERE diveid = '' || quote_literal(i_diveid);
   EXECUTE query_str;
   IF FOUND THEN
      status_val := true;
   END IF;
   
   IF update_pg = true THEN
      SELECT INTO new * FROM dives WHERE diveid = i_diveid;
      pg_in := sel_curr_pgroup(new.userid,new.time_in);
      pg_out := sel_post_pgroup(pg_in, (new.time_out-new.time_in)::INTERVAL HOUR to MINUTE, depth_var);
      
      EXECUTE ''UPDATE dives SET press_group_in=''
              || quote_literal(pg_in)
              || '', ''
              || ''press_group_out=''
              || quote_literal(pg_out)
              || '' WHERE diveid=''
              || quote_literal(i_diveid);
      IF FOUND THEN
         status_val := true;
      END IF;
   END IF;
   RETURN status_val;
END;
' LANGUAGE plpgsql;