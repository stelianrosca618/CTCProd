  var table_freights = $(".table-freights");
  if (table_freights.length) {
    var FreightsServerParams = {},
      Freights_Filters;
    Freights_Filters = $("._hidden_inputs._filters._freights_filters input");
    $.each(Freights_Filters, function () {
      FreightsServerParams[$(this).attr("name")] =
        '[name="' + $(this).attr("name") + '"]';
    });

    // Freights not sortable
    var freightsTableNotSortable = [0]; // bulk actions
    var freightsTableURL = admin_url + "freights/table";

    if ($("body").hasClass("freights-page")) {
      freightsTableURL += "?bulk_actions=true";
    }

    _table_api = initDataTable(
      table_freights,
      freightsTableURL,
      freightsTableNotSortable,
      freightsTableNotSortable,
      FreightsServerParams
    );
  }

  // New freight function, various actions performed
  function new_freight(url, timer_id) {
    url = typeof url != "undefined" ? url : admin_url + "freights/freight";    

    var $freightEditModal = $("#_freight_modal");
    if ($freightEditModal.is(":visible")) {
      $freightEditModal.modal("hide");
    }

    requestGet(url)
      .done(function (response) {
        $("#_freight").html(response);
        $("body").find("#_freight_modal").modal({
          show: true,
          backdrop: "static",
        });
      })
      .fail(function (error) {
        alert_float("danger", error.responseText);
      });
  }

  // Go to edit view
  function edit_freight(freight_id) {
    requestGet("freights/freight/" + freight_id).done(function (response) {
      $("#_freight").html(response);
      $("#freight-modal").modal("hide");
      $("body").find("#_freight_modal").modal({
        show: true,
        backdrop: "static",
      });
    });
  }

  // Handles freight add/edit form modal.
  function freight_form_handler(form) {
    // Disable the save button in cases od duplicate clicks
    $("#_freight_modal").find('button[type="submit"]').prop("disabled", true);

    var formURL = form.action;
    var formData = new FormData($(form)[0]);

    $.ajax({
      type: $(form).attr("method"),
      data: formData,
      mimeType: $(form).attr("enctype"),
      contentType: false,
      cache: false,
      processData: false,
      url: formURL,
    })
      .done(function (response) {
        response = JSON.parse(response);
        if (response.success === true || response.success == "true") {
          alert_float("success", response.message);
        }
        $("#_freight_modal").modal("hide");
        reload_freights_tables();
      })
      .fail(function (error) {
        alert_float("danger", JSON.parse(error.responseText));
      });

    return false;
  }

  // Reload all freights possible table where the table data needs to be refreshed after an action is performed on freight.
  function reload_freights_tables() {
    var av_freights_tables = [
      ".table-freights"
    ];
    $.each(av_freights_tables, function (i, selector) {
      if ($.fn.DataTable.isDataTable(selector)) {
        $(selector).DataTable().ajax.reload(null, false);
      }
    });
  }