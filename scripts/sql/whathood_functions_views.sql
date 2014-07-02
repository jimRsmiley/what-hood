--
--
--
CREATE OR REPLACE VIEW show_test_point_info AS
    SELECT set_num as set_number,COUNT(*) as total_test_points FROM test_point GROUP BY set_num ORDER BY set_num ASC;