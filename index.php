<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD with Slider</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&family=Titillium+Web:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <!-- Bootstrap -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css"> -->
    
    <!-- jQuery (Only Once) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Slick Slider CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">
    
    <!-- Slick Slider JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
 
</head>
<body class="brand-secondary-bg brand-white">
<div class="container">
    <div class="mainwrapper">
    <h1 class="font-titi font-light font-size-jumbo font-normal">DelphianLogic in Action</h1>
    <p class="opensans font-light pad-y-3">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean commodo</p>
    <div class="rows">
        <!-- Tabs -->
        <div class="col-md-3 gray-white-bg ">
            <div id="tabs" class="tab-container gap-4"></div>
        </div>

        <!-- Slider -->
        <div class="col-md-4 brand-fourth-bg">
            <div id="slider-container"></div>   
        </div>

        <!-- Image -->
        <div class="col-md-4">
            <img id="slideImage" src="" class="img-fluid" alt="Slide Image">
        </div>
    </div>
    </div>
</div>

<script>
$(document).ready(function () {
    let currentTab = "";
    let currentSlides = [];
    let isMobileView = window.innerWidth <= 1024;

    function initializeLayout() {
    isMobileView = window.innerWidth <= 1024;
    $("#tabs").empty(); // Clear tabs/accordion
    $("#slider-container").html(""); // Clear slider

    $.ajax({
        url: "fetch.php",
        type: "GET",
        dataType: "json",
        success: function (response) {
            if (response.length === 0) {
                console.error("No data received");
                return;
            }

            response.forEach((section, index) => {
                let isActive = index === 0 ? "active-tab" : "";
                let iconUrl = section.icon_url ? section.icon_url : "default-icon.png";

                let tabContent = isMobileView
                    ? `<div class="accordion-item">
                        <button class="tab-btn brand-white-bg font-size-big gap-3 font-semibold brand-secondary br-3 pad-y-4 pad-x-5 ${isActive}" data-tab="${section.tab_name}">
                            <img src="${iconUrl}" class="tab-icon"> ${section.tab_name}
                        </button>
                        <div class="accordion-content" style="${index === 0 ? 'display: block;' : 'display: none;'}">
                            <div class="slider-container"></div>
                        </div>
                    </div>`
                    : `<button class="tab-btn brand-white-bg font-size-big gap-3 font-semibold brand-secondary br-3 pad-y-4 pad-x-5 ${isActive}" data-tab="${section.tab_name}">
                        <img src="${iconUrl}" class="tab-icon"> ${section.tab_name}
                    </button>`;

                $("#tabs").append(tabContent);
            });

            let firstTab = response[0];
            if (firstTab) {
                currentTab = firstTab.tab_name;
                currentSlides = firstTab.slides;
                loadSlides(currentSlides, $("#slider-container"));

                if (isMobileView) {
                    let firstAccordion = $(".accordion-item .accordion-content").first();
                    firstAccordion.find(".slider-container").html(""); // Clear and load slides
                    loadSlides(currentSlides, firstAccordion.find(".slider-container"));

                    // Ensure the first tab's image loads
                    if (currentSlides.length > 0) {
                        $("#slideImage").attr("src", currentSlides[0].image_url || "default-image.jpg");
                    }
                } else {
                    if (currentSlides.length > 0) {
                        $("#slideImage").attr("src", currentSlides[0].image_url || "default-image.jpg");
                    }
                }
            }

            $(".tab-btn").click(function () {
    let tabName = $(this).data("tab");
    let selectedSection = response.find((s) => s.tab_name === tabName);
    let accordionContent = $(this).next(".accordion-content");

    if (selectedSection) {
        currentTab = tabName;
        currentSlides = selectedSection.slides;

        if (isMobileView) {
            if ($(this).hasClass("active-tab")) {
                // **If the same tab is clicked twice, close it**
                $(this).removeClass("active-tab");
                accordionContent.slideUp();
                return;
            }

            $(".tab-btn").removeClass("active-tab");
            $(this).addClass("active-tab");

            $(".accordion-content").slideUp(); // Close other accordions
            accordionContent.slideDown(); // Open the clicked one

            let sliderContainer = accordionContent.find(".slider-container");
            sliderContainer.html(""); // Clear old slides before appending new ones
            loadSlides(selectedSection.slides, sliderContainer);

            if (currentSlides.length > 0) {
                $("#slideImage").attr("src", currentSlides[0].image_url || "default-image.jpg");
            }
        } else {
            $(".tab-btn").removeClass("active-tab");
            $(this).addClass("active-tab");

            $("#slider-container").html(""); // Clear old slides before appending new ones
            loadSlides(selectedSection.slides, $("#slider-container"));

            if (currentSlides.length > 0) {
                $("#slideImage").attr("src", currentSlides[0].image_url || "default-image.jpg");
            }
        }
    }
});

        },
        error: function (xhr, status, error) {
            console.error("Error fetching data: ", error);
        }
    });
}



    function loadSlides(slides, container) {
        if (!container.length) {
            console.warn("Slider container not found!");
            return;
        }

        // Destroy previous Slick instance before reinitializing
        if (container.hasClass("slick-initialized")) {
            container.slick("unslick");
        }

        container.empty(); // Ensure old slides are cleared before loading new ones

        if (slides.length > 0) {
            slides.forEach((slide) => {
                container.append(`
                    <div class="slide">
                         <p class="font-titi title font-size-small">${slide.title}</p>
                        <h3 class="font-size-largest font-semibold">${slide.subtitle}</h3>
                        <button class="btn btn-primary">${slide.button_text}</button>
                    </div>
                `);
            });

            container.slick({
                infinite: false,
                dots: true,
                slidesToShow: 1,
                slidesToScroll: 1,
                prevArrow: false,
                nextArrow: false,
            });

        } else {
            container.html("<p>No slides available</p>");
        }
    }

    initializeLayout();

    $(window).resize(function () {
        if ((window.innerWidth <= 768 && !isMobileView) || (window.innerWidth > 768 && isMobileView)) {
            initializeLayout();
        }
    });
});




</script>
</body>
</html>
