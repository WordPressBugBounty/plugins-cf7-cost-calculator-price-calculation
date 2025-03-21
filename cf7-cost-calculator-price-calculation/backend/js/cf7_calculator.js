(function($) {
    "use strict";
    $( document ).ready( function () { 
        $("body").on("change",".calculatedformat_enable",function(e){
          if ($(this).is(':checked')) { 
            $(".calculatedformat").removeClass("hidden");
          }else{
            $(".calculatedformat").addClass("hidden");
          }
        })
        $("body").on("focusout keyup",".calculatedformat_data",function(e){
            var value = $(this).val();
            if(value != ""){
              value = value.replaceAll(" ", "space");
              value = value.replaceAll("€", "EUR");
              value = value.replaceAll("zł", "PLN");
              value = value.replaceAll(",", "comma");
              $(this).val(value);
            }
        })
    })
    setTimeout(() => {
      if (typeof contact_form_7_calculator_name !== 'undefined' && contact_form_7_calculator_name !== null) {
         var tributeAttributes = {
            autocompleteMode: true,
            noMatchTemplate: "",
            values: contact_form_7_calculator_name,
            selectTemplate: function(item) {
              if (typeof item === "undefined") return null;
              if (this.range.isContentEditable(this.current.element)) {
                return (
                  '<span contenteditable="false"><a>' +
                  item.original.key +
                  "</a></span>"
                );
              }
              return item.original.value;
            },
            menuItemTemplate: function(item) {
              return item.string;
            }
          };
          var tributeAutocompleteTestArea = new Tribute(
            Object.assign(
              {
                menuContainer: document.getElementById("autocomplete-textarea-container"),
                replaceTextSuffix: "",
              },
              tributeAttributes
            )
          );
          tributeAutocompleteTestArea.attach(
            document.getElementById("autocomplete-textarea")
          );
        }
      },1000);
})(jQuery);