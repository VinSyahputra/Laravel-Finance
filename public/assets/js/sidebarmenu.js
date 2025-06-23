function initSidebarActiveLinks() {
    "use strict";

    const url = window.location.href;
    const path = url.replace(window.location.origin + '/', '');

    // Highlight current sidebar item
    const element = $("ul#sidebarnav a").filter(function () {
        return this.href === url || this.href === path;
    });

    // Clear previous states
    $("ul#sidebarnav a").removeClass("active");
    $("ul#sidebarnav li").removeClass("selected in");

    // Re-apply based on current link
    element.parentsUntil(".sidebar-nav").each(function () {
        if ($(this).is("li") && $(this).children("a").length !== 0) {
            $(this).children("a").addClass("active");
            $(this).parent("ul#sidebarnav").length === 0 ?
                $(this).addClass("active") :
                $(this).addClass("selected");
        } else if (!$(this).is("ul") && $(this).children("a").length === 0) {
            $(this).addClass("selected");
        } else if ($(this).is("ul")) {
            $(this).addClass("in");
        }
    });

    element.addClass("active");

    // Clean up previous bindings
    $("#sidebarnav a").off("click").on("click", function (e) {

        if (!$(this).hasClass("active")) {
            // Collapse all open
            $("ul", $(this).parents("ul:first")).removeClass("in");
            $("a", $(this).parents("ul:first")).removeClass("active");

            // Expand new
            $(this).next("ul").addClass("in");
            $(this).addClass("active");
        } else {
            $(this).removeClass("active");
            $(this).parents("ul:first").removeClass("active");
            $(this).next("ul").removeClass("in");
        }
    });

    // Disable default for arrows
    $("#sidebarnav > li > a.has-arrow").off("click").on("click", function (e) {
        e.preventDefault();
    });
}

// Initial load
$(function () {
    initSidebarActiveLinks();
});

// Livewire-friendly re-bind on navigation
document.addEventListener('livewire:navigated', () => {
    initSidebarActiveLinks();
});
