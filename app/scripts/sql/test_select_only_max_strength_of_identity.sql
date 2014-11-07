SELECT * FROM my_temp_table x
JOIN ( SELECT test_point_id, MAX(strength_of_identity) as strength_of_identity FROM my_temp_table GROUP BY test_point_id ) y
ON x.test_point_id = y.test_point_id AND x.strength_of_identity = y.strength_of_identity
JOIN test_point ON x.test_point_id = test_point.id
ORDER BY x.strength_of_identity ASC