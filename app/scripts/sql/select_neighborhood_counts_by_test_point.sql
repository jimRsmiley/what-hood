SELECT heat_map_test_point.id,neighborhood.name FROM heat_map_test_point
INNER JOIN neighborhood_polygon ON ST_Contains( neighborhood_polygon.polygon, heat_map_test_point.point ) = 't'
INNER JOIN neighborhood ON neighborhood_polygon.neighborhood_id = neighborhood.id
WHERE 
    heat_map_test_point.set_num = 2
ORDER BY
    neighborhood.name
;
