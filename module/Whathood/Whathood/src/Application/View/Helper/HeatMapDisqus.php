<?php
namespace Application\View\Helper;
/**
 * Description of NeighborhoodPolygonDisqus
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class HeatMapDisqus extends \Zend\View\Helper\AbstractHelper {
    
    public function __invoke( $regionName,$neighborhoodName ) {
        $html=<<<EOF
    <div id="disqus_thread"></div>
    <script type="text/javascript">
        /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
        var disqus_shortname = 'whathood'; // required: replace example with your forum shortname
        var disqus_url = 'http://whathood.in/$regionName/$neighborhoodName';
        /* * * DON'T EDIT BELOW THIS LINE * * */
        (function() {
            var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
            dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
        })();
    </script>
    <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
    <a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>
EOF;
    
        return $html;
    }
    
    protected $view = null;
    
    public function setView( \Zend\View\Renderer\RendererInterface $view)
    {
        $this->view = $view;
    }
    
    public function getView() {
        return $this->view;
    }
}

?>
