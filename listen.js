$(document).ready(function () {
  let lastSelectedFormat = null;
  let lastSelectedCountry = null;

  function updateStores() {
    const formato = $("input[name=formato]:checked").val();
    const country = $("input[name=country]:checked").val();

    // Check if both values are set
    if (formato && country) {
      // If the format or country has changed, update the "stores" dropdown
      if (formato !== lastSelectedFormat || country !== lastSelectedCountry) {
        // Send AJAX request for stores
        $.ajax({
          url: "get_store_list.php",
          type: "POST",
          data: { formato: formato, country: country },
          success: function (data) {
            $("select[name=stores]").html(data);
            console.log("AJAX request successful!");

            // Update the stored format and country values
            lastSelectedFormat = formato;
            lastSelectedCountry = country;
          },
          error: function () {
            console.log("AJAX request failed!");
          },
        });
      }
    }
  }

  // When the radio buttons change, send an AJAX request to the server to get the filtered stores
  $("input[type=radio]").on("change.updateStores", updateStores);

  // When the store dropdown changes, send an AJAX request to the server to get the employee list
});
// Add an event listener to the "stores" dropdown

function goBack() {
    window.history.back();
}