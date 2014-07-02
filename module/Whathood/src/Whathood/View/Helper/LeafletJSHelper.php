<?php
namespace Application\View\Helper;
/**
 * Description of LeafletJSHelper
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class LeafletJSHelper extends \Zend\View\Helper\AbstractHelper {
    
    public function __invoke() {
        
        return '<link rel="stylesheet" href="/css/leaflet.css" />
<!--[if lte IE 8]>
    <link rel="stylesheet" href="/css/leaflet.ie.css" />
<![endif]-->

<script src="/js/leaflet-src.js"></script>

<script src="/js/leaflet.draw.js"></script>

	<script src="/js/src/edit/handler/Edit.Poly.js"></script>
	<script src="/js/src/edit/handler/Edit.SimpleShape.js"></script>
	<script src="/js/src/edit/handler/Edit.Circle.js"></script>
	<script src="/js/src/edit/handler/Edit.Rectangle.js"></script>

	<script src="/js/src/draw/handler/Draw.Feature.js"></script>
	<script src="/js/src/draw/handler/Draw.Polyline.js"></script>
	<script src="/js/src/draw/handler/Draw.Polygon.js"></script>
	<script src="/js/src/draw/handler/Draw.SimpleShape.js"></script>
	<script src="/js/src/draw/handler/Draw.Rectangle.js"></script>
	<script src="/js/src/draw/handler/Draw.Circle.js"></script>
	<script src="/js/src/draw/handler/Draw.Marker.js"></script>

	<script src="/js/src/ext/LatLngUtil.js"></script>
	<script src="/js/src/ext/LineUtil.Intersect.js"></script>
	<script src="/js/src/ext/Polygon.Intersect.js"></script>
	<script src="/js/src/ext/Polyline.Intersect.js"></script>

	<script src="/js/src/Control.Draw.js"></script>
	<script src="/js/src/Tooltip.js"></script>
	<script src="/js/src/Toolbar.js"></script>

	<script src="/js/src/draw/DrawToolbar.js"></script>
	<script src="/js/src/edit/EditToolbar.js"></script>
	<script src="/js/src/edit/handler/EditToolbar.Edit.js"></script>
	<script src="/js/src/edit/handler/EditToolbar.Delete.js"></script>';
    }
    
}

?>
