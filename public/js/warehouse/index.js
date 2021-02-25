$(document).ready(() => {
  let WarehouseIndex = {
    init: function () {
      let toggleInputs = $(".js-toggle-input");
      toggleInputs.each(function (index, el) {
        WarehouseIndex.handleToggleInputs(el);
      });
      toggleInputs.change((e) => {
        WarehouseIndex.handleToggleInputs(e.target);
      });

      if ($("#cube").length > 0) {
        $([document.documentElement, document.body]).animate(
          {
            scrollTop: $("#cube").offset().top,
          },
          2000
        );
      }

      WarehouseIndex.handleSliderInputs();
      WarehouseIndex.handleCuberRotation();
    },
    handleCuberRotation: function () {
      $("#cube").css(
        "transform",
        "translateZ(-150px) rotateX(330deg) rotateY(230deg)"
      );

      setTimeout(() => {
        $("#cube").css(
          "transform",
          "rotateX(0deg) rotateY(0deg) rotateZ(0deg) translateZ(-150px)"
        );
      }, 5000);

      $("#cube").hover(() => {
        $("#cube").css(
          "transform",
          "translateZ(-150px) rotateX(330deg) rotateY(230deg)"
        );
      });

      $("#cube").mouseleave(() => {
        $("#cube").css(
          "transform",
          "rotateX(0deg) rotateY(0deg) rotateZ(0deg) translateZ(-150px)"
        );
      });
    },
    handleSliderInputs: function () {
      let sliders = $('input[type="range"]');

      sliders.on("change", (e) => {
        let slider = $(e.target);

        let label = $("label[for='" + $(slider).attr("id") + "']");

        label.find(".slider-value").text(slider.val());
      });
    },
    handleToggleInputs: function (el) {
      let element = $(el);
      let targetClass = element.data("toggle-target");
      let targetInput = $("." + targetClass);

      if (element.is(":checked")) {
        targetInput.show();
      } else {
        targetInput.hide();
      }
    },
  };

  WarehouseIndex.init();
});
