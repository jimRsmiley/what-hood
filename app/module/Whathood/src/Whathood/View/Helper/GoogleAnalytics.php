<?php
namespace Whathood\View\Helper;

/**
 * Description of UserRegionUrlHelper
 *
 * @author Jim Smiley twitter:@jimRsmiley
 */
class GoogleAnalytics extends \Zend\View\Helper\AbstractHelper {

    protected $_google_ui;

    public function setGoogleUi($google_ui) {
        $this->_google_ui = $google_ui;
    }

    public function __invoke() {
        $google_ui_code = $this->_google_ui;

        if (!$google_ui_code) return "";

        return "<script>
             (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                       m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

     ga('create', '$google_ui_code', 'auto');
     ga('send', 'pageview');

    </script>";
    }
}

?>
