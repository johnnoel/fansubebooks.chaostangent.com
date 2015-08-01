/**
 * Menu expand / collapse
 *
 * @author John Noel <john.noel@chaostangent.com>
 * @package FansubEbooks
 */

document.addEventListener('DOMContentLoaded', function(e) {
    var menu = document.getElementById('menu');
    menu.addEventListener('click', function(e) {
        // check if the menu is on or off
        var navbar = menu.parentNode;
        while (navbar.className.indexOf('navbar') === -1) {
            if (!navbar.parentNode) {
                break;
            }

            navbar = navbar.parentNode;
        }

        var showingMenu = false;
        if (navbar.className.indexOf('navbar-on') !== -1) {
            showingMenu = true;
        }

        // if on, hide menu, remove class
        if (showingMenu) {
            navbar.className = navbar.className.replace('navbar-on', '');
        } else { // if off, show menu, add class
            navbar.className += ' navbar-on';
        }
    });
});
