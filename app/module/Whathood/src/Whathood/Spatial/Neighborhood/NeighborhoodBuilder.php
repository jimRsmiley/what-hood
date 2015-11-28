<?php

/**
 * Neighborhood logic for the system
 */
namespace Whathood\Spatial\Neighborhood;

use Whathood\Mapper\MapperTrait;

/**
 * Build neighborhoods for the system
 */
class NeighborhoodBuilder {

    use MapperTrait;

    /**
     * build the neighborhood by name and region
     *
     * @param neighborhoodName [String] the name of the neighborhood
     * @param regionName [String] the name of neighborhoods region
     * @return array \Whathood\Entity\Neighborhood
     */
    public function byName($neighborhoodName, $regionName) {
        return $this->getMapper('neighborhood')->getNeighborhoodByName($neighborhoodName, $regionName);
    }

    /**
     * build neighborhoods based on whether they have heatmaps
     * @return array \Whathood\Entity\Neighborhood
     */
    public function allByHeatmapPriority() {
        $arr = array();
        # get neighborhoods with no heatmaps first
        $neighborhoods = $this->getMapper('heatMapPoint')
            ->neighborhoodsWithNoHeatmapPoints();

        $this->array_add_neighborhoods($arr, $neighborhoods);

        # then get the rest
        $neighborhoods = $this->getMapper('neighborhood')->fetchAll();
        $neighborhoods = $this->getMapper('neighborhood')
            ->sortByOldestBorder($neighborhoods);

        $this->array_add_neighborhoods($arr, $neighborhoods);

        return $arr;
    }

    public function array_add_neighborhoods(array &$arr, array $neighborhoods) {

        foreach ($neighborhoods as $n) {
            $found = 0;
            foreach ($arr as $test) {
                # $n already in $arr
                if ($test->getId() == $n->getId())
                    $found = true;
            }
            if (!$found)
                array_push($arr, $n);
        }
        return $arr;
    }
}
