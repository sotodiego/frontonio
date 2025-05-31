jQuery(document).ready(function($) {
    const menuToggle = $('.hamburger-toggle');
    const menuContainer = $('.hamburger-container');
    const menu_movil = $('.menu_movil');
    const backButton = $('.menu_movil .back-arrow');
    const closeButton = $('.menu_movil .menu-close');
    const menuTitle = $('.menu_movil .menu-title');
    const menuContent = $('.menu_movil .menu-content');
    let menuStack = [];
    let isAnimating = false;
    const topLevelMenu = $('.sp-hamburger-menu').first();

    const overlay_cart = menu_movil.find(".overlay");

    overlay_cart.on('click', function () {
        closeButton.trigger("click")
    });

    menuToggle.on('click', function() {
        menu_movil.toggleClass("open");
        showMenu(topLevelMenu, null);
        menuStack = [];
        menuTitle.text("Menú");
    });

    closeButton.on('click', function() {
        menu_movil.toggleClass("open");
        showMenu(topLevelMenu, null);
        menuStack = [];
        menuTitle.text("Menú");
    });

    backButton.on('click', function() {
        if (isAnimating) {
            return;
        }

        if (menuStack.length > 0) {
            const previousMenu = menuStack.pop();
            showMenu(previousMenu, 'back');
        }

        if (menuStack.length > 0) {
            menuTitle.text(backButton.data('currentTitle'));
        }
        else{
            menuTitle.text("Menú");
        }

    });

    function showMenu(newMenu, direction) {
        if (isAnimating) {
            return;
        }

        isAnimating = true;

        const currentMenu = menuContent.find('.sp-hamburger-menu').first();

        const clonedMenu = $('<ul>').addClass('sp-hamburger-menu').append(newMenu.children().clone());

        clonedMenu.find('li').has('ul').each(function() {
            const submenuIndicator = $('<span class="submenu-indicator"><svg xmlns="http://www.w3.org/2000/svg" height="32px" viewBox="0 -960 960 960" width="32px" fill="#fff"><path d="m480-320 160-160-160-160-56 56 64 64H320v80h168l-64 64 56 56Zm0 240q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/></svg></span>');
            $(this).find('.submenu-indicator').remove();
            $(this).children('a').append(submenuIndicator);
        });

        if (direction === 'forward') {
            clonedMenu.addClass('slide-in-from-right');
        } else if (direction === 'back') {
            clonedMenu.addClass('slide-in-from-left');
        } else {
            clonedMenu.addClass('current-menu');
        }

        menuContent.append(clonedMenu);

        clonedMenu[0].offsetHeight; 

        if (currentMenu.length) {
            if (direction === 'forward') {
                currentMenu.addClass('slide-out-to-left');
            } else if (direction === 'back') {
                currentMenu.addClass('slide-out-to-right');
            } else {
                currentMenu.remove();
            }
            currentMenu.removeClass('current-menu');
        }

        clonedMenu.removeClass('slide-in-from-right slide-in-from-left');
        clonedMenu.addClass('current-menu');

        updateBackButton();

        setTimeout(function() {
            if (currentMenu.length) {
                currentMenu.remove();
            }
            isAnimating = false;
        }, 300); 
    }

    function updateBackButton() {
        if (menuStack.length > 0) {
            backButton.show();
        } else {
            backButton.hide();
        }
    }

    menuContent.on('click', 'a', function(e) {
        if (isAnimating) {
            return;
        }
        
        const parentLi = $(this).parent('li');
        const submenu = parentLi.children('ul').first();
        const link = $(this);
        const itemTitle = link.text();

        if (submenu.length > 0 && !$(e.target).is("a")) {
            e.preventDefault();
            const currentMenu = menuContent.children('.sp-hamburger-menu').first();

            menuStack.push(currentMenu);

            showMenu(submenu, 'forward');

            backButton.data('currentTitle', menuTitle.text())
            menuTitle.text(itemTitle);

        } else {
            // menuContainer.hide();
        }
    });
});