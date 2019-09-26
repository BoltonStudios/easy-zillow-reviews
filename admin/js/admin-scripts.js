// JavaScript Document
function ezrwpAdminScripts(){
    // Toggle grid columns field based on layout selected
    var ezrwpLayout = document.getElementsByClassName('ezrwp_layout')[0];
    var ezrwpCols = document.getElementsByClassName('ezrwp_cols')[0];
    ezrwpCols.disabled = ezrwpLayout.value == 'list' ? true : false;
    ezrwpLayout.addEventListener('change', function () {
        ezrwpCols.disabled = this.value == 'list' ? true : false;
    });

    // Toggle disclaimer notice based on disclaimer setting
    var style = document.getElementById('ezrwp_disclaimer').value == 1 ? 'block' : 'none';
    document.getElementById('disclaimer-warning').style.display = style;
    document.getElementById('ezrwp_disclaimer').addEventListener('change', function () {
        var style = this.value == 1 ? 'block' : 'none';
        document.getElementById('disclaimer-warning').style.display = style;
    });   
}
(function($) {
    $(document).ready(function() {
        ezrwpAdminScripts();
    });
})(jQuery);