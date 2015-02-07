CREATE OR REPLACE VIEW latest_neighborhoods AS
    SELECT * from neighborhood_polygon np
    WHERE np.id IN
     (
        SELECT MAX(id) AS id_max FROM neighborhood_polygon GROUP BY neighborhood_id
    )
