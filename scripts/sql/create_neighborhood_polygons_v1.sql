--
-- using left outer join gets the last heat map created for that neighborhood
--
SELECT * FROM test_point tp1
LEFT OUTER JOIN test_point tp2
    ON ( tp1.strength_of_identity < tp2.strength_of_identity )
;