1.  first you need to create a grid of test points

INSERT INTO test_point (set_num,point) (
  SELECT 
  150 as set_num,
  (ST_Dump(
    -- 500 turns into  16095 points  in  19,132 ms
    -- 400 turns into  25159 points  in  35,201 ms
    -- 300 turns into  44696 points  in  61,698 ms
    -- 200 turns into 100575 points  in 231,979 ms
    -- 180 turns into 124154 poings  in 355,330 ms
    makegrid_2d(region.border,150)
  )).geom AS point FROM region WHERE region.name = 'Philadelphia'
);

Then you need to 