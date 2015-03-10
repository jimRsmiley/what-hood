--
-- name: delete_user_polygon( up_id )
--
DROP FUNCTION IF EXISTS delete_user_polygon( _up_id integer );

CREATE OR REPLACE FUNCTION delete_user_polygon( _up_id integer )
RETURNS VOID
AS
$BODY$
BEGIN

  IF ( SELECT 1 FROM user_polygon WHERE id = _up_id LIMIT 1 ) THEN
    DELETE FROM trans_tp_up WHERE user_polygon_id = _up_id;
    DELETE FROM trans_np_up WHERE up_id = _up_id;
    DELETE FROM user_polygon WHERE id = _up_id;

    RAISE NOTICE 'you must re-run the process to build neighborhood_polygons';
  ELSE
    RAISE EXCEPTION 'user_polygon with id % does not exist',test_up_id;
  END IF;

END;
$BODY$
LANGUAGE plpgsql;
