DROP FUNCTION IF EXISTS whathood.merge_user_polygons( neighborhood_name text, test_points geometry[] );

DROP TYPE IF EXISTS merge_user_polygon_res_holder;
CREATE TYPE merge_user_polygon_res_holder AS ( p geometry, num1 int);

CREATE OR REPLACE FUNCTION  whathood.merge_user_polygons( 
  neighborhood_name text, 
  test_points geometry[]
)
RETURNS SETOF merge_user_polygon_res_holder
AS
$$
  DECLARE
    tp geometry;
    r merge_user_polygon_res_holder%rowtype;
  BEGIN
  
    FOREACH tp IN ARRAY test_points LOOP
      --FOR r IN SELECT tp as test_point, COUNT(*) as count1, 345::int as count2 FROM user_polygon WHERE ST_Contains(polygon,tp) = 'true' LOOP
      FOR r IN SELECT ST_Point(1,1), 12 as num1 LOOP
         RETURN NEXT r;
      END LOOP;
    END LOOP;

    RETURN;
  END;
$$
LANGUAGE plpgsql;
