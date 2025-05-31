document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".sp_carrusel").forEach(function (carousel) {
        let dataPerPage = carousel.getAttribute("data-perPage") ? parseInt(carousel.getAttribute("data-perPage")) : 4;
        let dataGap = carousel.getAttribute("data-gap") ? parseInt(carousel.getAttribute("data-gap")) : 20;
        let dataAutoplay = carousel.getAttribute("data-autoplay") === "true"; 

        let dataBreakpoints = carousel.getAttribute("data-breakpoints");
        let breakpoints = dataBreakpoints ? JSON.parse(dataBreakpoints) : {};

        new Splide(carousel, {
            type: "slide",
            perMove: 1,
            perPage: dataPerPage,
            autoplay: dataAutoplay,
            updateOnMove: true,
            clones: 0,
            gap: dataGap,
            pagination: false,
            mediaQuery: "max",
            breakpoints: breakpoints
        }).mount();
    });




    document.querySelectorAll(".sp_carrusel_elementor").forEach(function (carousel) {
        let dataPerPage = carousel.getAttribute("data-perPage") ? parseInt(carousel.getAttribute("data-perPage")) : 5;
        let dataGap = carousel.getAttribute("data-gap") ? parseInt(carousel.getAttribute("data-gap")) : 20;
        let dataAutoplay = carousel.getAttribute("data-autoplay") === "true"; 
    
        let dataBreakpoints = carousel.getAttribute("data-breakpoints");
        let breakpoints = dataBreakpoints ? JSON.parse(dataBreakpoints) : {};
    
        let slides = carousel.querySelectorAll(".sp_diapositiva");
        if (slides.length === 0) {
            console.warn("No se encontraron elementos con la clase .sp_diapositiva dentro de", carousel);
            return;
        }
    
        let track = document.createElement("div");
        track.classList.add("splide__track");
        let list = document.createElement("ul");
        list.classList.add("splide__list");
    
        slides.forEach(slide => {
            let listItem = document.createElement("li");
            listItem.classList.add("splide__slide");
            listItem.appendChild(slide);
            list.appendChild(listItem);
        });
    
        track.appendChild(list);
        carousel.appendChild(track);
    
        carousel.classList.add("splide");
    
        new Splide(carousel, {
            type: "slide",
            perMove: 1,
            perPage: dataPerPage,
            autoplay: dataAutoplay,
            updateOnMove: true,
            clones: 0,
            gap: dataGap,
            pagination: false,
            mediaQuery: "max",
            breakpoints: breakpoints
        }).mount();
    });
});


jQuery(document).ready(function ($) {
    $(document).on("click", ".btn_carrusel", function(){
        let target = $(this).attr("target");
        if(target && $(target).length){
            $(target).trigger("click");
        }
    });
});
