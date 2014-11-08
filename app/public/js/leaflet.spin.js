L.SpinMapMixin = {
    spin: function (state) {
        var state = !!state;
        
        var opts = {
  lines: 13, // The number of lines to draw
  length: 32, // The length of each line
  width: 23, // The line thickness
  radius: 49, // The radius of the inner circle
  corners: 1, // Corner roundness (0..1)
  rotate: 0, // The rotation offset
  direction: 1, // 1: clockwise, -1: counterclockwise
  color: '#000', // #rgb or #rrggbb
  speed: 1, // Rounds per second
  trail: 87, // Afterglow percentage
  shadow: true, // Whether to render a shadow
  hwaccel: false, // Whether to use hardware acceleration
  className: 'spinner', // The CSS class to assign to the spinner
  zIndex: 2e9, // The z-index (defaults to 2000000000)
  top: 'auto', // Top position relative to parent in px
  left: 'auto' // Left position relative to parent in px
};

        if (state) {
            // start spinning !
            if (!this._spinner) {
                this._spinner = new Spinner(opts).spin(this._container);
                this._spinning = 0;
            }
            this._spinning++;
        }
        else {
            this._spinning--;
            if (this._spinning <= 0) {
                // end spinning !
                if (this._spinner) {
                    this._spinner.stop();
                    this._spinner = null;
                }
            }
        }
    }
};

L.Map.include(L.SpinMapMixin);

L.Map.addInitHook(function () {
    this.on('layeradd', function (e) {
        // If added layer is currently loading, spin !
        if (e.layer.loading) this.spin(true);
        if (typeof e.layer.on != 'function') return;
        e.layer.on('data:loading', function () { this.spin(true) }, this);
        e.layer.on('data:loaded',  function () { this.spin(false) }, this);
    }, this);
    this.on('layerremove', function (e) {
        // Clean-up
        if (e.layer.loading) this.spin(false);
        if (typeof e.layer.on != 'function') return;
        e.layer.off('data:loaded');
        e.layer.off('data:loading');
    }, this);
});