var Profile = function() {

    var dashboardMainChart = null;

    return {

        //main function
        init: function() {
        
            Profile.initMiniCharts();
        },

        initMiniCharts: function() {

            // IE8 Fix: function.bind polyfill
            if (App.isIE8() && !Function.prototype.bind) {
                Function.prototype.bind = function(oThis) {
                    if (typeof this !== "function") {
                        // closest thing possible to the ECMAScript 5 internal IsCallable function
                        throw new TypeError("Function.prototype.bind - what is trying to be bound is not callable");
                    }

                    var aArgs = Array.prototype.slice.call(arguments, 1),
                        fToBind = this,
                        fNOP = function() {},
                        fBound = function() {
                            return fToBind.apply(this instanceof fNOP && oThis ? this : oThis,
                                aArgs.concat(Array.prototype.slice.call(arguments)));
                        };

                    fNOP.prototype = this.prototype;
                    fBound.prototype = new fNOP();

                    return fBound;
                };
            }
        }

    };

}();

if (App.isAngularJsApp() === false) { 
    jQuery(document).ready(function() {
        Profile.init();
    });
}