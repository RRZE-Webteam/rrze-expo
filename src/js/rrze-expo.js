/**
Shortcode script
*/
jQuery(document).ready(function ($) {
    document.addEventListener('touchstart', handleTouchStart, false);
    document.addEventListener('touchmove', handleTouchMove, false);

    var xDown = null;
    var yDown = null;

    function handleTouchStart(evt) {
        xDown = evt.originalEvent.touches[0].clientX;
        yDown = evt.originalEvent.touches[0].clientY;
    };

    function handleTouchMove(evt) {
        if ( ! xDown || ! yDown ) {
            return;
        }

        var xUp = evt.originalEvent.touches[0].clientX;
        var yUp = evt.originalEvent.touches[0].clientY;

        var xDiff = xDown - xUp;
        var yDiff = yDown - yUp;

        if ( Math.abs( xDiff ) > Math.abs( yDiff ) ) {/*most significant*/
            if ( xDiff > 0 ) {
                /* left swipe */
                $('li.prev-booth').find('a').click();
            } else {
                /* right swipe */
                $('li.next-booth').find('a').click();
            }
        } else {
            if ( yDiff > 0 ) {
                /* up swipe */
                //$('a#scrolldown').click();
            } else {
                /* down swipe */
            }
        }
        /* reset values */
        xDown = null;
        yDown = null;
    };
});
